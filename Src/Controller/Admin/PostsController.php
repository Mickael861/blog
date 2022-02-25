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
        
        $this->addDatasPages();

        $this->postsManagement();
        
        echo $this->viewsRender($this->view, $this->datas, $this->folder);
    }
    
    /**
     * Posts management
     *
     * @return void
     */
    private function postsManagement(): void
    {
        $this->modelPosts = new PostsModel;
        
        $posts = $this->modelPosts->fetchAll(true, 'post_id', $this->page, array(), 'DESC');
        if (!empty($posts)) {
            $this->addDatasNbrsPages($this->modelPosts);

            $this->addStatutesChange();

            $this->addDatasPosts($posts);

            $this->deletePost();

            if (!empty($this->modelPosts->getErrors())) {
                $this->datas['errors'] = $this->modelPosts->getErrors()['page'];
            }
        } else {
            $this->datas['errors'] = 'Aucun article disponible';
        }
    }
    
    /**
     * Delete post
     *
     * @return void
     */
    private function deletePost(): void
    {
        if (!empty($this->datas_get['delete'])) {
            $this->modelPosts->delete($this->datas_get['delete']);
            $_SESSION['success'] = 'Article supprimé avec succés';
            header('Location: /admin/posts/' . $this->page);
            exit();
        }
    }
    
    /**
     * Change the status of the post
     *
     * @return void
     */
    private function addStatutesChange(): void
    {
        if (!empty($this->datas_get['publish'])) {
            $post_id = $this->datas_get['publish'];
            $post_publish = $this->modelPosts->fetchId($post_id);
            
            $is_publish = $post_publish['statut'] === 'publier' ? 'en_attente' : 'publier';

            $datas_publish = array(
                'statut' => $is_publish
            );

            $this->modelPosts->save($datas_publish, $post_id);
            header('Location: /admin/posts/' . $this->page);
            exit();
        }
    }

    /**
     * Add data to item
     *
     * @param  objet $posts
     * @return void
     */
    private function addDatasPosts($posts): void
    {
        $userModel = new UserModel;

        foreach ($posts as &$post) {
            $itemUser = $userModel->fetchId($post->author_id);
            $post->author_name = $itemUser['pseudo'];
            $post->date_upd = (new Utils())::dbToDate($post->date_upd);

            $post->is_publish = $post->statut === 'publier' ? '1' : '0';
        }

        $this->datas['posts'] = $posts;
    }
}
