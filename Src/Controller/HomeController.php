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
        $datasPost = empty($datas['POST']) ? array() : $datas['POST'];

        $this->datas = array(
            'title' => $this->title
        );

        echo parent::viewsRender($this->view, $this->datas);
    }
}
