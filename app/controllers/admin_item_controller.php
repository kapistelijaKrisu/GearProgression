<?php

class AdminConfigController extends BaseController {

    public static function adminPage() {
        parent::adminCheck();
        $everything = array(
            'player' => parent::get_user_logged_in(),
            'classes' => Clas::all(),
            'elements' => Element::all(),
            'items' => Item::findAll(),
            'players' => Player::findAll(),
            'avatars' => Avatar::all(array()));
        View::make('admin_config.html', $everything);
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
                Redirect::to('/admin/config', array('message' => 'Class added!'));
            }
        }
        Redirect::to('/admin/config', array('errors' => $errors, 'attributes' => array('clas' => $clas->name)));
    }

    public static function delete_clas() {
        parent::adminCheck();
        parent::check_post_can_int('class', '/admin/config');
        $clas = Clas::findById($_POST['class']);
        if ($clas != null) {
            $clas->delete();
            Redirect::to('/admin/config', array('message' => 'Class deleted!'));
        }
        Redirect::to('/admin/config', array('errors' => array('Class does not exist!')));
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
                Redirect::to('/admin/config', array('message' => 'Element added!'));
            }
        }
        Redirect::to('/admin/config', array('errors' => $errors));
    }

    public static function delete_element() {
        parent::adminCheck();
        parent::check_post_can_int('element', '/admin/config');
        $element = Element::findById($_POST['element']);
        if ($element != null) {
            $element->delete();
            Redirect::to('/admin/config', array('message' => 'Element deleted!'));
        } else {
            Redirect::to('/admin/config', array('errors' => array('Element does not exist!')));
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
                Redirect::to('/admin/config', array('message' => 'Item added!'));
            }
        }
        Redirect::to('/admin/config', array('errors' => $errors, 'attributes' => array('item' => $item->name)));
    }

    public static function delete_item() {
        parent::adminCheck();
        parent::check_post_can_int('item', '/admin/config');
        $item = Item::findById($_POST['item']);
        if ($item != null) {
            $item->delete();
            Redirect::to('/admin/config', array('message' => 'Item deleted!'));
        }
        Redirect::to('/admin/config', array('errors' => array('Item does not exist!')));
    }
}
