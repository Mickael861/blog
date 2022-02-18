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
        
        $this->getSuccessUserAccount();
        
        //Contact form fields
        $datasContactExpected = array(
            "first_name" => 'Prénom',
            "last_name" => 'Nom',
            "email" => 'Adresse e-mail',
            "subject" => "Objet",
            "message" => 'Message'
        );

        $action = '/#contact_form';
        $formContactHome = new Form($action, 'POST', $this->datas_post);
        //verification form data
        $is_valide = $formContactHome->verifDatasForm($datasContactExpected);
        if ($is_valide) {
            //Send Email
            $is_send = $this->addMail();
            if ($is_send) {
                header('Location: /?sendmail=1');
                exit();
            } else {
                $this->datas['errors_send_mail'] = sprintf('Envoie de l\'e-mail impossible, Veuillez vérifier que
                    votre adresse "%s" est correcte', $this->datas_post['email']);
            }
        }
        
        //Add datas contact form
        $formContact = $this->getFormContact($formContactHome);
        $this->datas['formContactHome'] = $formContact;
        
        echo $this->viewsRender($this->view, $this->datas);
    }
    
    /**
     * Manage account errors
     *
     * @return void
     */
    private function getSuccessUserAccount(): void
    {
        if (!empty($this->datas_get['login'])) {
            $this->datas['success'] = 'Bienvenue ' . $this->datas['user_session']['user_pseudo'];
        }

        if (!empty($this->datas_get['signup'])) {
            $this->datas['success'] = 'Compte crée avec succés et en attente d\'acceptation';
        }

        if (!empty($this->datas_get['logout']) && $this->session::sessionIsStart()) {
            $this->datas['success'] = 'Déconnexion réussi !';
            unset($this->datas['user_session']);
            $this->session::sessionDestroy();
        }

        if (!empty($this->datas_get['sendmail'])) {
            $this->datas['success_send_mail'] = 'l\'E-mail a été correctement envoyé';
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
    private function sendMailer($is_send = false): bool
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
