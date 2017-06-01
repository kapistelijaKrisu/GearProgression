<?php

class ClasController extends BaseController {
    public static function store() {
        $params = $_POST;
        $clas = new Clas(array(
            'name' => $params['name']
        ));
        Kint::dump($params);
        $clas->save();
    }
}

