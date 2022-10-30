<?php

include_once('Session.php');

class Validator{

    //data begin validated
    private $data;

    //validation errors
    private $errors;

    private $validate_rules;

    private $messages = [
            'required' => 'This field is required.',
            'number' => 'This field must be a number.',
            'email' => 'This field must be a valid email address.',
            'date' => 'This field must be a valid date.'
    ];

    public function __construct($data, $validation_rules){
        $this->data = $data;
        $this->validation_rules = $validation_rules;
    }

    public function validate(){
        foreach ($this->validation_rules as $field => $rule){
            $field_value = $this->getFieldValue($field);
            //follow naming convention...it's important
            $rule = ucfirst($rule);
            $method_to_call = "validate".$rule;

            if(!$this->$method_to_call($field_value)){
                //add errors here
                $this->addError($rule,$field);
            }
        }
    }

    public function getFieldValue($field){
        return $this->data[$field];
    }

    //validates required field
    public function validateRequired($value){
        return !empty($value);
    }

    //validates number field
    public function validateNumber($value){
        return is_numeric($value);
    }

    //validates email address
    public function validateEmail($value){
        return filter_var($value,FILTER_VALIDATE_EMAIL);
    }

    //validates date
    public function validateDate($value){
        $format = 'Y-m-d';
        $d = DateTime::createFromFormat($format,$value);
        //return false;
        return ($d && ($d->format($format) === $value));
    }

    public function addError($rule,$field){
        $rule = strtolower($rule);
        $message = $this->messages[$rule];

        $this->errors[$field] = $message;

        //Place in a Session variable
        appSession::set('errors',$this->errors);

    }

    public static function getErrorForField($field){
        if(appSession::exists('errors')){
            $errors = appSession::get('errors');
            if(key_exists($field,$errors)){
                $error = $errors[$field];
                unset($_SESSION['errors'][$field]);
                return $error;
            }
        }
    }

    public function passes(){
        //return true, if all validation passes
        return empty($this->errors);
    }
}