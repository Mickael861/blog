<?php
namespace App\Controller\Admin;

use App\Controller\Controller;
use App\Model\CommentsModel;

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
     * @var bool
     */
    protected $admin_access = true;

    /**
     * @var string
     */
    private $folder = 'admin';

    /**
     * view of action
     *
     * @param array Datas POST|GET
     * @return void
     */
    public function homeAction(array $datas = array()): void
    {
        $this->init($datas);

        $modelComments = new CommentsModel;
        $this->datas['count_comments'] = $modelComments->countComments();

        $this->datas['today'] = date('Y-m-d');

        echo $this->viewsRender($this->view, $this->datas, $this->folder);
    }
}
