<?php

class AdminController extends BaseController {

    public static function adminCheck() {
        $admin = $logged_in = parent::get_user_logged_in();
        if ($admin == null || $admin->admin == false) {
            Redirect::to('/overview', array('message' => 'De fuc?'));
        }
    }

    public static function adminPage($error_map) {
        AdminController::adminCheck();
        $everything = array(
            'player' => parent::get_user_logged_in(),
            'classes' => Clas::all(),
            'elements' => Element::all(),
            'items' => Item::findAll(),
            'players' => Player::findAll(),
            'avatars' => Avatar::findAll());

        if (count($error_map != 0)) {
            $everything = array_merge($everything, $error_map);
        }

        View::make('admin.html', $everything);
    }

    public static function store_clas() {
        AdminController::adminCheck();
        $clas = new Clas(array(
            'name' => $_POST['class']
        ));

        $errors = $clas->errors();
        if (sizeof($errors) == 0) {
            $clas->save();
            Redirect::to('/admin', array('message' => 'Class added!'));
        } else {
            Redirect::to('/admin', array('errors' => $errors, 'attributes' => array('clas' => $clas->name)));
        }
    }

    public static function delete_clas() {
        AdminController::adminCheck();
        $clas = Clas::findById($_POST['class']);
        if ($clas != null) {
            $clas->delete();
            Redirect::to('/admin', array('message' => 'Class deleted!'));
        } else {
            Redirect::to('/admin', array('errors' => array('Class does not exist!')));
        }
    }

    public static function store_element() {
        $params = $_POST;
        $element = new Element(array(
            'type' => $params['element']
        ));
        $errors = $element->errors();
        if (sizeof($errors) == 0) {
            $element->save();
            Redirect::to('/admin', array('message' => 'Element added!'));
        } else {
            Redirect::to('/admin', array('errors' => $errors, 'attributes' => array('element' => $element->type)));
        }
    }

    public static function delete_element() {
        AdminController::adminCheck();
        $element = Element::findById($_POST['element']);
        if ($element != null) {
            $element->delete();
            Redirect::to('/admin', array('message' => 'Element deleted!'));
        } else {
            Redirect::to('/admin', array('errors' => array('Element does not exist!')));
        }
    }

    public static function store_item() {
        AdminController::adminCheck();
        $item = new Item(array(
            'name' => $_POST['item']
        ));

        $errors = $item->errors();
        if (sizeof($errors) == 0) {
            $item->save();
            Redirect::to('/admin', array('message' => 'Item added!'));
        } else {
            Redirect::to('/admin', array('errors' => $errors, 'attributes' => array('item' => $item->name)));
        }
    }

    public static function delete_item() {
        AdminController::adminCheck();
        $item = Item::findById($_POST['item']);
        if ($item != null) {
            $item->delete();
            Redirect::to('/admin', array('message' => 'Item deleted!'));
        } else {
            Redirect::to('/admin', array('errors' => array('Item does not exist!')));
        }
    }

    public static function store_player() {//to admin
        AdminController::adminCheck();
        Kint::dump($_POST);

        $params = $_POST;
        $attributes = array(
            'name' => $_POST['player_name'],
            'password' => 'asd',
            'admin' => false
        );
        $player = new Player($attributes);
        $errors = $player->errors();
        Kint::dump($player);
        Kint::dump($errors);
        if (count($errors) == 0) {
            $player->store();
            Redirect::to('/admin', array('message' => 'Player added. Tell the player default password is: "asd".'));
        } else {
            Redirect::to('/admin', array('errors' => $errors, 'player' => $attributes));
        }
    }

    public static function delete_player() {

        AdminController::adminCheck();
        $player = Player::findById($_POST['player']);
        if ($player == null) {
            Redirect::to('/admin', array('errors' => array('Player does not exist!')));
        } else if ($player->admin == false) {
            $player->delete();
            Redirect::to('/admin', array('message' => 'Player deleted!'));
        } else {
            Redirect::to('/admin', array('message' => 'Admins will stay forever!'));
        }
    }

    public static function delete_character() {
        AdminController::adminCheck();
        $avatar = Avatar::findById($_POST['avatar']);

        if ($avatar == null) {
            Redirect::to('/admin', array('errors' => array('Character does not exist!')));
        } else {
            $avatar->delete();
            Redirect::to('/admin', array('message' => 'Character deleted!'));
        }
    }

    public static function store_avatar() {
        AdminController::adminCheck();
        Kint::dump($_POST);

        $main = false;
        if ($_POST['priority'] == 'main') {
            $main = true;
        }
        $avatar_att = array(
            'name' => $_POST['character'],
            'element' => Element::findById($_POST['element']),
            'main' => $main,
            'clas' => Clas::findById($_POST['class']),
            'owner_id' => $_POST['player']
        );
        

        $avatar = new Avatar($avatar_att);
        $errors = $avatar->errors();
        if (sizeof($errors) == 0) {
            $avatar->store();
            $items = Item::findAll();
            $owned = array();
            foreach ($items as $item) {
                if (isset($_POST[$item->id])) {
                    $toSave = new Ownership(array('a_id' => $avatar->id, 'i_id' => $item->id));
                    $toSave->store();
                }
            }
            Redirect::to('/admin', array('message' => 'Character deleted!'));
        } else {
            Redirect::to('/admin', array('errors' => array('Character does not exist!')));
        }
    }

}
