<?php
namespace App\Controller\Admin;

use App\Controller\Controller;
use App\Model\CommentsModel;
use App\Model\PostsModel;
use App\Model\UserModel;
use App\Utils\Utils;

class CommentsController extends Controller
{

    /**
     * @var string
     */
    protected $title = 'Gestion des commentaires';

    /**
     * @var string
     */
    protected $view = 'comments';

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
    public function commentsAction(array $datas = array()): void
    {
        $this->init($datas);

        $this->commentsModel = new CommentsModel;

        $this->addDatasPages();

        $this->addStatusManagement($this->commentsModel);

        $this->commentsManagement();

        echo $this->viewsRender($this->view, $this->datas, $this->folder);
    }
    
    /**
     * Comments management
     *
     * @return void
     */
    private function commentsManagement()
    {
        $comments = $this->commentsModel->fetchAll(true, 'comment_id', $this->page, $this->filters, 'DESC');
        if (!empty($comments)) {
            $this->addDatasNbrsPages($this->commentsModel);
    
            $this->addDatasComments($comments);

            $this->addSaveAccount($this->commentsModel);
        } else {
            $this->datas['errors'] = 'Aucun commentaire trouvé';
        }
    }
    
    /**
     * Add data to item
     *
     * @param  objet $comments
     * @return void
     */
    private function addDatasComments($comments)
    {
        $userModel = new UserModel;
        $postsModel = new PostsModel;

        foreach ($comments as &$comment) {
            $itemUser = $userModel->fetchId($comment->user_id);
            if (!empty($itemUser)) {
                $comment->pseudo = $itemUser['pseudo'];
            }

            $itemPost = $postsModel->fetchId($comment->post_id);
            if (!empty($itemPost)) {
                $comment->title_post = $itemPost['title'];
                $comment->date_add = (new Utils())::dbToDate($comment->date_add);
            }

            $this->addDatasStatutItem($comment);

            $comment->content = nl2br($comment->content);
        }

        $this->datas['comments'] = $comments;
    }
}
