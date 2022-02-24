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
        $this->page = empty($this->datas_match['page']) ? 1 : (int) $this->datas_match['page'];
        $this->datas['page'] = $this->page;
        
        $modelPosts = new PostsModel();
        $userModel = new UserModel;
        $filters = array(
            'publier' => 'statut'
        );
        $posts = $modelPosts->fetchAll(true, 'post_id', $this->page, $filters, 'DESC', 6);
        if (!empty($posts)) {
            $this->nbrs_page = $modelPosts->getNbrsPage();
            $this->disabledPagination();
            $this->datas['nbrs_page'] = $this->nbrs_page;

            foreach ($posts as $post) {
                $itemUser = $userModel->fetchId($post->user_id);
                $post->user_name = $itemUser['pseudo'];

                $post->date_upd = (new Utils())::dbToDate($post->date_upd);
            }
            $this->datas['posts'] = $posts;

            if (!empty($modelPosts->getErrors())) {
                $this->datas['errors'] = $modelPosts->getErrors()['page'];
            }
        } else {
            $this->datas['errors'] = 'Aucun article disponible';
        }
    }
}
