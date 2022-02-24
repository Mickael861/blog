<?php
namespace App\Controller\Utilisateurs;

use App\Controller\Controller;
use App\Model\UserModel;
use App\Utils\Form;

class LoginController extends Controller
{

    /**
     * @var string
     */
    protected $title = 'Connexion';

    /**
     * @var string
     */
    protected $view = 'login';

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
    public function loginAction(array $datas = array()): void
    {
        $this->init($datas);

        //login form fields
        $datasContactExpected = array(
            "email" => 'E-mail',
            "password" => 'Mot de passe'
        );
        $action = '/login';
        $formLogin = new Form($action, 'POST', $this->datas_post);
        //verification form data
        $is_valide = $formLogin->verifDatasForm($datasContactExpected);
        if ($is_valide) {
            $modelUtilisateurs = new UserModel;
            $parameters = array(
                'email' => $this->datas_post['email']
            );
            $utilisateur = $modelUtilisateurs->getAllWithParams($parameters);
            if (!empty($utilisateur)) {
                $is_correct = password_verify($this->datas_post['password'], $utilisateur[0]->password);
                if ($is_correct) {
                    if ($utilisateur[0]->statut === "en_attente") {
                        $this->datas['errors'] = 'Votre compte n\'a pas été vérifié';
                    } elseif ($utilisateur[0]->statut === 'refuser') {
                        $this->datas['errors'] = 'Votre compte a été refusé';
                    } else {
                        $this->session::setDatasSession(array(
                            'user_id' => $utilisateur[0]->user_id,
                            'role' => $utilisateur[0]->role,
                            'user_pseudo' => $utilisateur[0]->pseudo
                        ));
                        
                        if ($utilisateur[0]->role === 'admin') {
                            $_SESSION['success'] = 'Vous êtes connecté';
                            header('Location: /admin/home/');
                            exit();
                        }
    
                        $_SESSION['success'] = 'Vous êtes connecté';
                        header('Location: /');
                        exit();
                    }
                } else {
                    $this->datas['errors'] = 'Votre adresse E-mail ou votre mot de passe est incorrecte';
                }
            } else {
                $this->datas['errors'] = 'Votre adresse E-mail ou votre mot de passe est incorrecte';
            }
        }

        //create login form
        $formLogin = $this->getFormLogin($formLogin);
        $this->datas['formLogin'] = $formLogin;

        echo $this->viewsRender($this->view, $this->datas);
    }
        
    /**
     * Creation of the login form
     *
     * @param  Objet $formContact Objet of the Form
     * @return string form contact in HTML
     */
    public function getFormLogin(Form $formLogin): string
    {
        $fields = $formLogin->addInputText('email', 'email', 'Votre adresse e-mail', 'email', true);
        $fields .= $formLogin->addInputText('password', 'password', 'Votre mot de passe', 'password', true);
        $fields .= $formLogin->addButton('Se connecter', 'margin-btn-form');

        return $formLogin->createForm($fields);
    }
}
