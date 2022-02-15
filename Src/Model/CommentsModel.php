<?php
namespace App\Model;

use PDO;

class CommentsModel extends Model
{
    /**
     * @var string
     */
    protected $table = 'commentaires';

    /**
     * @var string
     */
    protected $primary_key = 'commentaire_id';

    /**
     * @var string
     */
    protected $class = __CLASS__;
    
    /**
     * @var array
     */
    protected $fields = array(
        'commentaire_id' => array(
            'name' => 'L\'identifiant du commentaire',
            'type' => 'int'
        ),
        'post_id' => array(
            'name' => 'L\'identifiant du post',
            'type' => 'int',
            'required' => true
        ),
        'utilisateur_id' => array(
            'name' => 'L\'identifiant de l\'utilisateur',
            'type' => 'int',
            'required' => true
        ),
        'content' => array(
            'name' => 'Le contenu du commentaire',
            'type' => 'string',
            'required' => true
        ),
        'status' => array(
            'name' => 'Le status du commentaire',
            'type' => 'string',
            'required' => true
        ),
        'date_add' => array(
            'name' => 'La date d\'ajout',
            'type' => 'date',
            'required' => true
        )
    );
    
    /**
     * Retrieve comments with user information
     *
     * @param  int $post_id item id
     * @return obj comments
     */
    public function getCommentsUser(int $post_id)
    {
        $query = 'SELECT *, u.pseudo AS pseudo, c.date_add AS date_add FROM ' . $this->table . ' AS c'.
        ' LEFT JOIN utilisateurs AS u ON c.utilisateur_id = u.utilisateur_id' .
        ' WHERE c.post_id = :post_id AND c.statut = "valider"';
    
        $params = array(
            'post_id' => $post_id
        );
        
        return $this->request($query, $params)->fetchAll(PDO::FETCH_CLASS, $this->class);
    }
}
