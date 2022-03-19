<?php
namespace App\Controller\Admin;

use App\Controller\Controller;
use App\Model\PostsModel;
use App\Utils\Form;
use App\Utils\Utils;

class PostController extends Controller
{

    /**
     * @var string
     */
    protected $title = 'Création d\'article';

    /**
     * @var string
     */
    protected $view = 'post';

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
    public function postAction(array $datas = array()): void
    {
        $this->init($datas);

        $this->postManagement();

        $this->addFormPost();

        echo $this->viewsRender($this->view, $this->datas, $this->folder);
    }
    
    /**
     * Post management
     *
     * @return void
     */
    private function postManagement()
    {
        $this->modelPosts = new PostsModel;
        $is_valide = true;
        $is_update = !empty($this->datas_get['id']) && $this->datas_match['action'] === 'update' ? true : false;

        if ($is_update) {
            $this->item_post = $this->modelPosts->fetchId($this->datas_get['id']);

            if (!empty($this->item_post)) {
                if (empty($this->datas_post)) {
                    $is_valide = false;

                    $this->datas_post['title'] = $this->item_post['title'];
                    $this->datas_post['chapo'] = $this->item_post['chapo'];
                    $this->datas_post['author'] = $this->item_post['author'];
                    $this->datas_post['content'] = $this->item_post['content'];
                }
            } else {
                $this->utils::setErrorsSession('L\'article est introuvable');
                $this->utils::redirect("/admin/posts/1");
            }
        }

        $datas_post = array(
            "title" => 'Titre',
            "chapo" => 'Chapô',
            "author" => 'Auteur',
            "content" => 'Contenu'
        );
        
        $action = '';
        $this->formLogin = new Form($action, 'POST', $this->datas_post);

        if ($is_valide) {
            $is_valide = $this->formLogin->verifDatasForm($datas_post);
        }

        if ($is_valide) {
            $this->createUpdatePost($is_update);
        }
    }

    /**
     * Add form post
     *
     * @return void
     */
    private function addFormPost(): void
    {
        $this->datas['formPost'] = $this->getformPost($this->formLogin);
    }
    
    /**
     * Create post
     *
     * @param bool $is_update
     * @return void
     */
    private function createUpdatePost($is_update): void
    {

        $slug = (new Utils())::changeSlugCharacter($this->datas_post['title']);
        $datas_save = array(
            'title' => $this->datas_post['title'],
            'chapo' => $this->datas_post['chapo'],
            'content' => $this->datas_post['content'],
            'slug' => $slug,
            'author' => $this->datas_post['author'],
            'user_upd' => (int) $this->datas['user_session']['user_id'],
            'date_upd' => date('Y-m-d')
        );
        
        if ($is_update) {
            $datas_save['user_id'] = (int) $this->item_post['user_id'];
            $datas_save['statut'] = $this->item_post['statut'];
            $datas_save['date_add'] = $this->item_post['date_add'];
            $datas_save['user_add'] = (int) $this->item_post['user_add'];

            $is_save = $this->modelPosts->save($datas_save, $this->item_post['post_id']);
        } else {
            $datas_save['user_id'] = (int) $this->datas['user_session']['user_id'];
            $datas_save['statut'] = 'en_attente';
            $datas_save['date_add'] = date('Y-m-d');
            $datas_save['user_add'] = (int) $this->datas['user_session']['user_id'];

            $is_save = $this->modelPosts->save($datas_save);
        }

        if ($is_save) {
            $message_success = !empty($this->datas_get['id']) ?
                'Modifications de l\'article éffectuées' :
                    'Création de l\'article effectuées';
                    
            $this->utils::setSuccessSession($message_success);
            $this->utils::redirect("/admin/posts/1/");
        }

        $this->datas['errors'] = implode('</br>', $this->modelPosts->getErrors());
    }

    /**
     * Creation of the post form
     *
     * @param  Objet $formPost Objet of the Form
     * @return string form contact in HTML
     */
    public function getformPost(Form $formPost): string
    {
        $fields = $formPost->addInputText('title', 'title', 'Titre', 'text', true);
        $fields .= $formPost->addInputText('chapo', 'chapo', 'Chapô', 'text', true);
        $fields .= $formPost->addInputText('author', 'author', 'Auteur', 'text', true);
        $fields .= $formPost->addTextArea('content', 'content', 'Contenu', true);
        $fields .= $formPost->addButton('Enregistrer', 'margin-btn-form');

        return $formPost->createForm($fields);
    }
}
