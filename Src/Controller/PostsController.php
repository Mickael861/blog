<?php
namespace App\Controller;

use App\Model\PostsModel;

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
     * Datas
     *
     * @var array
     */
    protected $datas = array();

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

        $posts = $modelPosts->fetchAll(true, 'post_id', $page, 'ASC', 4);
        
        $nbrs_page = $modelPosts->getNbrsPage();
        $this->disabledPagination($page, $nbrs_page);
        $this->datas['nbrs_page'] = $nbrs_page;

        if (!empty($posts)) {
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
