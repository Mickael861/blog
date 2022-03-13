<?php
namespace App\Controller\Admin;

use App\Controller\Controller;
use App\Model\PostsModel;
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
        $this->addStatusManagement($this->modelPosts);

        $this->addDatasPages();
        
        $nbrs_article =  12;
        if (!empty($this->filters)) {
            $nbrs_article = 100;
        }

        $posts = $this->modelPosts->fetchAll(true, 'post_id', $this->page, $this->filters, 'DESC', $nbrs_article);
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
        foreach ($posts as &$post) {
            $this->addBadgeNewItems($post);

            $post->date_add = (new Utils())::dbToDate($post->date_add);
            $post->date_upd = (new Utils())::dbToDate($post->date_upd);
            $post->is_publish = $post->statut === 'publier' ? '1' : '0';
        }

        $this->datas['today'] = date('Y-m-d');

        $this->datas['posts'] = $posts;
    }
}
