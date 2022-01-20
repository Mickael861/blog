<?php
namespace App\Controller;

use App\Utils\Form;

class HomeController extends Controller
{
    private $title = 'Accueil';
    private $view = 'home';
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
            "content" => 'Message'
        );

        $formContactHome = new Form('/home', 'POST', $datasPost);
        //verification form data
        $is_valide = $formContactHome->verifDatasForm($datasContactExpected);
        if ($is_valide) {
            //TODO Envoie du mail
            $this->datas['success_contact'] = 'L\'email a été envoyé avec succes';
        }
        
        //Add datas contact form
        $formContact = $this->getFormContact($formContactHome);
        $this->datas['formContactHome'] = $formContact;

        echo parent::viewsRender($this->view, $this->datas);
    }
        
    /**
     * Creation of the contact form
     *
     * @param  Objet $formContact Objet of the Form
     * @return string
     */
    public function getFormContact(Form $formContactHome): string
    {
        $fields = $formContactHome->addInputText('first_name', 'first_name', 'Prénom', true);
        $fields .= $formContactHome->addInputText('last_name', 'last_name', 'Nom', true);
        $fields .= $formContactHome->addInputText('email', 'email', 'Adresse e-mail', true);
        $fields .= $formContactHome->addTextArea('content', 'content', 'Message', true);
        $fields .= $formContactHome->addButton();

        return $formContactHome->createForm($fields);
    }
}
