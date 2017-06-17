<?php

class ItemController extends BaseController {

    public static function overview() {
        $avatars = Avatar::findAll();
        $items = Item::findAll();
        $player = BaseController::get_user_logged_in();
        View::make('overview.html', array('avatars' => $avatars, 'items' => $items, 'player' => $player));
    }

    public static function addItem($id) {
        parent::adminCheck();
        parent::check_param_can_int($id, '/overview');

        $avatar = Avatar::findById($id);
        if ($avatar == null) {
            Redirect::to('/overview', array('errors' => array('Character does not exist!')));
        }

        if (isset($_POST['item'])) {
            $owned = new Ownership(array("a_id" => $avatar->id, "i_id" => $_POST['item']));

            $errors = $owned->errors();
            if (sizeof($errors) == 0) {
                if (isset($avatar->ownerships[$owned->i_id])) {
                    Redirect::to('/overview', array('errors' => 'Character already has this item!'));
                }
                $item = Item::findById($owned->i_id);
                if ($item == null) {
                    Redirect::to('/overview', array('errors' => 'This item does not exist!'));
                }
                $owned->store();
                Redirect::to('/overview', array('message' => 'Item added to character!'));
            }
        }
        Redirect::to('/overview', array('errors' => $errors));
    }

    public static function deleteItem($c_id) {
        parent::adminCheck();
        parent::check_param_can_int($c_id, '/overview');
        parent::check_post_can_int('item', '/overview');

        $owned = Ownership::findOne($c_id, $_POST['item']);
        if ($owned == null) {
            Redirect::to('/overview', array('errors' => 'This character did not have the item to begin with!'));
        }
        $owned->delete();
        Redirect::to('/overview', array('message' => 'Item deleted from character! '));
    }

}
