<?php
namespace App\Controller\Utilisateurs;

use App\Controller\Controller;
use App\Model\PostsModel;
use App\Model\UserModel;
use App\Utils\Utils;

class PostsController extends Controller
{

    /**
     * @var string
     */
    protected $title = 'Articles';

    /**
     * @var string
     */
    protected $view = 'posts';

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

        echo $this->viewsRender($this->view, $this->datas);
    }
    
    /**
     * Posts management
     *
     * @return void
     */
    private function postsManagement(): void
    {
        $this->addDatasPages();
        
        $modelPosts = new PostsModel();
        $filters = array(
            'publier' => 'statut'
        );
        $posts = $modelPosts->fetchAll(true, 'post_id', $this->page, $filters, 'DESC', 6);
        if (!empty($posts)) {
            $this->addDatasNbrsPages($modelPosts);

            $this->addDatasPosts($posts);

            if (!empty($modelPosts->getErrors())) {
                $this->datas['errors'] = $modelPosts->getErrors()['page'];
            }
        } else {
            $this->datas['errors'] = 'Aucun article disponible';
        }
    }
    
    /**
     * Add data to item
     *
     * @param  array $posts
     * @return void
     */
    private function addDatasPosts(array $posts)
    {
        $userModel = new UserModel;

        foreach ($posts as $post) {
            $itemUser = $userModel->fetchId($post->user_id);
            $post->user_name = $itemUser['pseudo'];

            $post->date_upd = (new Utils())::dbToDate($post->date_upd);
        }
        
        $this->datas['posts'] = $posts;
    }
}
