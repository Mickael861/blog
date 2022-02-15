<?php
namespace App\Controller\Admin;

use App\Controller\Controller;

class HomeController extends Controller
{

    /**
     * @var string
     */
    protected $title = 'Dashboard';

    /**
     * @var string
     */
    protected $view = 'home';

    
    /**
     * @var string
     */
    protected $folder = 'admin';

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
    public function homeAction(array $datas = array()): void
    {
        $this->init(true);

        //Datas POST
        $datasPost = empty($datas['POST']) ? array() : $datas['POST'];
        $datasGET = empty($datas['GET']) ? array() : $datas['GET'];

        dump($datasPost, $datasGET);

        echo $this->viewsRender($this->view, $this->datas, $this->folder);
    }
}
