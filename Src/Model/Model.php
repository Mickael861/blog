<?php
namespace App\Model;

use App\Core\Connexion;
use App\Exception\ModelException;
use PDO;
use PDOStatement;

abstract class Model
{

    /**
     * @var string
     */
    protected $table = '';
    
    /**
     * @var string
     */
    protected $primary_key = '';

    /**
     * @var string
     */
    protected $class = '';
    
    /**
     * @var array
     */
    protected $fields = array();
    
    /**
     * @var array
     */
    protected $errors = array();

    /**
     * @var int
     */
    protected $nbrs_page = 0;
    
    /**
     * save items
     *
     * @param  mixed $params parameter to save
     * @param  mixed $item_id item ID
     * @return bool returns false on error, true otherwise
     */
    public function save($params, $item_id = 0): bool
    {
        $this->dataVerification($params);

        if (empty($this->errors)) {
            if (!empty($item_id)) {
                $is_save = $this->update($params, $item_id);
            } else {
                $is_save = $this->create($params);
            }

            return $is_save;
        }

        return false;
    }
    
    /**
     * update item
     *
     * @param  array $params parameter to save
     * @param  int $item_id item ID
     * @return bool false backup failed, true otherwise
     */
    private function update(array $params, int $item_id)
    {
        foreach ($params as $field => $value) {
            $str_params[] = $field . ' = :' . $field;
        }

        $params['post_id'] = $item_id;

        $query = 'UPDATE ' . $this->table . ' SET ';
        $query .= implode(', ', $str_params);
        $query .= ' WHERE ' . $this->primary_key . ' = :' . $this->primary_key;

        $result = self::request($query, $params);

        if (empty($result)) {
            return false;
        }

        return true;
    }
    
    /**
     * create items
     *
     * @param  array $params parameter to save
     * @return bool false backup failed, true otherwise
     */
    private function create(array $params): bool
    {
        foreach ($params as $key => $param) {
            $fieldsExpected[] = $key;
            $valuesExpected[] = '"' . $param . '"';
        }

        $fields = implode(', ', $fieldsExpected);
        $valuesExpected = implode(', ', $valuesExpected);

        $query = 'INSERT INTO ' . $this->table . ' (' . $fields . ') VALUES (' . $valuesExpected . ')';

        $result = self::request($query);

        if (empty($result)) {
            return false;
        }

        return true;
    }
    
    /**
     * delete item
     *
     * @param  int $item_id item ID
     * @return bool false element not deleted, true otherwise
     */
    public function delete(int $item_id)
    {
        $query = 'DELETE FROM ' . $this->table . ' WHERE ' . $this->primary_key . ' = :' . $this->primary_key;

        $params[$this->primary_key] = $item_id;

        $result = self::request($query, $params);

        if (empty($result)) {
            return false;
        }

        return true;
    }
    
    /**
     * Retrieve an element using its identifier
     *
     * @param  int $item_id identifier sought
     * @return array|bool item
     */
    public function fetchId(int $item_id)
    {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE ';

        $query .= $this->primary_key . ' = :' . $this->primary_key;

        $params = array(
            $this->primary_key => $item_id
        );

        return self::request($query, $params)->fetch();
    }
    
    /**
     * retrieve items with parameters
     *
     * @param  array $params parameter of the were clause
     * @return array item
     */
    public function getAllWithParams(array $params): array
    {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE ';

        foreach ($params as $field => $value) {
            $str_params[] = $field . ' = :' . $field;
        }
        
        $query .= implode(' AND ', $str_params);

        return self::request($query, $params)->fetchAll(PDO::FETCH_CLASS, $this->class);
    }
    
        
    /**
     * returns all elements of a table
     *
     * @param  mixed $total total article for pagination
     * @param  mixed $field sort by which field
     * @param  mixed $page The current page
     * @param  mixed $order sort order
     * @param  mixed $item_per_page Number of items per page
     * @return array|bool the items or false if it finds an error
     */
    public function fetchAll(bool $total, string $field, int $page, string $order = 'DESC', $item_per_page = 20)
    {
        if ($total) {
            if ($page < 1) {
                $this->errors['page'] = 'La page demandée est inférieure aux nombres de page';

                return false;
            }

            $query = 'SELECT count(*) FROM ' . $this->table;

            $nbrs_items = (int) self::request($query)->fetch()[0];

            $this->nbrs_page = (int) ceil($nbrs_items / $item_per_page);

            if ($page > $this->nbrs_page) {
                $this->errors['page'] = 'La page demandée est supérieure aux nombres de page';

                return false;
            }

            $page_view = ($page - 1) *  $item_per_page;
            
            $query = 'SELECT * FROM ' . $this->table . ' ORDER BY ' . $field . ' ' . $order .
                ' LIMIT ' . $page_view . ',' . $item_per_page;
        } else {
            $query = 'SELECT * FROM ' . $this->table;
        }

        return self::request($query)->fetchAll(PDO::FETCH_CLASS, $this->class);
    }
    
    /**
     * Check datas
     *
     * @param  array $params parameter to save
     * @return void
     */
    private function dataVerification($params): void
    {
        foreach ($params as $field => $param) {
            if (key_exists($field, $this->fields)) {
                if (!empty($this->fields[$field]['required'])) {
                    if (empty($params[$field])) {
                        $this->errors[$field] = sprintf('"%s" est obligatoire', $this->fields[$field]['name']);
                    }
                }

                if (!empty($this->fields[$field]['sizemax'])) {
                    if (strlen($params[$field]) > $this->fields[$field]['sizemax']) {
                        $this->errors[$field] = sprintf(
                            '"%s" doit être inférieur ou égal à %s caractéres',
                            $this->fields[$field]['name'],
                            $this->fields[$field]['sizemax']
                        );
                    }
                }

                if (!empty($this->fields[$field]['type'])) {
                    if ($this->fields[$field]['type'] === 'int') {
                        if (!is_int($params[$field])) {
                            $this->errors[$field] = sprintf(
                                '"%s" doit être un entier',
                                $this->fields[$field]['name']
                            );
                        }
                    }
                        
                    if ($this->fields[$field]['type'] === 'date') {
                        if (!preg_match("#^[0-9]{4}-[0-9]{2}-[0-9]{2}$#", $params[$field])) {
                            $this->errors[$field] = sprintf(
                                '"%s" doit être un type date "YYYY-mm-dd"',
                                $this->fields[$field]['name']
                            );
                        }
                    }
                }
            }
        }
    }

    /**
     * Execute requests
     *
     * @param  string $query SQL request
     * @param  array $params parameters
     * @return PDOStatement instance of PDOStatement
     */
    protected static function request(string $query, array $params = []): PDOStatement
    {
        $pdo = new Connexion;

        if (!empty($params)) {
            $query = $pdo->prepare($query);
            $query->execute($params);

            return $query;
        }

        return $pdo->query($query);
    }
    
    /**
     * Get errors
     *
     * @return array error table
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get numbers of page
     *
     * @return int numbers of page
     */
    public function getNbrsPage(): int
    {
        return $this->nbrs_page;
    }
}
