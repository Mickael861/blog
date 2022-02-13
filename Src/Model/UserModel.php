<?php
namespace App\Model;

use PDO;

class UserModel extends Model
{
    /**
     * @var string
     */
    protected $table = 'utilisateurs';

    /**
     * @var string
     */
    protected $primary_key = 'utilisateur_id';

    /**
     * @var string
     */
    protected $class = __CLASS__;
    
    /**
     * @var array
     */
    protected $fields = array(
        'utilisateur_id' => array(
            'name' => 'L\'identifiant de l\'utilisateur',
            'type' => 'int'
        ),
        'role' => array(
            'name' => 'Le rôle de l\'utilisateur',
            'type' => 'string',
            'required' => true
        ),
        'pseudo' => array(
            'name' => 'Le pseudo de l\'utilisateur',
            'type' => 'string',
            'required' => true
        ),
        'prenom' => array(
            'name' => 'Le prénom de l\'utilisateur',
            'type' => 'string',
            'required' => true
        ),
        'nom' => array(
            'name' => 'Le nom de l\'utilisateur',
            'type' => 'string',
            'required' => true
        ),
        'email' => array(
            'name' => 'L\'e-mail de de l\'utilisateur',
            'type' => 'string',
            'required' => true
        ),
        'password' => array(
            'name' => 'Le mot de passe de l\'utilisateur',
            'type' => 'string',
            'required' => true
        ),
        'statut' => array(
            'name' => 'Le statut de l\'article',
            'type' => 'string',
            'required' => true
        ),
        'date_add' => array(
            'name' => 'La date d\'ajout',
            'type' => 'date',
            'required' => true
        ),
        'user_add' => array(
            'name' => 'l\'identifiant de l\'utilisateur',
            'type' => 'int'
        ),
        'date_upd' => array(
            'name' => 'La date de modification',
            'type' => 'date'
        ),
        'user_upd' => array(
            'name' => 'l\'identifiant de l\'utilisateur',
            'type' => 'int'
        )
    );
    
    /**
     * Check that the username and password do not already exist
     *
     * @param  string $pseudo
     * @param  string $email
     * @return ?array if there is a match return errors, otherwise false
     */
    public function getErrorsSameDatas(string $pseudo, string $email)
    {
        $sameDatas = array();
        $samePseudo = $this->getSamePseudo($pseudo);
        if ($samePseudo) {
            $sameDatas[] = sprintf('Le pseudo "%s" éxiste dêjà', $pseudo);
        }

        $sameEmail = $this->getSameEmail($email);
        if ($sameEmail) {
            $sameDatas[] = sprintf('L\'e-mail "%s" éxiste dêjà', $email);
        }

        if (!empty($sameDatas)) {
            return implode('</br>', $sameDatas);
        }

        return false;
    }
    
    /**
     * Get same pseudo
     *
     * @param  string $pseudo Pseudo of form
     * @return bool If a pseudo is found return true otherwise false
     */
    private function getSamePseudo(string $pseudo)
    {
        $query_pseudo = 'SELECT utilisateur_id FROM ' . $this->table .
        ' WHERE pseudo = :pseudo';

        $params = array(
            'pseudo' => $pseudo
        );
        
        return !empty($this->request($query_pseudo, $params)->fetchAll(PDO::FETCH_CLASS, $this->class)) ? true : false;
    }
    
    /**
     * Get same email
     *
     * @param  string $email Email of form
     * @return bool If a email is found return true otherwise false
     */
    private function getSameEmail(string $email)
    {
        $query_email = 'SELECT utilisateur_id FROM ' . $this->table .
        ' WHERE email = :email';

        $params = array(
            'email' => $email
        );
        
        return !empty($this->request($query_email, $params)->fetchAll(PDO::FETCH_CLASS, $this->class)) ? true : false;
    }
}
