<?php
namespace App\Controller\Utilisateurs;

use App\Utils\Form;
use App\Controller\Controller;
use App\Utils\PhpMailer;

class HomeController extends Controller
{

    /**
     * @var string
     */
    protected $title = 'Accueil';

    /**
     * @var string
     */
    protected $view = 'home';

    /**
     * view of action
     *
     * @param array Datas POST|GET
     * @return void
     */
    public function homeAction(array $datas = array()): void
    {
        $this->init($datas);

        $this->emailManagement();

        $this->logout();
        
        $this->addFormContact();
        
        echo $this->viewsRender($this->view, $this->datas);
    }
    
    /**
     * Add the form to the view
     *
     * @return void
     */
    private function addFormContact(): void
    {
        $formContact = $this->getFormContact($this->formContactHome);
        $this->datas['formContactHome'] = $formContact;
    }
    
    /**
     * email management
     *
     * @return void
     */
    private function emailManagement(): void
    {
        $this->formContactHome = new Form('/#contact_form', 'POST', $this->datas_post, true);
        
        $datasContactExpected = array(
            "first_name" => 'Prénom',
            "last_name" => 'Nom',
            "email" => 'Adresse e-mail',
            "subject" => "Objet",
            "message" => 'Message'
        );

        $is_valide = $this->formContactHome->verifDatasForm($datasContactExpected);

        if ($is_valide) {
            $is_send = $this->addMail();
            if ($is_send) {
                $this->utils::setSuccessSession('L\'e-mail a été correctement envoyé');
                $this->utils::redirect("/#");
            } else {
                $this->datas['errors_send_mail'] = 'Le serveur d\'envoi d\'email est indisponible';
            }
        }
    }
    
    /**
     * Manage disconnection
     *
     * @return void
     */
    private function logout():void
    {
        if (!empty($this->datas_get['logout'])) {
            if ($this->session::sessionIsStart()) {
                unset($this->datas['user_session']);
                $this->session::sessionDestroy();
            }

            $this->datas['success'] = 'Déconnexion réussi';
        }
    }
    
    /**
     * Add contact email
     *
     * @return bool true, if the sending of the contact email was successful, false otherwise
     */
    private function addMail(): bool
    {
        $mailer = new PhpMailer(true);
        
        $datas_mail = array(
            'FromMail' => $this->datas_post['email'],
            'ToMail' => 'mickael.sayer.dev@gmail.com',
            'Subject' => 'Réponse automatique',
            'Body' => $this->datas_post['message']
        );
        $is_send = $mailer->addDatasMail($datas_mail);
        if ($is_send) {
            $datas_mail = array(
                'FromMail' => 'mickael.sayer.dev@gmail.com',
                'ToMail' => $this->datas_post['email'],
                'Subject' => $this->datas_post['subject'],
                'Body' => 'Votre message sur le site "blog" à correctement était envoyé le ' . date('d-m-Y à H:m:s')
            );
            $mailer->addDatasMail($datas_mail);
        }
        
        return $is_send;
    }
        
    /**
     * Creation of the contact form
     *
     * @return string form contact in HTML
     */
    public function getFormContact(): string
    {
        $fields = $this->formContactHome->addInputText('first_name', 'first_name', 'Prénom', 'text', true);
        $fields .= $this->formContactHome->addInputText('last_name', 'last_name', 'Nom', 'text', true);
        $fields .= $this->formContactHome->addInputText('email', 'email', 'Adresse e-mail', 'email', true);
        $fields .= $this->formContactHome->addInputText('subject', 'subject', 'Objet', 'text', true);
        $fields .= $this->formContactHome->addTextArea('message', 'message', 'Message', true);
        $fields .= $this->formContactHome->addButton('Envoyer', 'margin-btn-form');

        return $this->formContactHome->createForm($fields);
    }
}
