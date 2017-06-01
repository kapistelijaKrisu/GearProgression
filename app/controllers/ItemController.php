<?php

class ItemController extends BaseController {
    public static function store() {
        $params = $_POST;
        $item = new Item(array(
            'name' => $params['name']
        ));
        Kint::dump($params);
        $item->save();
    }
}
