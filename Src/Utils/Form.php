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
    private $datasPost = array();
    
    /**
     * @var array
     */
    private $errorsForm = array();

    public function __construct($action, $method, $datasPost)
    {
        $this->action = $action;
        $this->method = $method;
        $this->datasPost = $datasPost;
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
     * @param  bool $required Add "required" for the field
     * @param  bool $with_label Add label to the field
     * @return string input in HTML
     */
    public function addInputText(
        string $name,
        string $id,
        string $labelValue,
        bool $required = false,
        bool $with_label = true
    ): string {
        $required = $required ? 'required' : '';
        $errors_field = !empty($this->errorsForm[$name]) ? 'errors-field' : '';

        $valueField = !empty($this->datasPost[$name]) ? $this->datasPost[$name] : '';
        
        $field = '';
        if ($with_label) {
            $field = '<label for="' . $name . '" class="form-label fw-bold mt-3">' . $labelValue . '</label>';
        }
        $field .= '<input type="text" class="form-control ' .
        $errors_field . '" name="' . $name . '" id="' . $id . '" value="' . $valueField . '" ' . $required .
        '>';

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
        $required = $required ? 'required' : '';
        $errors_field = !empty($this->errorsForm[$name]) ? 'errors-field' : '';
        $valueField = !empty($this->datasPost[$name]) ? $this->datasPost[$name] : '';

        $field = '';
        if ($with_label) {
            $field = '<label for="' . $name . '" class="form-label fw-bold mt-3">' . $labelValue . '</label>';
        }
        $field .= '<textarea type="text" class="form-control ' .
        $errors_field . '" name="' . $name . '" id="' . $id . '" ' . $required .
        '>' . $valueField . '</textarea>';

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
        
        foreach ($this->datasPost as $field => $data) {
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
     * @return string button in HTML
     */
    public function addButton($buttonValue = 'Envoyer'): string
    {
        $button = '<div class="text-center" style="margin-top:50px">';
        $button .= '<button type="submit" class="btn btn-primary">' . $buttonValue . '</button></div>';
        
        return $button;
    }
}
