<?php
namespace App\Controller;

class PostsController extends Controller
{

    /**
     * @var string
     */
    private $title = 'Articles';

    /**
     * @var string
     */
    private $view = 'posts';
        
    /**
     * @var array
     */
    private $datas = array();

    /**
     * view of action
     *
     * @param array Datas POST|GET
     * @return void
     */
    public function postsAction(array $datas = array()): void
    {
        //Title name view
        $this->datas['title'] = $this->title;

        echo parent::viewsRender($this->view, $this->datas);
    }
}
