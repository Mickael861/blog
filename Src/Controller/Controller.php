<?php
namespace App\Controller;

use App\Core\Router;
use DateTime;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Controller
{
        
    /**
     *
     * @var array
     */
    protected $datas = array();
    
    /**
     * important data initialization
     *
     * @return void
     */
    protected function init(array $datas, $with_access = false)
    {
        $user_session = !empty($_SESSION['utilisateur_id']) ? $_SESSION : array();
        $this->datas['user_session'] = $user_session;
        $this->datas['title'] = $this->title;
        $this->datas['view'] = $this->view;

        $this->datasPost = empty($datas['POST']) ? array() : $datas['POST'];
        $this->datasGet = empty($datas['GET']) ? array() : $datas['GET'];
        $this->paramsUrl = empty($datas['URL']) ? array() : $datas['URL'];

        if ($with_access) {
            $this->getAccessUser();
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

    private function getAccessUser()
    {
        if (!empty($this->datas['user_session']['utilisateur_id']) && $this->datas['user_session']['role'] !== 'admin') {
            header('Location: /');
            exit();
        }
    }
}
