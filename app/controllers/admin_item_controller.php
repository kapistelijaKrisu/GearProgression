<?php

class AdminConfigController extends BaseController {

    public static function adminPage() {
        parent::kick_non_admin();
        $data = array(
            'player' => parent::get_user_logged_in(),
            'classes' => Clas::all(),
            'elements' => Element::all(),
            'items' => Item::findAll());
        View::make('admin_config.html', $data);
    }

    public static function store_clas() {
        parent::kick_non_admin();
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
        parent::kick_non_admin();
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
        parent::kick_non_admin();
        $params = $_POST;
        $element = new Element(array(
            'name' => $params['element']
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
        parent::kick_non_admin();
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
        parent::kick_non_admin();
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
        parent::kick_non_admin();
        parent::check_post_can_int('item', '/admin/config');
        $item = Item::findById($_POST['item']);
        if ($item != null) {
            $item->delete();
            Redirect::to('/admin/config', array('message' => 'Item deleted!'));
        }
        Redirect::to('/admin/config', array('errors' => array('Item does not exist!')));
    }
}
