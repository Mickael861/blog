<?php
namespace App\Utils;

class Form
{

    /**
     * @var string
     */
    private $action;

    /**
     * @var string
     */
    private $method;
    
    /**
     * @var array
     */
    private $datas_post = array();
    
    /**
     * @var array
     */
    private $errorsForm = array();

    public function __construct($action, $method, $datas_post)
    {
        $this->action = $action;
        $this->method = $method;
        $this->datas_post = $datas_post;
    }
    
    /**
     * Creation form
     *
     * @param  string $fields fields of the form
     * @return string form in HTML
     */
    public function createForm($fields): string
    {
        $form = '<form action="' . $this->action . ' " method="' . $this->method . '"> ';
        $form .= $fields;
        $form .= '</form>';

        return $form;
    }

    /**
     * Creation of the input field
     *
     * @param  string $name name of the field
     * @param  string $id l'ID of the field
     * @param  string $labelValue value of the label
     * @param  string $type type of field
     * @param  bool $required Add "required" for the field
     * @param  bool $with_label Add label to the field
     * @return string input in HTML
     */
    public function addInputText(
        string $name,
        string $id,
        string $labelValue,
        string $type = 'text',
        bool $required = false,
        bool $with_label = true
    ): string {
        $required = $required ? '*' : '';
        $errors_field = !empty($this->errorsForm[$name]) ? 'errors-field' : '';
        $valueField = !empty($this->datas_post[$name]) ? $this->datas_post[$name] : '';
        
        $field = '';
        if ($with_label) {
            $field = '<label for="' . $name . '" class="form-label fw-bold mt-3">' . $labelValue .
            ' <span class="text-danger">' . $required . '</span></label>';
        }
        $field .= '<input type=' . $type . ' class="form-control ' .
        $errors_field . '" name="' . $name . '" id="' . $id . '" value="' . $valueField . '">';

        if (!empty($this->errorsForm[$name])) {
            $field .= '<div class="invalid">' . $this->errorsForm[$name] . '</div>';
        }
        
        return $field;
    }

    /**
     * Creation of the input field
     *
     * @param  string $name name of the field
     * @param  string $id l'ID of the field
     * @param  string $labelValue value of the label
     * @param  bool $required Add "required" for the field
     * @param  bool $with_label Add label to the field
     * @return string textarea in HTML
     */
    public function addTextArea(
        string $name,
        string $id,
        string $labelValue,
        bool $required = false,
        bool $with_label = true
    ): string {
        $required = $required ? '*' : '';
        $errors_field = !empty($this->errorsForm[$name]) ? 'errors-field' : '';
        $valueField = !empty($this->datas_post[$name]) ? $this->datas_post[$name] : '';

        $field = '';
        if ($with_label) {
            $field = '<label for="' . $name . '" class="form-label fw-bold mt-3">' . $labelValue .
            ' <span class="text-danger">' . $required . '</span></label>';
        }
        $field .= '<textarea type="text" class="form-control ' .
        $errors_field . '" name="' . $name . '" id="' . $id . '">' . $valueField . '</textarea>';

        if (!empty($this->errorsForm[$name])) {
            $field .= '<div class="invalid">' . $this->errorsForm[$name] . '</div>';
        }

        return $field;
    }

    /**
     * Verification of data passed in POST
     *
     * @param  array $keysExpected Expected datas
     * @return array|bool un tableau de données, false si un champ est manquant
     */
    public function verifDatasForm(array $keysExpected)
    {
        $datasForm = array();
        $errors = array();
        
        foreach ($this->datas_post as $field => $data) {
            if (!empty($keysExpected[$field]) && !empty(trim(htmlentities($data)))) {
                $datasForm[$field] = trim(htmlentities($data));
            } else {
                $errors[$field] = sprintf('Le champ "%s" est obligatoire', $keysExpected[$field]);
            }
        }

        if (!empty($errors)) {
            $this->errorsForm = $errors;

            return false;
        }

        return $datasForm;
    }
    
    /**
     * getButton
     *
     * @param  string $buttonValue Value of the button
     * @param  string $class Value of class
     * @return string button in HTML
     */
    public function addButton($buttonValue = 'Envoyer', $class = ''): string
    {
        $button = '<div class="text-center">';
        $button .= '<button type="submit" class="btn btn-primary ' . $class . '">' . $buttonValue . '</button></div>';
        
        return $button;
    }
}
