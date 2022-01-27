<?php
namespace App\Controller;

use App\Model\PostsModel;

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

        $modelPosts = new PostsModel();

        $params = array(
            'utilisateur_id' => 0,
            'titre' => 'modifié',
            'chapo' => 'un chapo modifié',
            'content' => 'un contenu modifié',
            'statut' => 'publie'
        );
        $post = $modelPosts->fetchAll();
        
        if ($post) {
            $this->datas['posts'] = $post;
        }
        
        echo parent::viewsRender($this->view, $this->datas);
    }
}
