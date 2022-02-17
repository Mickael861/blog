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

        //signup form fields
        $datasContactExpected = array(
            'pseudo' => 'Pseudo',
            'prenom' => 'Prénom',
            'nom' => 'Nom',
            "email" => 'E-mail',
            "password" => 'Mot de passe'
        );
        $action = '/signup';
        $formSignup = new Form($action, 'POST', $this->datasPost);
        //verification form data
        $is_valide = $formSignup->verifDatasForm($datasContactExpected);
        if ($is_valide) {
            $this->modelUtilisateurs = new UserModel;

            $same_errors = $this->getErrorsFormSave();
            if (!$same_errors) {
                $datas = array(
                    'role' => 'utilisateur',
                    'pseudo' => $this->datasPost['pseudo'],
                    'prenom' => $this->datasPost['prenom'],
                    'nom' => $this->datasPost['nom'],
                    'email' => $this->datasPost['email'],
                    'password' => password_hash($this->datasPost['password'], PASSWORD_ARGON2I),
                    'statut' => 'en_attente'
                );

                $is_save = $this->modelUtilisateurs->save($datas);
                if ($is_save) {
                    header('Location: /home/?signup=1');
                    exit();
                } else {
                    $this->datas['errors'] = implode('<br>', $this->modelUtilisateurs->getErrors());
                }
            } else {
                $this->datas['errors'] = $same_errors;
            }
        }
        
        //create signup form
        $form_signup = $this->getFormSignup($formSignup);
        $this->datas['formSignup'] = $form_signup;

        echo $this->viewsRender($this->view, $this->datas);
    }
    
    /**
     * Manage errors related to the account creation form
     *
     * @return string False if no error, a character string otherwise
     */
    private function getErrorsFormSave(): string
    {
        $error_password = strlen($this->datasPost['password']) < 15;
        $same_errors = $this->modelUtilisateurs->getErrorsSameDatas($this->datasPost['pseudo'], $this->datasPost['email']);
        if ($error_password) {
            $set_br = !empty($same_errors) ? '</br>' : '';
            $same_errors .= $set_br . 'Le mot de passe doit contenir 15 caractéres minimum';
        }

        return $same_errors;
    }
        
    /**
     * Creation of the login form
     *
     * @param  Objet $formContact Objet of the Form
     * @return string form contact in HTML
     */
    public function getFormSignup(Form $formSignup): string
    {
        $fields = $formSignup->addInputText('pseudo', 'pseudo', 'Votre pseudo', 'pseudo', true);
        $fields .= $formSignup->addInputText('prenom', 'prenom', 'Votre prenom', 'prenom', true);
        $fields .= $formSignup->addInputText('nom', 'nom', 'Votre nom', 'nom', true);
        $fields .= $formSignup->addInputText('email', 'email', 'Votre adresse e-mail', 'email', true);
        $fields .= $formSignup->addInputText('password', 'password', 'Votre mot de passe', 'password', true);
        $fields .= $formSignup->addButton('Créer un compte', 'margin-btn-form');

        return $formSignup->createForm($fields);
    }
}
