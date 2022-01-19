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
            "last_name",
            "first_name",
            "email",
            "content"
        );

        //Add datas contact form
        $formContact = $this->getFormContact();
        $this->datas['formContact'] = $formContact;

        //Add datas form contact
        $is_good = parent::verifDatasPost($datasPost, $datasContactExpected);

        if(!$is_good) {
            //Add contact form errors to data
            $this->datas['form_errors'] = parent::getErrors();
        }
        
        echo parent::viewsRender($this->view, $this->datas);
    }
    
    /**
     * Creation of the contact form
     *
     * @return string
     */
    public function getFormContact()
    {
        $formContact = new Form('/home', 'POST');
        
        $form = $formContact->addInputText('last_name', 'last_name', 'Prénom', true);
        $form .= $formContact->addInputText('first_name', 'first_name', 'Nom', true);
        $form .= $formContact->addInputText('email', 'email', 'Adresse e-mail', true);
        $form .= $formContact->addTextArea('content', 'content', 'Message', true);
        $form .= $formContact->addButton();

        return $formContact->createForm($form);
    }
}
