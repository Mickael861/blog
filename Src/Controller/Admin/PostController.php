<?php
namespace App\Controller\Admin;

use App\Controller\Controller;
use App\Model\PostsModel;
use App\Model\UserModel;
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

        $modelPosts = new PostsModel;
        $datas_post = array(
            "title" => 'Titre',
            "chapo" => 'Chapô',
            "author_id" => 'Auteur',
            "content" => 'Contenu'
        );
        $action = '';
        $formLogin = new Form($action, 'POST', $this->datas_post);
        $is_valide = $formLogin->verifDatasForm($datas_post);
        if (empty($this->datas_get['id']) && $is_valide) {
            $datas_save = array(
                'user_id' => (int) $this->datas['user_session']['user_id'],
                'title' => $this->datas_post['title'],
                'chapo' => $this->datas_post['chapo'],
                'content' => $this->datas_post['content'],
                'author_id' => (int) $this->datas_post['author_id'],
                'statut' => 'publier',
                'date_add' => date('Y-m-d'),
                'user_add' => (int) $this->datas['user_session']['user_id']
            );

            $datas_save['slug'] = str_replace(' ', '-', strtolower($this->datas_post['title']));
            $is_save = $modelPosts->save($datas_save);
            if ($is_save) {
                header('Location: /admin/posts/1/?create=1');
                exit();
            }

            $this->datas['errors'] = implode('</br>', $modelPosts->getErrors());
        }

        //create login form
        $formLogin = $this->getformPost($formLogin);
        $this->datas['formPost'] = $formLogin;

        echo $this->viewsRender($this->view, $this->datas, $this->folder);
    }

    /**
     * Creation of the post form
     *
     * @param  Objet $formPost Objet of the Form
     * @return string form contact in HTML
     */
    public function getformPost(Form $formPost): string
    {
        $userModel = new UserModel;
        $itemsUser = $userModel->getUserSelect();

        $fields = $formPost->addInputText('title', 'title', 'Titre', 'text', true);
        $fields .= $formPost->addInputText('chapo', 'chapo', 'Chapô', 'text', true);
        $fields .= $formPost->addSelect(
            'author_id',
            'author_id',
            'Créateur',
            $itemsUser,
            'Choisisez un utilisateur',
            true
        );
        $fields .= $formPost->addTextArea('content', 'content', 'Message', true);
        $fields .= $formPost->addButton('Enregistrer', 'margin-btn-form');

        return $formPost->createForm($fields);
    }
}
