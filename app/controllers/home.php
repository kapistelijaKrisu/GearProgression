<?php

class HomeController extends BaseController {
    public static function home() {
        $data = array(
            'player' => parent::get_user_logged_in(),
            'classes' => Clas::all(),
            'elements' => Element::all(),
            'items' => Item::findAll());
        View::make('home.html', $data);
    }
}
