<?php
namespace App\Model;

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
}
