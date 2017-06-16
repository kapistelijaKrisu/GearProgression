<?php

class ItemController extends BaseController {

 
    public static function sandbox() {
       
    }

    public static function overview() {
        $avatars = Avatar::findAll();
        $items = Item::findAll();
        $player = BaseController::get_user_logged_in();
        View::make('overview.html', array('avatars' => $avatars, 'items' => $items, 'player' => $player));
    }

    

    

}
