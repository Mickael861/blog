<?php
namespace App\Controller\Admin;

use App\Controller\Controller;
use App\Model\UserModel;

class AccountsController extends Controller
{

    /**
     * @var string
     */
    protected $title = 'Gestion des comptes utilisateurs';

    /**
     * @var string
     */
    protected $view = 'accounts';

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
    public function accountsAction(array $datas = array()): void
    {
        $this->init($datas);

        $this->userModel = new UserModel;
        $this->page = empty($this->datas_match['page']) ? 1 : (int) $this->datas_match['page'];
        $this->datas['page'] = $this->page;
        $filters = array();

        foreach ($this->datas_post as $key => $post) {
            if (!empty($this->datas_post[$key])) {
                $column = explode('_', $key)[0];
                $filters[$post] = $column;
                $this->datas[$key] = $post;
            }
        }

        $this->getNbrsItems('valider');
        $this->getNbrsItems('refuser');
        $this->getNbrsItems('en_attente');

        $accounts = $this->userModel->fetchAll(true, 'user_id', $this->page, $filters, 'DESC');

        if (!empty($accounts)) {
            $this->nbrs_page = $this->userModel->getNbrsPage();
            $this->disabledPagination();
            $this->datas['nbrs_page'] = $this->nbrs_page;
    
            foreach ($accounts as &$account) {
                if ($account->statut === 'valider') {
                    $account->color_statut = '#52BE80';
                    $account->statut = 'Valider';
                } elseif ($account->statut === 'refuser') {
                    $account->color_statut = '#EC7063';
                    $account->statut = 'Refuser';
                } else {
                    $account->color_statut = '#F5B041';
                    $account->statut = 'En attente';
                }
            }

            if (!empty($this->datas_get['valide'])) {
                $datas_save['statut'] = 'valider';
                $this->userModel->save($datas_save, (int) $this->datas_get['valide']);
                $_SESSION['success'] = 'Compte accepté';
                header('Location: /admin/accounts/' . $this->page);
                exit();
            }

            if (!empty($this->datas_get['refuse'])) {
                $datas_save['statut'] = 'refuser';
                $this->userModel->save($datas_save, $this->datas_get['refuse']);
                $_SESSION['success'] = 'Compte refusé';
                header('Location: /admin/accounts/' . $this->page);
                exit();
            }

            $this->datas['accounts'] = $accounts;
        } else {
            $this->datas['errors'] = 'Aucun compte trouvé';
        }

        echo $this->viewsRender($this->view, $this->datas, $this->folder);
    }
    
    /**
     * Count the number of elements
     *
     * @param  string $statut statut of item
     * @return void
     */
    private function getNbrsItems($statut)
    {
        $this->datas['accounts_' . $statut] = '+ ' . sizeof($this->userModel->getAllWithParams(array(
            'statut' => $statut
        )));
    }
}
