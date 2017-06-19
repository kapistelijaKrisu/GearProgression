<?php

class AdminController extends BaseController {

    public static function adminPage() {
        parent::adminCheck();
        $everything = array(
            'player' => parent::get_user_logged_in(),
            'classes' => Clas::all(),
            'elements' => Element::all(),
            'items' => Item::findAll(),
            'players' => Player::findAll(),
            'avatars' => Avatar::all(array()));
        View::make('admin.html', $everything);
    }

    public static function store_clas() {
        parent::adminCheck();
        $clas = new Clas(array(
            'name' => $_POST['class']
        ));

        $errors = $clas->errors();
        if (sizeof($errors) == 0) {
            $errors = array_merge($errors, $clas->check_name_is_unique());
            if (sizeof($errors) == 0) {
                $clas->save();
                Redirect::to('/admin', array('message' => 'Class added!'));
            }
        }
        Redirect::to('/admin', array('errors' => $errors, 'attributes' => array('clas' => $clas->name)));
    }

    public static function delete_clas() {
        parent::adminCheck();
        parent::check_post_can_int('class', '/admin');
        $clas = Clas::findById($_POST['class']);
        if ($clas != null) {
            $clas->delete();
            Redirect::to('/admin', array('message' => 'Class deleted!'));
        }
        Redirect::to('/admin', array('errors' => array('Class does not exist!')));
    }

    public static function store_element() {
        Kint::dump($_POST);
        parent::adminCheck();
        $params = $_POST;
        $element = new Element(array(
            'type' => $params['element']
        ));
        $errors = $element->errors();
        if (sizeof($errors) == 0) {
            $errors = array_merge($errors, $element->check_type_is_unique());
            if (sizeof($errors) == 0) {
                $element->save();
                Redirect::to('/admin', array('message' => 'Element added!'));
            }
        }
        Redirect::to('/admin', array('errors' => $errors));
    }

    public static function delete_element() {
        parent::adminCheck();
        parent::check_post_can_int('element', '/admin');
        $element = Element::findById($_POST['element']);
        if ($element != null) {
            $element->delete();
            Redirect::to('/admin', array('message' => 'Element deleted!'));
        } else {
            Redirect::to('/admin', array('errors' => array('Element does not exist!')));
        }
    }

    public static function store_item() {
        parent::adminCheck();
        $item = new Item(array(
            'name' => $_POST['item']
        ));

        $errors = $item->errors();
        if (sizeof($errors) == 0) {
            $errors = array_merge($errors, $item->check_name_is_unique());
            if (sizeof($errors) == 0) {
                $item->save();
                Redirect::to('/admin', array('message' => 'Item added!'));
            }
        }
        Redirect::to('/admin', array('errors' => $errors, 'attributes' => array('item' => $item->name)));
    }

    public static function delete_item() {
        parent::adminCheck();
        parent::check_post_can_int('item', '/admin');
        $item = Item::findById($_POST['item']);
        if ($item != null) {
            $item->delete();
            Redirect::to('/admin', array('message' => 'Item deleted!'));
        }
        Redirect::to('/admin', array('errors' => array('Item does not exist!')));
    }

    public static function store_player() {//to admin
        parent::adminCheck();

        $params = $_POST;
        $attributes = array(
            'name' => $_POST['player_name'],
            'password' => 'asd',
            'admin' => false
        );
        $player = new Player($attributes);
        $errors = $player->errors();

        if (sizeof($errors) == 0) {
            $errors = array_merge($errors, $player->check_name_is_unique());
            if (count($errors) == 0) {
                $player->store();
                Redirect::to('/admin', array('message' => 'Player added. Tell the player default password is: "asd".'));
            }
        }
        Redirect::to('/admin', array('errors' => $errors, 'player' => $attributes));
    }

    public static function delete_player() {
        parent::adminCheck();
        parent::check_post_can_int('player', '/admin');
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
        parent::adminCheck();
        parent::check_post_can_int('avatar', '/admin');
        $avatar = Avatar::findById($_POST['avatar']);

        if ($avatar == null) {
            Redirect::to('/admin', array('errors' => array('Character does not exist!')));
        } else {
            $avatar->delete();
            Redirect::to('/admin', array('message' => 'Character deleted!'));
        }
    }

    public static function store_avatar() {
        parent::adminCheck();

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
            $errors = array_merge($errors, $avatar->check_name_is_unique());
            $errors = array_merge($errors, $avatar->check_non_admin_avatar_limit_count());
            $errors = array_merge($errors, $avatar->check_non_admin_main_avatar());
            if (sizeof($errors) == 0) {
                $avatar->store();
                $items = Item::findAll();

                foreach ($items as $item) {

                    if (isset($_POST[$item->id])) {
                        parent::check_post_can_int($item->id, '/admin');
                        $toSave = new Ownership(array('a_id' => $avatar->id, 'i_id' => $item->id));
                        $toSave->store();
                    }
                }
                Redirect::to('/admin', array('message' => 'Character created!'));
            }
        }
        Redirect::to('/admin', array('errors' => $errors));
    }

}
