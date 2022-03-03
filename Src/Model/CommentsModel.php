<?php
namespace App\Model;

use PDO;

class CommentsModel extends Model
{
    /**
     * @var string
     */
    protected $table = 'comments';

    /**
     * @var string
     */
    protected $primary_key = 'comment_id';

    /**
     * @var string
     */
    protected $class = __CLASS__;
    
    /**
     * @var array
     */
    protected $fields = array(
        'comment_id' => array(
            'name' => 'L\'identifiant du commentaire',
            'type' => 'int'
        ),
        'post_id' => array(
            'name' => 'L\'identifiant du post',
            'type' => 'int',
            'required' => true
        ),
        'user_id' => array(
            'name' => 'L\'identifiant de l\'utilisateur',
            'type' => 'int',
            'required' => true
        ),
        'content' => array(
            'name' => 'Le contenu du commentaire',
            'type' => 'string',
            'required' => true
        ),
        'statut' => array(
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
        ' LEFT JOIN users AS u ON c.user_id = u.user_id' .
        ' WHERE c.post_id = :post_id AND c.statut = "valider"';
    
        $params = array(
            'post_id' => $post_id
        );
        
        return $this->request($query, $params)->fetchAll(PDO::FETCH_CLASS, $this->class);
    }

    /**
     * Counts the number of pending comments and new ones
     *
     * @return array A comment counter
     */
    public function countComments(): array
    {
        $query = 'SELECT count(*) AS comments_waiting FROM ' . $this->table . ' WHERE statut = "en_attente"';
        $results_count['comments_waiting'] = '+ ' . (int) $this->request($query)->fetch()['comments_waiting'];

        $results_count['new_comments'] = '+ ' . $this->getCountNewComments();

        return $results_count;
    }
    
    /**
     * Counts the new comments
     *
     * @return int numbers of new comments
     */
    public function getCountNewComments():int
    {
        $query = 'SELECT count(*) AS new_comments FROM ' . $this->table .
            ' WHERE statut <> "valider" AND statut != "refuser"' .
            ' AND date_add = "' . date('Y-m-d') . '"';

        return (int) $this->request($query)->fetch()['new_comments'];
    }
}
