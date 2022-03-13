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
        $this->addDatasPages();
        
        $nbr_posts =  12;
        if (!empty($this->filters)) {
            $nbr_posts = 100;
        }

        $comments = $this->commentsModel->fetchAll(true, 'comment_id', $this->page, $this->filters, 'DESC', $nbr_posts);
        if (!empty($comments)) {
            $this->addDatasNbrsPages($this->commentsModel);
    
            $this->addDatasComments($comments);

            $this->changeStatusItem($this->commentsModel, array(
                'accept' => 'Commentaire accepté',
                'refus' => 'Commentaire refusé'
            ));
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

            $this->addBadgeNewItems($comment);

            $this->addDatasStatutItem($comment);

            $maxlen = 100;
            $comment->date_add = (new Utils())::dbToDate($comment->date_add);
            
            $strlen = strlen($comment->content);
            $with_btn_content = false;
            if ($strlen > $maxlen) {
                $with_btn_content = true;
                $chaine = substr($comment->content, 0, $maxlen);
                $last_space = strrpos($chaine, " ");
                $comment->content = substr($chaine, 0, $last_space)."...";
            }

            $comment->with_btn_content = $with_btn_content;
            

            $comment->content = nl2br($comment->content);
        }
        
        $this->datas['today'] = date('Y-m-d');

        $this->datas['comments'] = $comments;
    }
    
    /**
     * Manage the display of comment content in the modal
     *
     * @param  array $datas POST|GET
     * @return json|array Data sent to ajax
     */
    public function getcontentcommentidAction($datas)
    {
        $this->init($datas);

        $modelComments = new CommentsModel;

        $comment_id = empty($this->datas_post['comment_id']) ? '' : $this->datas_post['comment_id'] ;

        if (!empty($comment_id)) {
            $itemComment = $modelComments->fetchId($comment_id);

            if (!empty($itemComment)) {
                $response['content_comment'] = nl2br($itemComment['content']);
            } else {
                $response['content_comment'] = 'Le contenu est introuvable';
            }
        }

        echo json_encode($response);
    }
}
