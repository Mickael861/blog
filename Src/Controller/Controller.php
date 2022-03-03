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
    private $no_access_session = false;
    
    /**
     *
     * @var array
     */
    protected $datas_post = array();

    /**
     *
     * @var array
     */
    protected $datas_get = array();

    /**
     *
     * @var array
     */
    protected $datas_match = array();
    
    /**
     *
     * @var int
     */
    protected $page = 1;

    /**
     *
     * @var int
     */
    private $nbrs_page = 0;
    
    /**
     *
     * @var array
     */
    protected $filters = array();
    
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
            $datas_controle[$key] = trim(htmlspecialchars($data));
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
        
        if ($this->no_access_session && $this->session::sessionIsStart()) {
            header('Location: /');
            exit();
        }
        //pb
        if ($this->admin_access && !$this->session::userIsAdmin()) {
            $_SESSION['errors'] = 'Vous n\'avez pas accés à cette partie du blog';
            header('Location: /');
            exit();
        }
    }

    /**
     * Handles next and before pagination
     *
     * @return void
     */
    private function disabledPagination()
    {
        if ($this->nbrs_page === 1) {
            $this->datas['pagination_next'] = 'disabled';
            $this->datas['pagination_before'] = 'disabled';
        } elseif ($this->page === 1) {
            $this->datas['pagination_before'] = 'disabled';
        } elseif ($this->page > $this->nbrs_page - 1) {
            $this->datas['pagination_next'] = 'disabled';
        }
    }
    
    /**
     * Add datas numbers of page
     *
     * @param  objet $model
     * @return void
     */
    protected function addDatasNbrsPages($model)
    {
        $this->nbrs_page = $model->getNbrsPage();
        $this->disabledPagination();
        $this->datas['nbrs_page'] = $this->nbrs_page;
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
        if (!empty($_SESSION['success'])) {
            $this->datas['success'] = $_SESSION['success'];

            unset($_SESSION['success']);
        }

        if (!empty($_SESSION['errors'])) {
            $this->datas['errors'] = $_SESSION['errors'];

            unset($_SESSION['errors']);
        }
    }
    
    /**
     * Add to the item the status
     *
     * @param  objet $item
     * @return void
     */
    protected function addDatasStatutItem(&$item): void
    {
        if ($item->statut === 'valider') {
            $item->color_statut = '#52BE80';
            $item->statut = 'Valider';
        } elseif ($item->statut === 'refuser') {
            $item->color_statut = '#EC7063';
            $item->statut = 'Refuser';
        } else {
            $item->color_statut = '#F5B041';
            $item->statut = 'En attente';
        }
    }
    
    /**
     * Addition of statutes
     *
     * @param  objet $model
     * @return void
     */
    protected function addStatusManagement($model): void
    {
        $this->statusManagement($model);
    }
    
    /**
     * Status management
     *
     * @param  objet $model
     * @return void
     */
    private function statusManagement($model): void
    {
        $statut_expected = array(
            'valider',
            'refuser',
            'en_attente',
            date('Y-m-d'),
            'publier'
        );
        
        foreach ($this->datas_post as $key => $post) {
            if (in_array($post, $statut_expected) && !empty($this->datas_post[$key])) {
                $column = explode('_', $key)[0];

                if ($column === 'new' || $column === 'newPosts') {
                    $column = 'date_add';
                }
                $this->filters[$post] = $column;
                $this->datas[$key] = $post;
            }
        }
        
        $this->getNbrsItems('valider', $model);
        $this->getNbrsItems('refuser', $model);
        $this->getNbrsItems('en_attente', $model);
        $this->getNbrsItems('publier', $model);
        $this->getNbrsItems('new', $model);
        $this->getNbrsItems('newPosts', $model);
    }

    /**
     * Count the number of elements
     *
     * @param  string $statut statut of item
     * @param  objet $model
     * @return void
     */
    private function getNbrsItems(string $statut, $model): void
    {
        $filters = array(
            'statut' => $statut
        );
        if ($statut === 'new' || $statut === 'newPosts') {
            $filters = array(
                'date_add' => date('Y-m-d')
            );
            if ($statut === 'new') {
                $filters['statut'] = 'en_attente';
            }
        }
        
        $this->datas[$model->getTable() . '_' . $statut] = '+ ' . sizeof($model->getAllWithParams($filters));
    }
    
    /**
     * record new statuses
     *
     * @param  objet $model
     * @return void
     */
    protected function addSaveAccount($model): void
    {
        $this->saveRefusAccount($model);

        $this->saveValideAccount($model);
    }

    /**
     * save refus account
     *
     * @param  objet $model
     * @return void
     */
    private function saveRefusAccount($model): void
    {
        if (!empty($this->datas_get['refuse'])) {
            $datas_save['statut'] = 'refuser';
            $model->save($datas_save, $this->datas_get['refuse']);
            $_SESSION['success'] = 'Compte refusé';

            header('Location: /admin/accounts/' . $this->page);
            exit();
        }
    }
    
    /**
     * save valide account
     *
     * @param  objet $model
     * @return void
     */
    private function saveValideAccount($model): void
    {
        if (!empty($this->datas_get['valide'])) {
            $datas_save['statut'] = 'valider';
            $model->save($datas_save, (int) $this->datas_get['valide']);
            $_SESSION['success'] = 'Compte accepté';
            
            header('Location: /admin/accounts/' . $this->page);
            exit();
        }
    }
    
    /**
     * Add page data to data
     *
     * @return void
     */
    protected function addDatasPages()
    {
        $this->page = empty($this->datas_match['page']) ? 1 : (int) $this->datas_match['page'];
        $this->datas['page'] = $this->page;
    }
    
    /**
     * Indicates that the item is a new item
     *
     * @param  objet $item
     * @return void
     */
    protected function addBadgeNewItems($item)
    {
        $item->new = false;
        if ($item->date_add === date('Y-m-d') && $item->statut !== 'refuser' && $item->statut !== 'valider') {
            $item->new = true;
        }
    }
    
    /**
     * Add an additional condition for new items
     *
     * @return void
     */
    protected function addStatutWaiting()
    {
        if (!empty($this->datas_post['new'])) {
            $this->filters['en_attente'] = 'statut';
        }
    }
}
