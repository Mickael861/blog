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
        
        $this->getSuccessUserAccount();

        $this->page = empty($this->datas_match['page']) ? 1 : (int) $this->datas_match['page'];
        $this->datas['page'] = $this->page;

        $modelPosts = new PostsModel();
        $posts = $modelPosts->fetchAll(true, 'post_id', $this->page, 'DESC');
        $this->nbrs_page = $modelPosts->getNbrsPage();
        $this->disabledPagination();
        $this->datas['nbrs_page'] = $this->nbrs_page;

        if (!empty($posts)) {
            foreach ($posts as $post) {
                $userModel = new UserModel;
                $itemUser = $userModel->fetchId($post->user_id);
                $post->user_name = $itemUser['pseudo'];

                $post->date_upd = (new Utils())::dbToDate($post->date_upd);
            }
            $this->datas['posts'] = $posts;
        }

        if (!empty($this->datas_get['delete'])) {
             $modelPosts->delete($this->datas_get['delete']);
             header('Location: /admnPosts/1');
             exit();
        }
        
        if (!empty($modelPosts->getErrors())) {
            $this->datas['errors'] = $modelPosts->getErrors()['page'];
        }

        echo $this->viewsRender($this->view, $this->datas, $this->folder);
    }
}
