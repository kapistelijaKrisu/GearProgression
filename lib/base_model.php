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
            if ($value == null) {
                $arr = $this->{$validator}();
            } else {
                $arr = $this->{$validator}($value);
            }
            $errors = array_merge($errors, $arr);
        }
        return $errors;
    }

    public function validate_string_length($param_arr) {
        $errors = array();
        $trimmed = preg_replace('/\s+/', '', $this->{$param_arr['attribute']});
        if ($trimmed == null ||
                strlen($trimmed) < $param_arr['min'] ||
                strlen($trimmed) > $param_arr['max']) {
            $errors[] = 'Name has to be ' . $param_arr['min'] . '-' . $param_arr['max'] . ' characters long and spaces do not count!';
        }
        return $errors;
    }

    public function validate_not_null($param_arr) {
        Kint::dump($param_arr);
        $errors = array();
        foreach ($param_arr as $att => $val) {
            Kint::dump($att);
            Kint::dump($val);
            if ($val == null) {
                $errors[] = 'This cannot be empty: ' . $att . ' !';
            }
            return $errors;
        }
    }

}
