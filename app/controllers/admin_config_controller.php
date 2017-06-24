<?php

class AdminConfigController extends BaseController {

    public static function adminPage() {
        $admin = parent::get_user_kick_non_admin();
        $data = array(
            'player' => $admin,
            'classes' => Clas::all(),
            'elements' => Element::all(),
            'items' => Item::findAll());
        View::make('admin_config.html', $data);
    }

    public static function store_clas() {
        parent::get_user_kick_non_admin();
        if(!isset($_POST['name'])) {
            Redirect::to('/admin/config', array('errors' => array('missing name of class from post!')));
        }
        $clas = new Clas(array(
            'name' => $_POST['name']
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
        parent::get_user_kick_non_admin();
        parent::check_post_can_int('object', '/admin/config');
        $clas = Clas::findById($_POST['object']);
        if ($clas != null) {
            $clas->delete();
            Redirect::to('/admin/config', array('message' => 'Class deleted!'));
        }
        Redirect::to('/admin/config', array('errors' => array('Class does not exist!')));
    }

    public static function store_element() {
        parent::get_user_kick_non_admin();
        if(!isset($_POST['name'])) {
            Redirect::to('/admin/config', array('errors' => array('missing name of element from post!')));
        }
        $element = new Element(array(
            'name' => $_POST['name']
        ));
        $errors = $element->errors();
        if (sizeof($errors) == 0) {
            $errors = array_merge($errors, $element->check_name_is_unique());
            if (sizeof($errors) == 0) {
                $element->save();
                Redirect::to('/admin/config', array('message' => 'Element added!'));
            }
        }
        Redirect::to('/admin/config', array('errors' => $errors));
    }

    public static function delete_element() {
        parent::get_user_kick_non_admin();
        parent::check_post_can_int('object', '/admin/config');
        $element = Element::findById($_POST['object']);
        if ($element != null) {
            $element->delete();
            Redirect::to('/admin/config', array('message' => 'Element deleted!'));
        } else {
            Redirect::to('/admin/config', array('errors' => array('Element does not exist!'), 'attributes' => array('element' => $element->name)));
        }
    }

    public static function store_item() {
        parent::get_user_kick_non_admin();
        if(!isset($_POST['name'])) {
            Redirect::to('/admin/config', array('errors' => array('missing name of item from post!')));
        }
        $item = new Item(array('name' => $_POST['name']));

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
        parent::get_user_kick_non_admin();
        parent::check_post_can_int('object', '/admin/config');
        $item = Item::findById($_POST['object']);
        if ($item != null) {
            $item->delete();
            Redirect::to('/admin/config', array('message' => 'Item deleted!'));
        }
        Redirect::to('/admin/config', array('errors' => array('Item does not exist!')));
    }
}
