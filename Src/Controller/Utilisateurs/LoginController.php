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

        $this->loginManagement();

        $this->addFormLogin();

        echo $this->viewsRender($this->view, $this->datas);
    }

    /**
     * Add the form to the view
     *
     * @return void
     */
    private function addFormLogin(): void
    {
        $this->datas['formLogin'] = $this->getFormLogin();
    }
    
    /**
     * login management
     *
     * @return void
     */
    private function loginManagement(): void
    {
        $this->formLogin = new Form('/login', 'POST', $this->datas_post);

        $is_valide = $this->getVerifDatasForm();
        if ($is_valide) {
            $this->utilisateur = $this->checkLoginDetails();
            if (!empty($this->utilisateur)) {
                $this->checkLoginDatas();
            }
        }
    }
    
    /**
     * check login datas
     *
     * @return void
     */
    private function checkLoginDatas(): void
    {
        switch ($this->utilisateur[0]->statut) {
            case 'valider':
                $this->session::setDatasSession(array(
                    'user_id' => $this->utilisateur[0]->user_id,
                    'role' => $this->utilisateur[0]->role,
                    'user_pseudo' => $this->utilisateur[0]->pseudo
                ));
                if ($this->utilisateur[0]->role === 'admin') {
                    $this->utils::setSuccessSession('Vous êtes connecté');
                    $this->utils::redirect("/admin/home");
                }

                $this->utils::setSuccessSession('Vous êtes connecté');
                $this->utils::redirect("/");

                break;
            case 'refuser':
                $this->datas['errors'] = 'Votre compte a été refusé';
                break;
            default:
                $this->datas['errors'] = 'Votre compte est en attente de vérification';
                break;
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
            "email" => 'E-mail',
            "password" => 'Mot de passe'
        );
        
        return $this->formLogin->verifDatasForm($datasContactExpected);
    }
    
    /**
     * check login details
     *
     * @return array One user, otherwise empty array
     */
    private function checkLoginDetails(): array
    {
        $this->modelUtilisateurs = new UserModel;
        $parameters = array(
            'email' => $this->datas_post['email']
        );

        $utilisateur = $this->modelUtilisateurs->getAllWithParams($parameters);
        if (empty($utilisateur) || !password_verify($this->datas_post['password'], $utilisateur[0]->password)) {
            $this->datas['errors'] = 'Votre adresse E-mail ou votre mot de passe est incorrecte';
        }

        return $utilisateur;
    }
        
    /**
     * Creation of the login form
     *
     * @return string form contact in HTML
     */
    public function getFormLogin(): string
    {
        $fields = $this->formLogin->addInputText('email', 'email', 'Votre adresse e-mail', 'email', true);
        $fields .= $this->formLogin->addInputText('password', 'password', 'Votre mot de passe', 'password', true);
        $fields .= $this->formLogin->addButton('Se connecter', 'margin-btn-form');

        return $this->formLogin->createForm($fields);
    }
}
