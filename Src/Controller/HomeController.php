<?php
namespace App\Controller;

use App\Utils\Form;
use PHPMailer\PHPMailer\Exception as PHPMailerException;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class HomeController extends Controller
{

    /**
     * @var string
     */
    private $title = 'Accueil';

    /**
     * @var string
     */
    private $view = 'home';
        
    /**
     * @var array
     */
    private $datas = array();

    /**
     * view of action
     *
     * @param array Datas POST|GET
     * @return void
     */
    public function homeAction(array $datas = array()): void
    {
        //Title name view
        $this->datas['title'] = $this->title;

        //Datas POST
        $datasPost = empty($datas['POST']) ? array() : $datas['POST'];

        //Contact form fields
        $datasContactExpected = array(
            "first_name" => 'Prénom',
            "last_name" => 'Nom',
            "email" => 'Adresse e-mail',
            "subject" => "Objet",
            "message" => 'Message'
        );

        $formContactHome = new Form('/home/#contact_form', 'POST', $datasPost);
        //verification form data
        $is_valide = $formContactHome->verifDatasForm($datasContactExpected);
        if ($is_valide) {
            //Send Email
            $is_send = $this->addMail($datasPost);
            if ($is_send) {
                $this->datas['success_send_mail'] = 'L\'email a été envoyé avec succes';
            } else {
                $this->datas['errors_send_mail'] = sprintf('Envoie de l\'e-mail impossible, Veuillez vérifier que
                    votre adresse "%s" est correcte', $datasPost['email']);
            }
        }
        
        //Add datas contact form
        $formContact = $this->getFormContact($formContactHome);
        $this->datas['formContactHome'] = $formContact;

        echo parent::viewsRender($this->view, $this->datas);
    }
    
    /**
     * Add contact email
     *
     * @param  array Datas Get|POST
     * @return bool true, if the sending of the contact email was successful, false otherwise
     */
    private function addMail(array $datasPost): bool
    {
        $is_send = $this->sendMailer($datasPost);
        if ($is_send) {
            $this->sendMailer($datasPost, true);
        }

        return $is_send;
    }
    
    /**
     * Send contact email
     *
     * @param  array $datasPost Datas Get|POST
     * @param  bool $is_send true, if the contact email is sent, false otherwise
     * @return bool true, if the sending of the contact email was successful, false otherwise
     */
    private function sendMailer(array $datasPost, bool $is_send = false): bool
    {
        $mailer = new PHPMailer(true);

        $from = $is_send ? 'mickael.sayer.dev@gmail.com' : $datasPost['email'];
        $to = !$is_send ? 'mickael.sayer.dev@gmail.com' : $datasPost['email'];
        $subject = $is_send ? 'Réponse automatique' : $datasPost['subject'];
        $messageAdmin = 'Votre message sur le site "blog" à correctement était envoyé le ' . date('Y-m-d à H:m:s');
        $body = $is_send ? $messageAdmin  : $datasPost['message'];
        
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
        $fields .= $formContactHome->addButton();

        return $formContactHome->createForm($fields, 'form_contact');
    }
}
