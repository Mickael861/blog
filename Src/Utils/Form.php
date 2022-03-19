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

    /**
     * @var bool
     */
    private $with_captcha;

    public function __construct(string $action, string $method, array $datas_post, bool $with_captcha = false)
    {
        $this->action = $action;
        $this->method = $method;
        $this->datas_post = $datas_post;
        $this->with_captcha = $with_captcha;
    }
    
    /**
     * Creation form
     *
     * @param  string $fields fields of the form
     * @return string form in HTML
     */
    public function createForm(string $fields): string
    {
        $form = '<form action="' . $this->action . ' " method="' . $this->method . '"> ';
        $form .= $fields;
        if ($this->with_captcha) {
            $form .= '<input type="hidden" id="recaptchaResponse" value="" name="recaptcha-response">';
        }
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
        $valueField = !empty($this->datas_post[$name]) ? htmlspecialchars($this->datas_post[$name]) : '';

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
        $valueField = !empty($this->datas_post[$name]) ? htmlspecialchars($this->datas_post[$name]) : '';
 
        $field = '';
        if ($with_label) {
            $field = '<label for="' . $name . '" class="form-label fw-bold mt-3">' . $labelValue .
            ' <span class="text-danger">' . $required . '</span></label>';
        }
        $field .= '<textarea style="height:200px;" class="form-control ' .
        $errors_field . '" name="' . $name . '" id="' . $id . '">' . $valueField . '</textarea>';

        if (!empty($this->errorsForm[$name])) {
            $field .= '<div class="invalid">' . $this->errorsForm[$name] . '</div>';
        }

        return $field;
    }

    /**
     * Creation of the select field
     *
     * @param  string $name name of the field
     * @param  string $id l'ID of the field
     * @param  string $labelValue value of the label
     * @param  array $datas datas option
     * @param  string $default_value default value option
     * @param  bool $required Add "required" for the field
     * @param  bool $with_label Add label to the field
     * @return string select in HTML
     */
    public function addSelect($name, $id, $labelValue, $datas, $default_value, $required = false, $with_label = true)
    {
        $required = $required ? '*' : '';
        $errors_field = !empty($this->errorsForm[$name]) ? 'errors-field' : '';
        $valueField = !empty($this->datas_post[$name]) ? htmlspecialchars($this->datas_post[$name]) : '';

        $field = '';
        if ($with_label) {
            $field = '<label for="' . $name . '" class="form-label fw-bold mt-3">' . $labelValue .
            ' <span class="text-danger">' . $required . '</span></label>';
        }

        $field .= '<select name="' . $name . '" id="' . $id . '" for="' . $name .
            '" class="form-select ' . $errors_field . '">';

        $field .= '<option value="">' . $default_value . '</option>';
        foreach ($datas as $data) {
            $selected = '';
            if (!empty($valueField) && $data->user_id == (int) $valueField) {
                $selected = 'selected';
            }

            $field .= '<option ' . $selected . ' value="' . $data->user_id . '">'
            . $data->utilisateur_name .
            '</option>';
        }

        $field .= '</select>';

        if (!empty($this->errorsForm[$name])) {
            $field .= '<div class="invalid">' . $this->errorsForm[$name] . '</div>';
        }

        return $field;
    }

    /**
     * Verification of data passed in POST
     *
     * @param  array $keysExpected Expected datas
     * @return bool true if datas is complete, false otherwise
     */
    public function verifDatasForm(array $keysExpected): bool
    {
        if ($this->with_captcha) {
            $keysExpected['recaptcha-response'] = true;
        }

        $datasForm = false;
        if (!empty($this->datas_post)) {
            $errors = array();
            foreach ($this->datas_post as $field => $data) {
                if (!empty($keysExpected[$field]) && empty($data)) {
                    $errors[$field] = sprintf('Le champ "%s" est obligatoire', $keysExpected[$field]);
                }
            }
            
            $datasForm = true;
            if (!empty($errors)) {
                $this->errorsForm = $errors;
                
                $datasForm = false;
            }
    
            if ($this->with_captcha && empty($errors)) {
                $this->isRobotReCaptcha();
            }
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
        $button .= '<button type="submit" class="btn btn-primary btn-send ' . $class . '">' . $buttonValue . '</button></div>';
        
        return $button;
    }
    
    /**
     * handle redirect if recaptcha return false
     *
     * @return void
     */
    private function isRobotReCaptcha(): void
    {
        $is_robot = $this->addRecaptcha();

        if (!$is_robot) {
            (new Utils)::setErrorsSession('Vous ne pouvez pas soumettre ce formulaire');
            (new Utils)::redirect('/#');
        }
    }

    /**
     * Add verif recaptcha
     *
     * @return bool true if it's a robot, false otherwise
     */
    private function addRecaptcha(): bool
    {
        $datasCaptcha = false;
        if (!empty($this->datas_post['recaptcha-response'])) {
            $url = 'https://www.google.com/recaptcha/api/siteverify?' .
            'secret=6LegOuYeAAAAAKJqsOa_qNEbDXuJQWgPilExxn6D' .
            '&response=' . $this->datas_post['recaptcha-response'];
            
            if (function_exists('curl_version')) {
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_HEADER, false);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_TIMEOUT, 1);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

                $captcha_response = json_decode(curl_exec($curl));

                if (!empty($captcha_response)) {
                    $datasCaptcha = $captcha_response->success;
                }
            }
        }

        return $datasCaptcha;
    }
}
