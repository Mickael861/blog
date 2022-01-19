<?php
namespace App\Controller;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Controller
{    
    
    /**
     * Error table
     *
     * @var array
     */
    protected $errors;

    /**
     * Returns the rendering of a view
     *
     * @param  string $view name of the view
     * @param  array $datas the datas to be sent to the view
     * @return string content of the view
     */
    public static function viewsRender(string $view, array $datas): string
    {
        $path_views = dirname(__DIR__, 2) . "/views";
        $loader = new FilesystemLoader($path_views);

        $twig = new Environment($loader);

        $view = $view . '.twig';

        return $twig->render($view, $datas);
    }

    /**
     * Verification of data passed in POST
     *
     * @param  array $datasPost Data retrieved in POST
     * @param  array $keysExpected Expected datas
     * @return array|bool un tableau de données, false si un champ est manquant
     */
    protected function verifDatasPost(array $datasPost, array $keysExpected)
    {
        $datasForm = array();
        $errors = array();

        $datasPost['first_name'] = '';
        $datasPost['last_name'] = '';

        foreach($datasPost as $field => $data) {
            if(in_array($field, $keysExpected) && !empty($data)) {
                $datasForm[$field] = htmlentities($data);
            } else {
                $errors[$field] = sprintf('Le champs "%s" est obligatoire', $field);
            }
        }
        
        if(!empty($errors)) {
            $this->setErrors($errors);

            return false;
        }

        return $datasForm;
    }
    
    /**
     * Returns the error array
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
    
    /**
     * Add errors
     *
     * @param  array $errors array of errors
     * @return void
     */
    public function setErrors(array $errors)
    {
        foreach($errors as $key => $message) {
            $this->errors[$key] = $message;
        }
    }
}
