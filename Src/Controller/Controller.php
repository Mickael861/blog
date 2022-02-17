<?php
namespace App\Controller;

use App\Core\Access;
use App\Core\Router;
use DateTime;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Controller
{
        
    /**
     * @var array
     */
    protected $datas = array();

    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var string
     */
    protected $view = '';

    /**
     * @var array
     */
    protected $admin_access = false;
    
    /**
     * important data initialization
     *
     * @return void
     */
    protected function init(array $datas)
    {
        $this->session = new Access;
        $this->session::startSession();

        $this->datas['user_session'] = $this->session::getSession();
        
        $this->datas['title'] = $this->title;
        $this->datas['view'] = $this->view;

        $this->datasPost = empty($datas['POST']) ? array() : $datas['POST'];
        $this->datasGet = empty($datas['GET']) ? array() : $datas['GET'];
        $this->paramsUrl = empty($datas['URL']) ? array() : $datas['URL'];

        if ($this->admin_access && !$this->session::userIsAdmin()) {
            require_once dirname(__DIR__, 2) . '/views/error404.twig';
            exit;
        }
    }

    /**
     * Returns the rendering of a view
     *
     * @param  string $view name of the view
     * @param  array $datas the datas to be sent to the view
     * @param  string $folder name of the folder
     * @return string content of the view
     */
    protected static function viewsRender(string $view, array $datas, string $folder = 'utilisateurs'): string
    {
        $path_views = dirname(__DIR__, 2) . '/views/' . $folder;

        $loader = new FilesystemLoader($path_views);

        $twig = new Environment($loader);

        $view = $view . '.twig';

        return $twig->render($view, $datas);
    }
}
