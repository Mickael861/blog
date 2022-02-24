<?php
namespace App\Controller\Admin;

use App\Controller\Controller;
use App\Model\CommentsModel;
use App\Model\PostsModel;
use App\Model\UserModel;

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

        $userModel = new UserModel;
        $postsModel = new PostsModel;
        $this->commentsModel = new CommentsModel;
        
        $this->page = empty($this->datas_match['page']) ? 1 : (int) $this->datas_match['page'];
        $this->datas['page'] = $this->page;

        $this->addStatusManagement($this->commentsModel);

        $comments = $this->commentsModel->fetchAll(true, 'comment_id', $this->page, $filters, 'DESC');
        if (!empty($comments)) {
            $this->addDatasNbrsPages($this->commentsModel);
    
            foreach ($comments as &$comment) {
                $itemUser = $userModel->fetchId($comment->user_id);
                if (!empty($itemUser)) {
                    $comment->pseudo = $itemUser['pseudo'];
                }

                $itemPost = $postsModel->fetchId($comment->post_id);
                if (!empty($itemPost)) {
                    $comment->title_post = $itemPost['title'];
                }

                $this->addDatasStatutItem($comment);

                $comment->content = nl2br($comment->content);
            }

            if (!empty($this->datas_get['valide'])) {
                $datas_save['statut'] = 'valider';
                $this->commentsModel->save($datas_save, (int) $this->datas_get['valide']);
                $_SESSION['success'] = 'Commentaire accepté';
                header('Location: /admin/comments/' . $this->page);
                exit();
            }

            if (!empty($this->datas_get['refuse'])) {
                $datas_save['statut'] = 'refuser';
                $this->commentsModel->save($datas_save, $this->datas_get['refuse']);
                $_SESSION['success'] = 'Commentaire refusé';
                header('Location: /admin/comments/' . $this->page);
                exit();
            }

            $this->datas['comments'] = $comments;
        } else {
            $this->datas['errors'] = 'Aucun commentaire trouvé';
        }

        echo $this->viewsRender($this->view, $this->datas, $this->folder);
    }
}
