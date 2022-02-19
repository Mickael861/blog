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

        $this->datas['user_session'] = $this->session::getSession();
        
        $this->datas['title'] = $this->title;
        $this->datas['view'] = $this->view;

        $this->datas_post = $this->controleDatas($datas['POST']);
        $this->datas_get = $this->controleDatas($datas['GET']);
        $this->datas_match = $this->controleDatas($datas['match']);

        $this->getSuccessUserAccount();
        $this->manageSessionRedirects();
    }
    
    /**
     * controls input data and secures it
     *
     * @param  array $datas datas
     * @return array Controlled data
     */
    private function controleDatas($datas)
    {
        $datas_controle = array();

        foreach ($datas as $key => $data) {
            $datas_controle[$key] = htmlentities(trim($data));
        }

        return $datas_controle;
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
        if ($this->page === 1) {
            $this->datas['pagination_before'] = 'disabled';
        } elseif ($this->page < $this->nbrs_page - 1) {
            $this->datas['pagination_before'] = 'disabled';
        } elseif ($this->page > $this->nbrs_page - 1) {
            $this->datas['pagination_next'] = 'disabled';
        } else {
            $this->datas['pagination_next'] = 'disabled';
            $this->datas['pagination_before'] = 'disabled';
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

    /**
     * Manage account errors
     *
     * @return void
     */
    private function getSuccessUserAccount(): void
    {
        if (!empty($this->datas_get['login'])) {
            $this->datas['success'] = 'Bienvenue ' . $this->datas['user_session']['user_pseudo'];
        }

        if (!empty($this->datas_get['signup'])) {
            $this->datas['success'] = 'Compte crée avec succés et en attente d\'acceptation';
        }

        if (!empty($this->datas_get['logout']) && $this->session::sessionIsStart()) {
            $this->datas['success'] = 'Déconnexion réussi !';
            unset($this->datas['user_session']);
            $this->session::sessionDestroy();
        }

        if (!empty($this->datas_get['sendmail'])) {
            $this->datas['success_send_mail'] = 'l\'E-mail a été correctement envoyé';
        }

        if (!empty($this->datas_get['create'])) {
            $this->datas['success'] = 'Article crée avec succés';
        }

        if (!empty($this->datas_get['update'])) {
            $this->datas['success'] = 'Article modifié avec succés';
        }

        if (!empty($this->datas_get['successDelete'])) {
            $this->datas['success'] = 'Article supprimé avec succés';
        }
    }
}
