<?php
namespace App\Utils;

/**
 * Creation of forms
 */
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

    public function __construct($action, $method)
    {
        $this->action = $action;
        $this->method = $method;
    }
    
    /**
     * Creation form
     *
     * @param  string $fields fields of the form
     * @return string
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
     * @return string
     */
    public function addInputText(string $name, string $id, string $labelValue, bool $required = false, bool $with_label = true): string
    {
        $required = $required ? 'required' : '';

        $field = '';
        if ($with_label) {
            $field = '<label for="' . $name . '" class="form-label">' . $labelValue . '</label>';
        }
        $field .= '<input type="text" class="form-control" name="' . $name . '" id="' . $id . '" ' . $required . '>';
        
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
     * @return string
     */
    public function addTextArea(string $name, string $id, string $labelValue, bool $required = false, bool $with_label = true): string
    {
        $required = $required ? 'required' : '';

        $field = '';
        if ($with_label) {
            $field = '<label for="' . $name . '" class="form-label">' . $labelValue . '</label>';
        }
        $field .= '<textarea type="text" class="form-control" name="' . $name . '" id="' . $id . '" ' . $required . '></textarea>';

        return $field;
    }
    
    /**
     * getButton
     *
     * @param  string $buttonValue Value of the button
     * @return string
     */
    public function addButton($buttonValue = 'Envoyer'): string
    {
        $button = '<div class="text-center" style="margin-top:50px">';
        $button .= '<button type="submit" class="btn btn-primary">' . $buttonValue . '</button></div>';
        
        return $button;
    }
}