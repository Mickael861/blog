<?php
namespace App\Controller\Utilisateurs;

use App\Controller\Controller;
use App\Core\Access;
use App\Model\CommentsModel;
use App\Model\PostsModel;
use App\Utils\Form;

class PostController extends Controller
{

    /**
     * @var string
     */
    protected $title = 'Article';

    /**
     * @var string
     */
    protected $view = 'post';

    /**
     * view of action
     *
     * @param array Datas POST|GET
     * @return void
     */
    public function postAction(array $datas = array()): void
    {
        $this->init($datas);
        
        $this->postManagement();
        
        echo $this->viewsRender($this->view, $this->datas);
    }
    
    /**
     * Post Management
     *
     * @return void
     */
    private function postManagement()
    {
        $post_id = (int) $this->datas_match['id'];

        $this->ModelComments = new CommentsModel();
        $modelPosts = new PostsModel();

        $this->itemPost = $modelPosts->fetchId($post_id);
        $slug_url = $this->datas_match['slug'];
        $slug_post = $this->itemPost['slug'];
        
        if (!empty($this->itemPost) && $slug_url === $slug_post) {
            $this->addDatasPost();
            
            $is_valide = $this->verifFormDatasComments();
            if ($is_valide) {
                $this->saveDatasComments();
            }
            
            if (!empty($this->datas_get['success'])) {
                $this->utils::setSuccessSession('Commentaire enregistré et en attente de validation');
                $this->utils::redirect("/post/$slug_post/$post_id/#");
            }
            
            $this->getDatasComments();

            $this->addFormComments();
        } else {
            $this->datas['errors'] = 'La page que vous recherchez est introuvable';
        }
    }
    
    /**
     * Add datas post
     *
     * @return void
     */
    private function addDatasPost(): void
    {
        $this->itemPost['content'] = nl2br($this->itemPost['content']);
        $this->itemPost['date_upd'] = $this->utils::dbToDate($this->itemPost['date_upd']);

        $this->datas['post'] = $this->itemPost;
    }
    
    /**
     * save the comments form data
     *
     * @return void
     */
    private function saveDatasComments(): void
    {
        $is_user_exist = (new Access())->userIsAccept($this->datas['user_session']['user_id']);
        if ($is_user_exist) {
            $datas = array(
                'post_id' => (int) $this->itemPost['post_id'],
                'user_id' => (int) $this->datas['user_session']['user_id'],
                'content' => $this->datas_post['content'],
                'statut' => 'en_attente',
                'date_add' => date('Y-m-d')
            );
            
            $is_save = $this->ModelComments->save($datas);
            if ($is_save) {
                $this->utils::redirect($_SERVER['REQUEST_URI'] . '?success=1');
            } else {
                $this->datas['errors_comment'] = implode('</br>', $this->ModelComments->getErrors());
            }
        } else {
            $this->datas['errors_comment'] = 'Vous n\'avez pas l\'autorisation de commenter';
        }
    }
    
    /**
     * Comments form data checks
     *
     * @return array|bool true the data is correct, false otherwise
     */
    private function verifFormDatasComments()
    {
        $datasContactExpected = array(
            "content" => 'Contenu'
        );
        $action = '/post/' . $this->itemPost['slug'] . '/' . $this->itemPost['post_id'] . '/#comment_form';
        $this->formCommentPost = new Form($action, 'POST', $this->datas_post);

        return $this->formCommentPost->verifDatasForm($datasContactExpected);
    }
    
    /**
     * Add form comments
     *
     * @return void
     */
    private function addFormComments(): void
    {
        $this->datas['formCommentPost'] = $this->getFormComment($this->formCommentPost);
    }
    
    /**
     * get datas comments
     *
     * @return void
     */
    private function getDatasComments(): void
    {
        $itemsComments = $this->ModelComments->getCommentsUser($this->itemPost['post_id']);
        if (!empty($itemsComments)) {
            foreach ($itemsComments as &$comment) {
                $comment->date_add = $this->utils::dbToDate($comment->date_add);
                $comment->content = nl2br($comment->content);
            }
            $this->datas['comments'] = $itemsComments;
        }
    }
        
    /**
     * Creation of the comment form
     *
     * @param  Objet $formContact Objet of the Form
     * @return string form contact in HTML
     */
    private function getFormComment(): string
    {
        $fields = $this->formCommentPost->addTextArea('content', 'content', 'Votre commentaire', true);
        $fields .= $this->formCommentPost->addButton('Publier', 'btn-comment');

        return $this->formCommentPost->createForm($fields);
    }
}
