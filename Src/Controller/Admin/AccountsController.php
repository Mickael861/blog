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
        $this->addDatasPages();

        $accounts = $this->userModel->fetchAll(true, 'user_id', $this->page, $this->filters, 'DESC');
        if (!empty($accounts)) {
            $this->addDatasNbrsPages($this->userModel);
    
            foreach ($accounts as $account) {
                $this->addDatasStatutItem($account);
            }

            $this->datas['accounts'] = $accounts;
            
            $this->addSaveAccount($this->userModel);
        } else {
            $this->datas['errors'] = 'Aucun compte trouvé';
        }
    }
}
