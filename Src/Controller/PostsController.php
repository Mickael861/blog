<?php
namespace App\Controller;

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
        $this->init();
        
        //Datas POST
        $datasGet = empty($datas['GET']) ? array() : $datas['GET'];
        $page = empty($datasGet['page']) ? 1 : (int) $datasGet['page'];
        
        $this->datas['page'] = $page;

        $modelPosts = new PostsModel();

        $posts = $modelPosts->fetchAll(true, 'post_id', $page, 'DESC', 4);
        
        $nbrs_page = $modelPosts->getNbrsPage();
        $this->disabledPagination($page, $nbrs_page);
        $this->datas['nbrs_page'] = $nbrs_page;

        if (!empty($posts)) {
            foreach ($posts as $post) {
                $userModel = new UserModel;
                $itemUser = $userModel->fetchId($post->utilisateur_id);
                $post->user_name = $itemUser['pseudo'];

                $post->date_upd = (new Utils())::dbToDate($post->date_upd);
            }
            $this->datas['posts'] = $posts;
        }
        
        if (!empty($modelPosts->getErrors())) {
            $this->datas['errors'] = $modelPosts->getErrors()['page'];
        }

        echo $this->viewsRender($this->view, $this->datas);
    }
    
    /**
     * Handles next and before pagination
     *
     * @param  int $page Current page
     * @param  int $nbrs_page numbers of page
     * @return void
     */
    private function disabledPagination(int $page, int $nbrs_page)
    {
        $this->datas['pagination_next'] = '';
        $this->datas['pagination_before'] = '';

        if ($page > $nbrs_page - 1) {
            $this->datas['pagination_next'] = 'disabled';
        } elseif ($page === 1) {
            $this->datas['pagination_before'] = 'disabled';
        }
    }
}
