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

    public function validate_name($toValidate) {
        $errors = array();
        $value = $this->{$toValidate['attribute']};

        if ($value == null) {
            $errors[] = $toValidate['attribute'] . ' cannot be empty!';
        }
        if (!is_string($value)) {
            $errors[] = $toValidate['attribute'] . ' has to be string';
            return $errors;
        }
        if (strlen($value) < $toValidate['min'] ||
                strlen($value) > $toValidate['max']) {
            $errors[] = $toValidate['attribute'] . ' has to be ' . $toValidate['min'] . '-' . $toValidate['max'] . ' characters long!';
        }
        $space_count = substr_count($value, ' ');
        if ($space_count > 1) {
            $errors[] = $toValidate['attribute'] . ' only 1 space is allowed';
        }
        $lastChar = substr($value, -1);
        if ($lastChar == ' ') {
            $errors[] = $toValidate['attribute'] . ' last character cannot be a space!';
        }
        $firstChar = substr($value, 0, 1);
        if ($firstChar == ' ') {
            $errors[] = $toValidate['attribute'] . ' first character cannot be a space!';
        }
        return $errors;
    }

    public function validate_attributes_are_boolean($attributeArray) {
        $errors = array();

        foreach ($attributeArray as $asName) {
            if (!is_bool($this->{$asName})) {
                $errors[] = $asName . ' value has to be bool!';
            }
        }
        return $errors;
    }

    public function validate_object_classes_are_correct($evaluated) {
        $errors = array();
        foreach ($evaluated as $desired_class_name => $param_name) {
            try {
                if (get_class($this->{$param_name}) != $desired_class_name) {
                    $errors[] = 'Expected object class to be ' . $desired_class_name . ' but was ' . get_class($this->{$param_name}) . ' or null php is funny';
                }
            } catch (Exception $ex) {
                $errors[] = $param_name . ' was not an stdObject';
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
