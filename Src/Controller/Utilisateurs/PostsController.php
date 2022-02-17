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

        $this->page = empty($this->paramsUrl['page']) ? 1 : (int) $this->paramsUrl['page'];
        $this->datas['page'] = $this->page;

        $modelPosts = new PostsModel();

        $posts = $modelPosts->fetchAll(true, 'post_id', $this->page, 'DESC', 4);
        $this->nbrs_page = $modelPosts->getNbrsPage();
        $this->disabledPagination();
        $this->datas['nbrs_page'] = $this->nbrs_page;

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
}
