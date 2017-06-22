<?php

class HomeController extends BaseController {
    public static function home() {
        $options = array();
        if (isset($_GET['search'])) {
            $options['search'] = $_GET['search'];
        }
        $avatars = Avatar::all($options);
        $everything = array(
            'player' => parent::get_user_logged_in(),
            'classes' => Clas::all(),
            'elements' => Element::all(),
            'items' => Item::findAll(),
            'players' => Player::findAll());
        View::make('home.html', $everything);
    }
}
