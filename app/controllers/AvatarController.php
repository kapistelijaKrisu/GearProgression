<?php

class AvatarController extends BaseController {

    public static function characterPage($id) {
        $avatar = Avatar::findById($id);
        if ($avatar == null) {
            Redirect::to('/overview', array('errors' => array('Character does not exist!')));
        }
        $items = Item::findAll();
        $nameFormatted = preg_replace('/\s+/', '%20', $avatar->name);
        $link = 'https://bnstree.com/character/na/'.$nameFormatted;
        View::make('character.html', array('avatar' => $avatar,
            'items' => $items,
            'player' => parent::get_user_logged_in(),
            'link' => $link));
    }

    public static function addItem($id) {
        $player = parent::get_user_logged_in();
        if ($player == null) {
            Redirect::to('/overview', array('errors' => array('Not logged in!')));
        }
        parent::check_param_can_int($id, '/character/' . $id);
        $avatar = Avatar::findById($id);
        if ($avatar == null) {
            Redirect::to('/overview', array('errors' => array('Character does not exist!')));
        }
        if ($player->id != $avatar->owner_id) {
            Redirect::to('/overview', array('errors' => 'not yours!'));
        }
        if (isset($_POST['item'])) {
            $owned = new Ownership(array("a_id" => $avatar->id, "i_id" => $_POST['item']));
           
            $errors = $owned->errors();
            if (sizeof($errors) == 0) {
                if (isset($avatar->ownerships[$owned->i_id])) {
                    Redirect::to('/character/' . $id, array('errors' => 'You already have this item!'));
                }
                $item = Item::findById($owned->i_id);
                if ($item == null) {
                    Redirect::to('/character/' . $id, array('errors' => 'This item does not exist!'));
                }
                $owned->store();
                Redirect::to('/character/' . $id, array('message' => 'Congratulations on your '.$item->name.'!'));
            }
        }
         Redirect::to('/character/'.$id, array('errors' => $errors));
    }

}
