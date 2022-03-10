<?php
namespace App\Controller\Admin;

use App\Controller\Controller;
use App\Model\UserModel;
use App\Utils\Utils;

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
        
        $this->addStatutWaiting();

        $accounts = $this->userModel->fetchAll(true, 'user_id', $this->page, $this->filters, 'DESC');
        if (!empty($accounts)) {
            $this->addDatasNbrsPages($this->userModel);

            foreach ($accounts as &$account) {
                $this->addBadgeNewItems($account);

                $this->addDatasStatutItem($account);

                $account->date_add = (new Utils())::dbToDate($account->date_add);
            }

            $this->datas['today'] = date('Y-m-d');

            $this->datas['accounts'] = $accounts;
            
            $this->changeStatusItem($this->userModel, array(
                'accept' => 'Compte accepté',
                'refus' => 'Compte refusé'
            ));
        } else {
            $this->datas['errors'] = 'Aucun compte trouvé';
        }
    }
}
