<?php

class PlayerController extends BaseController {
    public static function store() {
        $params = $_POST;
        $player = new Player(array(
            'name' => $params['name'],
            'password' => 'asd',
            'admin' => false));
        
        Kint::dump($params);
        $player->save();
    }
}
