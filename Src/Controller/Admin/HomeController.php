<?php
namespace App\Controller\Admin;

use App\Controller\Controller;
use App\Model\CommentsModel;
use App\Model\PostsModel;
use App\Model\UserModel;

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
        $modelUsers = new UserModel;
        $modelPosts = new PostsModel;
        $this->datas['count_comments'] = $modelComments->countItems();
        $this->datas['count_users'] = $modelUsers->countItems();
        $this->datas['count_posts'] = $modelPosts->countItems();

        $this->datas['count_email_send'] = '+ ' . $this->getNbrsEmailSend();

        $this->datas['today'] = date('Y-m-d');

        echo $this->viewsRender($this->view, $this->datas, $this->folder);
    }
    
    /**
     * Returns the number of emails sent
     *
     * @return int
     */
    private function getNbrsEmailSend(): int
    {
        $nbrs_email_send = 0;
        $path_file = dirname(__DIR__, 3) . '/counter/send_mail.txt';
        if (file_exists($path_file)) {
            fopen($path_file, 'a');
            $nbrs_email_send = (int) file_get_contents($path_file);
        }

        return $nbrs_email_send;
    }
}
