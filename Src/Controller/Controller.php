<?php
namespace App\Controller;

use App\Core\Access;
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
     * @var string
     */
    protected $no_access_session = false;
    
    /**
     * important data initialization
     *
     * @return void
     */
    protected function init(array $datas): void
    {
        $this->session = new Access;
        $this->session::startSession();

        $this->datas['user_session'] = $this->session::getSession();
        
        $this->datas['title'] = $this->title;
        $this->datas['view'] = $this->view;

        $this->datasPost = empty($datas['POST']) ? array() : $datas['POST'];

        $this->datasGet = empty($datas['GET']) ? array() : $datas['GET'];

        $this->paramsUrl = empty($datas['URL']) ? array() : $datas['URL'];

        $this->manageSessionRedirects();
    }
    
    /**
     * Manage session redirects
     *
     * @return void
     */
    private function manageSessionRedirects(): void
    {
        if ($this->no_access_session) {
            if ($this->session::sessionIsStart()) {
                header('Location: /');
                exit();
            }
        }

        if ($this->admin_access && !$this->session::userIsAdmin()) {
            require_once dirname(__DIR__, 2) . '/views/error404.twig';
            exit;
        }
    }

    /**
     * Handles next and before pagination
     *
     * @return void
     */
    protected function disabledPagination()
    {
        $this->datas['pagination_next'] = '';
        $this->datas['pagination_before'] = '';

        if ($this->nbrs_page === 1) {
            $this->datas['pagination_next'] = 'disabled';
            $this->datas['pagination_before'] = 'disabled';
        } elseif ($this->page < $this->nbrs_page - 1) {
            $this->datas['pagination_before'] = 'disabled';
        } elseif ($this->page > $this->nbrs_page - 1) {
            $this->datas['pagination_next'] = 'disabled';
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
