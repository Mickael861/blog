<?php
namespace App\Controller\Admin;

use App\Controller\Controller;
use App\Model\PostsModel;
use App\Model\UserModel;
use App\Utils\Utils;

class PostsController extends Controller
{

    /**
     * @var string
     */
    protected $title = 'Liste des articles';

    /**
     * @var string
     */
    protected $view = 'posts';

    /**
     * @var bool
     */
    protected $admin_access = true;

    /**
     * @var string
     */
    private $folder = 'admin';

    /**
     * view of action
     *
     * @param array Datas POST|GET
     * @return void
     */
    public function postsAction(array $datas = array()): void
    {
        $this->init($datas);
        
        $this->page = empty($this->datas_match['page']) ? 1 : (int) $this->datas_match['page'];
        $this->datas['page'] = $this->page;

        $userModel = new UserModel;
        $modelPosts = new PostsModel;
        
        $posts = $modelPosts->fetchAll(true, 'post_id', $this->page, array(), 'DESC');
        if (!empty($posts)) {
            $this->nbrs_page = $modelPosts->getNbrsPage();
            $this->disabledPagination();
            $this->datas['nbrs_page'] = $this->nbrs_page;

            if (!empty($this->datas_get['publish'])) {
                $post_id = $this->datas_get['publish'];
                $post_publish = $modelPosts->fetchId($post_id);
                
                $is_publish = $post_publish['statut'] === 'publier' ? 'en_attente' : 'publier';

                $datas_publish = array(
                    'statut' => $is_publish
                );

                $modelPosts->save($datas_publish, $post_id);
                header('Location: /admin/posts/' . $this->page);
                exit();
            }

            foreach ($posts as &$post) {
                $itemUser = $userModel->fetchId($post->author_id);
                $post->author_name = $itemUser['pseudo'];
                $post->title = substr_replace($post->title, '...', 30);
                $post->chapo = substr_replace($post->chapo, '...', 30);
                $post->date_upd = (new Utils())::dbToDate($post->date_upd);

                $post->is_publish = $post->statut === 'publier' ? '1' : '0';
            }
            $this->datas['posts'] = $posts;

            if (!empty($this->datas_get['delete'])) {
                $modelPosts->delete($this->datas_get['delete']);
                $_SESSION['success'] = 'Article supprimé avec succés';
                header('Location: /admin/posts/' . $this->page);
                exit();
            }

            if (!empty($modelPosts->getErrors())) {
                $this->datas['errors'] = $modelPosts->getErrors()['page'];
            }
        } else {
            $this->datas['errors'] = 'Aucun article disponible';
        }
        
        echo $this->viewsRender($this->view, $this->datas, $this->folder);
    }
}
