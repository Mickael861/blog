<?php
namespace App\Controller\Utilisateurs;

use App\Controller\Controller;
use App\Model\CommentsModel;
use App\Model\PostsModel;
use App\Model\UserModel;
use App\Utils\Form;
use App\Utils\Utils;

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
        
        $post_id = (int) $this->datas_match['id'];

        $ModelComments = new CommentsModel();
        $modelPosts = new PostsModel();
        $itemPost = $modelPosts->fetchId($post_id);
 
        if (!empty($itemPost)) {
            $userModel = new UserModel;
            $itemUser = $userModel->fetchId($itemPost['user_id']);
            $itemPost['user_name'] = $itemUser['pseudo'];
            $itemPost['content'] = nl2br($itemPost['content']);
            $this->datas['post'] = $itemPost;
            
            //verification on the slug
            $slug_url = $this->datas_match['slug'];
            $slug_post = $itemPost['slug'];
            if ($slug_url !== $slug_post) {
                $this->datas['errors'] = 'L\'url est différente de celle attendue'; //TODO
                $itemPost['title'] = 'Aucun résultat';
            }

            //comment form fields
            $datasContactExpected = array(
                "content" => 'Contenu'
            );
            $action = '/post/' . $itemPost['slug'] . '/' . $itemPost['post_id'] . '/#comment_form';
            $formCommentPost = new Form($action, 'POST', $this->datas_post);

            //verification form data
            $is_valide = $formCommentPost->verifDatasForm($datasContactExpected);
            if ($is_valide) {
                $datas = array(
                    'post_id' => (int) $itemPost['post_id'],
                    'user_id' => (int) $this->datas['user_session']['user_id'],
                    'content' => $this->datas_post['content'],
                    'statut' => 'en_attente',
                    'date_add' => date('Y-m-d')
                );
                
                $is_save = $ModelComments->save($datas);
                if ($is_save) {
                    header('Location: ' . $_SERVER['REQUEST_URI'] . '?success=1');
                    exit();
                } else {
                    $this->datas['errors_comment'] = implode('</br>', $ModelComments->getErrors());
                }
            }
            
            if (!empty($this->datas_get['success'])) {
                $this->datas['success_comment'] = 'Commentaire enregistré et en attente de validation';
            }
            
            //View Comments post
            $itemsComments = $ModelComments->getCommentsUser($itemPost['post_id']);
            if (!empty($itemsComments)) {
                foreach ($itemsComments as &$comment) {
                    $comment->date_add = (new Utils())::dbToDate($comment->date_add);
                    $comment->content = nl2br($comment->content);
                }
                $this->datas['comments'] = $itemsComments;
            }

            //create comment form
            $formComment = $this->getFormComment($formCommentPost);
            $this->datas['formCommentPost'] = $formComment;
        } else {
            $this->datas['errors'] = 'L\'identifiant de l\'article est incorrecte';
        }
        
        echo $this->viewsRender($this->view, $this->datas);
    }
        
    /**
     * Creation of the comment form
     *
     * @param  Objet $formContact Objet of the Form
     * @return string form contact in HTML
     */
    public function getFormComment(Form $formContactHome): string
    {
        $fields = $formContactHome->addTextArea('content', 'content', 'Votre commentaire', true);
        $fields .= $formContactHome->addButton('Publier', 'btn-comment');

        return $formContactHome->createForm($fields);
    }
}
