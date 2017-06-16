<?php

class AvatarController extends BaseController {
    public static function characterPage($id) {
        $avatar = Avatar::findById($id);
        $items = Item::findAll();
        View::make('character.html', array('avatar' => $avatar, 'items' => $items, 'player' => parent::get_user_logged_in()));
    }
}
