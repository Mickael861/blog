<?php
namespace App\Controller;

use App\Core\Router;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Controller
{

    /**
     * important data initialization
     *
     * @return void
     */
    protected function init()
    {
        $user_session = !empty($_SESSION['utilisateur_id']) ? $_SESSION : array();
        $this->datas['user_session'] = $user_session;
        
        $this->datas['title'] = $this->title;
        $this->datas['view'] = $this->view;
    }

    /**
     * Returns the rendering of a view
     *
     * @param  string $view name of the view
     * @param  array $datas the datas to be sent to the view
     * @return string content of the view
     */
    public static function viewsRender(string $view, array $datas): string
    {
        $path_views = dirname(__DIR__, 2) . "/views";
        $loader = new FilesystemLoader($path_views);

        $twig = new Environment($loader);

        $view = $view . '.twig';

        return $twig->render($view, $datas);
    }
}
