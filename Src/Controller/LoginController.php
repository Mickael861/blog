<?php
namespace App\Controller;

use App\Model\UtilisateursModel;
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
    public function loginAction(array $datas = array()): void
    {
        parent::init();

        //Datas POST
        $datasPost = empty($datas['POST']) ? array() : $datas['POST'];
  
        if (!empty($this->datas['user_session'])) {
            header('Location: /');
            exit();
        }

        //login form fields
        $datasContactExpected = array(
            "email" => 'E-mail',
            "password" => 'Mot de passe'
        );
        $action = '/login/#login_form';
        $formLogin = new Form($action, 'POST', $datasPost);
        //verification form data
        $is_valide = $formLogin->verifDatasForm($datasContactExpected);
        if ($is_valide) {
            $modelUtilisateurs = new UtilisateursModel;
            $parameters = array(
                'email' => $datasPost['email']
            );
            $utilisateur = $modelUtilisateurs->getAllWithParams($parameters);
            if (!empty($utilisateur)) {
                $is_correct = password_verify($datasPost['password'], $utilisateur[0]->password);
                if ($is_correct) {
                    $_SESSION['utilisateur_id'] = $utilisateur[0]->utilisateur_id;
                    $_SESSION['user_name'] = $utilisateur[0]->pseudo;
                    header('Location: /home/?login=1');
                    exit();
                }
            }
            $this->datas['errors'] = 'Votre adresse E-mail ou votre mot de passe est incorrecte';
        }

        //create login form
        $formLogin = $this->getFormLogin($formLogin);
        $this->datas['formLogin'] = $formLogin;

        echo parent::viewsRender($this->view, $this->datas);
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
        $fields .= $formLogin->addButton('Se connecter');

        return $formLogin->createForm($fields);
    }
}
