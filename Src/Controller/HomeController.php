<?php
namespace App\Controller;

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
        $this->datas['title'] = $this->title;

        $datasPost = empty($datas['POST']) ? array() : $datas['POST'];
        $keysExpected = array(
            "last_name",
            "first_name",
            "email",
            "content"
        );
        $is_good = parent::verifDatasPost($datasPost, $keysExpected);

        if(!$is_good) {
            $this->datas['form_errors'] = parent::getErrors();
        } else {
        }
        
        echo parent::viewsRender($this->view, $this->datas);
    }
}
