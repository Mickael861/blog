<?php
namespace App\Controller\Utilisateurs;

use App\Controller\Controller;
use App\Model\UserModel;
use App\Utils\Form;

class SignupController extends Controller
{

    /**
     * @var string
     */
    protected $title = 'Créer un compte';

    /**
     * @var string
     */
    protected $view = 'signup';

    /**
     * @var string
     */
    protected $no_access_session = true;

    /**
     * view of action
     *
     * @param array Datas POST|GET
     * @return void
     */
    public function signupAction(array $datas = array()): void
    {
        $this->init($datas);

        $this->signupManagement();
        
        $this->addFormSignup();

        echo $this->viewsRender($this->view, $this->datas);
    }

    /**
     * Signup Management
     *
     * @return void
     */
    private function signupManagement()
    {
        $is_valide = $this->getVerifDatasForm();
        if ($is_valide) {
            $this->modelUtilisateurs = new UserModel;
            $same_errors = $this->getErrorsFormSave();
            if (!$same_errors) {
                $this->createAccount();
            } else {
                $this->datas['errors'] = $same_errors;
            }
        }
    }

    /**
     * Add the form to the view
     *
     * @return void
     */
    private function addFormSignup(): void
    {
        $this->datas['formSignup'] = $this->getFormSignup();
    }

    private function createAccount()
    {
        $datas = array(
            'role' => 'utilisateur',
            'pseudo' => $this->datas_post['pseudo'],
            'firstname' => $this->datas_post['firstname'],
            'lastname' => $this->datas_post['lastname'],
            'email' => $this->datas_post['email'],
            'password' => password_hash($this->datas_post['password'], PASSWORD_ARGON2I),
            'date_add' => date('Y-m-d'),
            'statut' => 'en_attente'
        );

        $is_save = $this->modelUtilisateurs->save($datas);
        if ($is_save) {
            $_SESSION['success'] = 'Compte crée avec succés et en attente d\'acceptation';
            header('Location: /');
            exit();
        } else {
            $this->datas['errors'] = implode('<br>', $this->modelUtilisateurs->getErrors());
        }
    }

     /**
     * Verify that the form data is correct
     *
     * @return array|bool true the data is correct, false otherwise
     */
    private function getVerifDatasForm()
    {
        $datasContactExpected = array(
            'pseudo' => 'Pseudo',
            'firstname' => 'Prénom',
            'lastname' => 'Nom',
            "email" => 'E-mail',
            "password" => 'Mot de passe'
        );
        $action = '/signup';
        $this->formSignup = new Form($action, 'POST', $this->datas_post);

        return $this->formSignup->verifDatasForm($datasContactExpected);
    }
    
    /**
     * Manage errors related to the account creation form
     *
     * @return string False if no error, a character string otherwise
     */
    private function getErrorsFormSave(): string
    {
        $error_password = strlen($this->datas_post['password']) < 15;
        $pseudo = $this->datas_post['pseudo'];
        $email = $this->datas_post['email'];
        $same_errors = $this->modelUtilisateurs->getErrorsSameDatas($pseudo, $email);
        if ($error_password) {
            $set_br = !empty($same_errors) ? '</br>' : '';
            $same_errors .= $set_br . 'Le mot de passe doit contenir 15 caractéres minimum';
        }

        return $same_errors;
    }
        
    /**
     * Creation of the login form
     *
     * @return string form contact in HTML
     */
    public function getFormSignup(): string
    {
        $fields = $this->formSignup->addInputText('pseudo', 'pseudo', 'Votre pseudo', 'text', true);
        $fields .= $this->formSignup->addInputText('firstname', 'firstname', 'Votre prenom', 'text', true);
        $fields .= $this->formSignup->addInputText('lastname', 'lastname', 'Votre nom', 'text', true);
        $fields .= $this->formSignup->addInputText('email', 'email', 'Votre adresse e-mail', 'email', true);
        $fields .= $this->formSignup->addInputText('password', 'password', 'Votre mot de passe', 'password', true);
        $fields .= $this->formSignup->addButton('Créer un compte', 'margin-btn-form');

        return $this->formSignup->createForm($fields);
    }
}
