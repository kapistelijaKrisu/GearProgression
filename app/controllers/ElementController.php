<?php

class ElementController extends BaseController {
    public static function store() {
        $params = $_POST;
        $element = new Element(array(
            'type' => $params['type']
        ));
        Kint::dump($params);
        $element->save();
    }
}
