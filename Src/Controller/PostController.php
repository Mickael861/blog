<?php
namespace App\Controller;

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
     * Datas
     *
     * @var array
     */
    protected $datas = array();

    /**
     * view of action
     *
     * @param array Datas POST|GET
     * @return void
     */
    public function postAction(array $datas = array()): void
    {
        parent::init();

        //Datas GET
        $datasGet = empty($datas['GET']) ? array() : $datas['GET'];
        //Datas GET
        $datasPost = empty($datas['POST']) ? array() : $datas['POST'];
        $post_id = (int) $datasGet['id'];

        $modelPosts = new PostsModel();
        $post = $modelPosts->fetchId($post_id);
  
        if (!empty($post)) {
            //verification on the slug
            $slug_url = $datasGet['slug'];
            $slug_post = $post['slug'];
            if ($slug_url !== $slug_post) {
                $this->datas['errors'] = 'L\'url est différente de celle attendue';
                $post['titre'] = 'Aucun résultat';
            }

            //comment form fields
            $datasContactExpected = array(
                "content" => 'Contenu'
            );
            $formCommentPost = new Form('/post/' . $post['slug'] . '/' . $post['post_id'] . '#comment_form', 'POST', $datasPost);
            //verification form data
            $is_valide = $formCommentPost->verifDatasForm($datasContactExpected);
            if ($is_valide) {
                //sauvegarde
            }

            //create comment form
            $formComment = $this->getFormComment($formCommentPost);
            $this->datas['formCommentPost'] = $formComment;

            $this->datas['post'] = $post;
        } else {
            $this->datas['errors'] = 'L\'identifiant de l\'article est incorrecte';
        }
        
        echo parent::viewsRender($this->view, $this->datas);
    }
        
    /**
     * Creation of the contact form
     *
     * @param  Objet $formContact Objet of the Form
     * @return string form contact in HTML
     */
    public function getFormComment(Form $formContactHome): string
    {
        $fields = $formContactHome->addTextArea('content', 'content', 'Votre commentaire', true);
        $fields .= $formContactHome->addButton();

        return $formContactHome->createForm($fields, 'form_comment');
    }
}
