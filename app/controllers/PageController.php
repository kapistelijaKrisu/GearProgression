<?php

class PageController extends BaseController {

 
    public static function sandbox() {
       
    }

    public static function overview() {
        $avatars = Avatar::findAll();
        $items = Item::findAll();
        $player = BaseController::get_user_logged_in();
        View::make('overview.html', array('avatars' => $avatars, 'items' => $items, 'player' => $player));
    }

    public static function adminPage($error_map) {
        $everything = array(
            'classes' => Clas::all(),
            'elements' => Element::all(),
            'items' => Item::findAll(),
            'players' => Player::findAll(),
            'avatars' => Avatar::findAll());
        
        if(count($error_map != 0)) {
            $everything = array_merge($everything, $error_map);
        }
        
        View::make('admin.html', $everything);
    }

    public static function myPage($id) {
        $player = Player::findById($id);
        $items = Item::findAll();
        $avatars = Avatar::findByPlayer($id);
        $classes = Clas::all();
        $elements = Element::all();
        View::make('mypage.html', array('avatars' => $avatars, 'items' => $items,
            'classes' => $classes, 'elements' => $elements, 'player' => $player));
  }

    public static function characterPage($id) {
        $avatar = Avatar::findOne($id);
        $items = Item::findAll();
        View::make('character.html', array('avatar' => $avatar, 'items' => $items));
    }

}
