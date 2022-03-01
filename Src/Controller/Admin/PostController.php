<?php
namespace App\Controller\Admin;

use App\Controller\Controller;
use App\Model\PostsModel;
use App\Utils\Form;

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

        $this->modelPosts = new PostsModel;
        $is_update = false;
        $with_verif = true;

        if (!empty($this->datas_get['id']) && empty($this->datas_post)) {
            $this->item_post = $this->modelPosts->fetchId($this->datas_get['id']);
            if (!empty($this->item_post)) {
                $this->datas_post['title'] = $this->item_post['title'];
                $this->datas_post['chapo'] = $this->item_post['chapo'];
                $this->datas_post['author'] = $this->item_post['author'];
                $this->datas_post['content'] = $this->item_post['content'];
            }

            $is_update = true;
            $with_verif = false;
        }

        $datas_post = array(
            "title" => 'Titre',
            "chapo" => 'Chapô',
            "author" => 'Auteur',
            "content" => 'Contenu'
        );
        $action = '';
        $formLogin = new Form($action, 'POST', $this->datas_post);
        
        $is_valide = false;
        if ($with_verif) {
            $is_valide = $formLogin->verifDatasForm($datas_post);
        }

        if ($is_valide) {
            $this->createUpdatePost($is_update);
        }

        //create login form
        $formLogin = $this->getformPost($formLogin);
        $this->datas['formPost'] = $formLogin;

        echo $this->viewsRender($this->view, $this->datas, $this->folder);
    }
    
    /**
     * Create post
     *
     * @return void
     */
    private function createUpdatePost($is_update): void
    {
        $datas_save = array(
            'title' => $this->datas_post['title'],
            'chapo' => $this->datas_post['chapo'],
            'content' => $this->datas_post['content'],
            'slug' => str_replace(array(' ', '\''), array('-', ''), strtolower($this->datas_post['title'])),
            'author' => $this->datas_post['author'],
            'user_upd' => (int) $this->datas['user_session']['user_id'],
            'date_upd' => date('Y-m-d')
        );
        
        if ($is_update && !empty($this->item_post)) {
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
            $_SESSION['success'] = $is_update ?
                'Modifications de l\'article éffectuées' :
                    'Création de l\'article effectuées';
            header('Location: /admin/posts/1/');
            exit();
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
        $fields .= $formPost->addTextArea('content', 'content', 'Message', true);
        $fields .= $formPost->addButton('Enregistrer', 'margin-btn-form');

        return $formPost->createForm($fields);
    }
}
