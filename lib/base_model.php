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
            $value = $this->{$toValidate['attribute']};
            
            if ($value == null) {
                $errors[] = $toValidate['attribute'] . ' cannot be empty!';
            }
            if (!is_string($value)) {
                $errors[] = $toValidate['attribute'] . ' has to be string';
                return $errors;
            }
            $space_count = substr_count($value, ' ');
            if ($space_count > 1) {
                $errors[] = $toValidate['attribute'] . ' Only 1 space is allowed';
                return $errors;
            }
            $spaces_out = str_replace(' ', '', $value);
           
            if (!ctype_alnum($spaces_out)) {
                $errors[] = $toValidate['attribute'] . ' Only alpha or numeric characters are allowed!';
                return $errors;
            }
            if (strlen($value) < $toValidate['min'] ||
                    strlen($value) > $toValidate['max']) {
                $errors[] = $toValidate['attribute'] . ' has to be ' . $toValidate['min'] . '-' . $toValidate['max'] . ' characters long and spaces do not count!';
            }
        }
        return $errors;
    }

    public function validate_value_is_boolean($asName) {
        $errors = array();

        if (is_bool($this->{$asName})) {
            return $errors;
        } else {
            $errors[] = $asName . ' value has to be bool!';
        }
        Kint::dump($errors);
        return $errors;
    }

    public function check_classes_are_correct($evaluated) {
        $errors = array();
        foreach ($evaluated as $desired_class_name => $param_name) {
            if ($desired_class_name == null || is_string($desired_class_name) == false) {
                $errors[] = 'array key is invalid must be a string';
            } else if ($param_name == null || is_string($param_name) == false) {
                $errors[] = 'array value is invalid must be a string';
            } else if (get_class($this->{$param_name}) != $desired_class_name) {
                $errors[] = 'Expected object class to be ' . $desired_class_name . ' but was ' . get_class($this->{$param_name}) . ' or null php is funny';
            }
        }
        return $errors;
    }

    public function validate_values_are_int($nameArr) {
        $errors = array();
        try {
            foreach ($nameArr as $asName) {
                $param = $this->{$asName};
                (int) $param;
            }
        } catch (Exception $ex) {
            $errors[] = 'value has to be integer!';
        }

        return $errors;
    }

}
