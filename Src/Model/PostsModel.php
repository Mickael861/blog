<?php
namespace App\Model;

class PostsModel extends Model
{
    /**
     * @var string
     */
    protected $table = 'posts';

    /**
     * @var string
     */
    protected $primary_key = 'post_id';

    /**
     * @var string
     */
    protected $class = __CLASS__;
    
    /**
     * @var array
     */
    protected $fields = array(
        'post_id' => array(
            'name' => 'L\'identifiant de l\'article',
            'type' => 'int'
        ),
        'user_id' => array(
            'name' => 'L\'identifiant de l\'utilisateur',
            'type' => 'int',
            'required' => true
        ),
        'title' => array(
            'name' => 'Le titre de l\'article',
            'type' => 'string',
            'sizemax' => 50,
            'required' => true
        ),
        'slug' => array(
            'name' => 'Le slug de l\'article',
            'type' => 'string',
            'required' => true
        ),
        'chapo' => array(
            'name' => 'Le chapô  de l\'article',
            'type' => 'string',
            'required' => true
        ),
        'content' => array(
            'name' => 'Le contenu de l\'article',
            'type' => 'string',
            'required' => true
        ),
        'author_id' => array(
            'name' => 'L\'identifiant de l\'auteur',
            'type' => 'int',
            'required' => true
        ),
        'statut' => array(
            'name' => 'Le statut de l\'article',
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
     * @var int
     */
    protected $nbrs_page = 0;
}
