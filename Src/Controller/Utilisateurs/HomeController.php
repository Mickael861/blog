<?php
namespace App\Controller\Utilisateurs;

use App\Controller\Controller;
use App\Utils\Form;
use PHPMailer\PHPMailer\Exception as PHPMailerException;
use PHPMailer\PHPMailer\PHPMailer;

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

        $this->formContactHome = new Form('/#contact_form', 'POST', $this->datas_post);

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
                $_SESSION['success'] = 'l\'E-mail a été correctement envoyé';
                header('Location: /');
                exit();
            } else {
                $this->datas['errors_send_mail'] = sprintf('Envoie de l\'e-mail impossible, Veuillez vérifier que
                    votre adresse "%s" est correcte', $this->datas_post['email']);
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
        $is_send = $this->sendMailer();
        if ($is_send) {
            $this->sendMailer(true);
        }

        return $is_send;
    }
    
    /**
     * Send contact email
     *
     * @param  bool $is_send true, if the contact email is sent, false otherwise
     * @return bool true, if the sending of the contact email was successful, false otherwise
     */
    private function sendMailer(bool $is_send = false): bool
    {
        $mailer = new PHPMailer(true);

        $from = $is_send ? 'mickael.sayer.dev@gmail.com' : $this->datas_post['email'];
        $to = !$is_send ? 'mickael.sayer.dev@gmail.com' : $this->datas_post['email'];
        $subject = $is_send ? 'Réponse automatique' : $this->datas_post['subject'];
        $messageAdmin = 'Votre message sur le site "blog" à correctement était envoyé le ' . date('Y-m-d à H:m:s');
        $body = $is_send ? $messageAdmin  : $this->datas_post['message'];
        
        try {
            //SMTP Setup
            $mailer->isSMTP();
            $mailer->Host = "localhost";
            $mailer->Port = 1025;

            //Charset
            $mailer->Charset = "UTF-8";

            //Expediteur
            $mailer->setFrom($from);

            //receiver
            $mailer->addAddress($to);

            //Content
            $mailer->Subject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
            $mailer->Body = wordwrap($body);

            //send
            $mailer->send();
        } catch (PHPMailerException $e) {
            return false;
        }

        return true;
    }
        
    /**
     * Creation of the contact form
     *
     * @param  Objet $formContact Objet of the Form
     * @return string form contact in HTML
     */
    public function getFormContact(Form $formContactHome): string
    {
        $fields = $formContactHome->addInputText('first_name', 'first_name', 'Prénom', true);
        $fields .= $formContactHome->addInputText('last_name', 'last_name', 'Nom', true);
        $fields .= $formContactHome->addInputText('email', 'email', 'Adresse e-mail', true);
        $fields .= $formContactHome->addInputText('subject', 'subject', 'Objet', true);
        $fields .= $formContactHome->addTextArea('message', 'message', 'Message', true);
        $fields .= $formContactHome->addButton('Envoyer', 'margin-btn-form');

        return $formContactHome->createForm($fields);
    }
}
