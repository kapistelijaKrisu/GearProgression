<?php

class AvatarController extends BaseController {

    public static function characterPage($id) {
        $avatar = Avatar::findById($id);
        if ($avatar == null) {
            Redirect::to('/overview', array('errors' => array('Character does not exist!')));
        }
        $items = Item::findAll();
        $nameFormatted = str_replace(' ', '%20', $avatar->name);
        $link = 'https://bnstree.com/character/na/' . $nameFormatted;
        
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
        if ($player->id != $avatar->owner_id && !$player->admin) {
            Redirect::to('/overview', array('errors' => 'You don\'t have the right to do this!!'));
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
                Redirect::to('/character/' . $id, array('message' => 'Congratulations on your ' . $item->name . '!'));
            }
        }
        Redirect::to('/character/' . $id, array('errors' => $errors));
    }

    public static function deleteItem($id) {
        parent::kick_non_admin();
        $player = parent::get_user_logged_in();
        parent::check_param_can_int($id, '/character/' . $id);
        parent::check_post_can_int('item', '/character/' . $id);
        
        $avatar = Avatar::findById($id);
        if ($avatar == null) {
            Redirect::to('/character/'.$id, array('errors' => array('Character does not exist!')));
        }
        $item = Item::findById($_POST['item']);
        if ($item == null) {
            Redirect::to('/overview', array('errors' => array('This item does not exist!')));
        }
        $owned = Ownership::findOne($avatar->id, $item->id);
        $owned->delete();
        Redirect::to('/character/' . $id, array('message' => 'This item has been deleted: ' . $item->name . ' from: ' . $avatar->name . '!'));
    }

}
