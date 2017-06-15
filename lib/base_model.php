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
        foreach ($this->validators as $key => $value) {
            if (is_int($key)) {
                $arr = $this->{$value}();
            } else {
                $arr = $this->{$key}($value);
            }
            $errors = array_merge($errors, $arr);
        }
        return $errors;
    }

    public function validate_string_lengths($param_arr) {
        $errors = array();
        foreach ($param_arr as $toValidate) {
            $trimmed = preg_replace('/\s+/', '', $this->{$toValidate['attribute']});
            if ($trimmed == null ||
                    strlen($trimmed) < $toValidate['min'] ||
                    strlen($trimmed) > $toValidate['max']) {
                $errors[] = $toValidate['attribute'] . ' has to be ' . $toValidate['min'] . '-' . $toValidate['max'] . ' characters long and spaces do not count!';
            }
        }
        return $errors;
    }

    public function validate_value_is_boolean($asName) {//fix to as param name
        $errors = array();
        
        if (is_bool($this->{$asName})) {
            return $errors;
        } else {
            $errors[] = $asName . ' value has to be bool!';
        }
        Kint::dump($errors);
        return $errors;
    }

    public function classes_are_correct($evaluated) {
        $errors = array();
        foreach ($evaluated as $desired_class_name => $param_name) {
            if ($desired_class_name == null || is_string($desired_class_name) == false) {
                $errors[] = 'array key is invalud must be a string';
            } else if ($param_name == null || is_string($param_name) == false) {
                $errors[] = 'array value is invalud must be a string';
            } else if (get_class($this->{$param_name}) != $desired_class_name) {
                $errors[] = 'Expected object class to be ' . $desired_class_name . ' but was ' . get_class($this->{$param_name}) . ' or null php is funny';
            }
        }
        return $errors;
    }
    
   

    public function validate_value_is_int($param) {
        $errors = array();

        if ($param == null || is_int($param) == false) {
            $errors[] = 'value has to be integer!';
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
