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
        
        $this->addStatusManagement($this->userModel);

        $this->accountsManagement();

        echo $this->viewsRender($this->view, $this->datas, $this->folder);
    }
    
    /**
     * Accounts management
     *
     * @return void
     */
    private function accountsManagement(): void
    {
        $this->page = empty($this->datas_match['page']) ? 1 : (int) $this->datas_match['page'];
        $this->datas['page'] = $this->page;

        $accounts = $this->userModel->fetchAll(true, 'user_id', $this->page, $this->filters, 'DESC');
        if (!empty($accounts)) {
            $this->addDatasNbrsPages($this->userModel);
    
            foreach ($accounts as $account) {
                $this->addDatasStatutItem($account);
            }

            $this->datas['accounts'] = $accounts;
            
            $this->saveValideAccount();

            $this->saveRefusAccount();
        } else {
            $this->datas['errors'] = 'Aucun compte trouvé';
        }
    }

    /**
     * save refus account
     *
     * @return void
     */
    private function saveRefusAccount(): void
    {
        if (!empty($this->datas_get['refuse'])) {
            $datas_save['statut'] = 'refuser';
            $this->userModel->save($datas_save, $this->datas_get['refuse']);
            $_SESSION['success'] = 'Compte refusé';

            header('Location: /admin/accounts/' . $this->page);
            exit();
        }
    }
    
    /**
     * save valide account
     *
     * @return void
     */
    private function saveValideAccount(): void
    {
        if (!empty($this->datas_get['valide'])) {
            $datas_save['statut'] = 'valider';
            $this->userModel->save($datas_save, (int) $this->datas_get['valide']);
            $_SESSION['success'] = 'Compte accepté';
            
            header('Location: /admin/accounts/' . $this->page);
            exit();
        }
    }
}
