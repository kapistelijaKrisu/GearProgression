<?php

class BaseModel {

    public $validators;

    public function __construct($attributes = null) {

        foreach ($attributes as $attribute => $value) {
            // Jos avaimen niminen attribuutti on olemassa...
            if (property_exists($this, $attribute)) {
                // ... lisätään avaimen nimiseen attribuuttin siihen liittyvä arvo
                $this->{$attribute} = $value;
            }
        }
    }

    public function errors() {

        $errors = array();
        foreach ($this->validators as $validator => $value) {
            $arr = $this->{$validator}($value);
            $errors = array_merge($errors, $arr);
        }
        return $errors;
    }

    public function validate_name($param_arr) {
        $errors = array();
        if ($this->name == null || strlen($this->name) < $param_arr['min'] || strlen($this->name) > $param_arr['max']) {
            $errors[] = 'Name has to be ' . $param_arr['min'] . '-' . $param_arr['max'] . ' characters long!';
        }
        return $errors;
    }

    public function validate_not_null($param_arr) {
        $errors = array();
        foreach ($param_arr as $att => $val) {

            if ($val == null) {
                $errors[] = 'This cannot be empty: ' . $att . ' !';
            }
            return $errors;
        }
    }

}
