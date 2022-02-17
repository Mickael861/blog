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
     * view of action
     *
     * @param array Datas POST|GET
     * @return void
     */
    public function loginAction(array $datas = array()): void
    {
        $this->init($datas, true);

        //login form fields
        $datasContactExpected = array(
            "email" => 'E-mail',
            "password" => 'Mot de passe'
        );
        $action = '/login';
        $formLogin = new Form($action, 'POST', $this->datasPost);
        //verification form data
        $is_valide = $formLogin->verifDatasForm($datasContactExpected);
        if ($is_valide) {
            $modelUtilisateurs = new UserModel;
            $parameters = array(
                'email' => $this->datasPost['email']
            );
            $utilisateur = $modelUtilisateurs->getAllWithParams($parameters);
            if (!empty($utilisateur)) {
                $is_correct = password_verify($this->datasPost['password'], $utilisateur[0]->password);
                if ($is_correct) {
                    $this->session::setDatasSession(array(
                        'utilisateur_id' => $utilisateur[0]->utilisateur_id,
                        'role' => $utilisateur[0]->role,
                        'user_pseudo' => $utilisateur[0]->pseudo
                    ));
                    
                    if ($utilisateur[0]->role === 'admin') {
                        header('Location: /admin/home/?login=1');
                        exit();
                    }

                    header('Location: /home/?login=1');
                    exit();
                }
            }
            $this->datas['errors'] = 'Votre adresse E-mail ou votre mot de passe est incorrecte';
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
