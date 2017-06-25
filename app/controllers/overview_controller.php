<?php

class OverviewController extends BaseController {

    public static function list_characters() {
        $arr = array();
        $html_arr = array();
        if (isset($_GET['search'])) {
            $arr['search'] = $_GET['search'];
            $html_arr['message'] = 'search complete!';
        }

        $html_arr['avatars'] = Avatar::all($arr);
        if (sizeof($html_arr['avatars']) == 0) {
            $array = array('errors' => array('No characters found by that name!'));
            if (isset($arr['search'])) {
                $array['search'] = $arr['search'];
            }
            Redirect::to('/home', $array);
        }
        $html_arr['items'] = Item::findAll();
        $html_arr['player'] = parent::get_user_logged_in();
        View::make('overview.html', $html_arr);
    }

    public static function list_characters_by_category($category_name, $category_value) {
        parent::check_param_can_int($category_value, '/overview');
        self::checkCategoryIsValid($category_name);
        $category_value = OverviewController::convertBooleanIfMainCategory($category_name, $category_value);

        $avatars = Avatar::all(array('category' => $category_name, 'category_value' => $category_value));
        if (sizeof($avatars) == 0) {
            Redirect::to('/home', array('errors' => array('No matches by this category!')));
        }
        $items = Item::findAll();
        $player = BaseController::get_user_logged_in();
        View::make('overview.html', array('avatars' => $avatars, 'items' => $items, 'player' => $player));
    }
    public static function addItem($id) {
        $player = parent::get_user_logged_in();
        $sourceURL = self::cut_server_referer();
        if($player == null) {
            Redirect::to($sourceURL, array('errors' => array('You are not logged in!')));
        }
        parent::check_param_can_int($id, $sourceURL);
        parent::check_post_can_int('item', $sourceURL);
        $avatar = Avatar::findById($id);
        
        if ($avatar == null) {
            Redirect::to($sourceURL, array('errors' => array('Character does not exist!')));
        }
        if($player->admin == false && $player->id != $avatar->owner_id) {
            Redirect::to($sourceURL, array('errors' => array('You don\'t have the rights!')));
        }
        //because post and param are int we already know validators will pass so there is no need to check
        $owned = new Ownership(array("a_id" => $avatar->id, "i_id" => $_POST['item']));
        if (isset($avatar->ownerships[$owned->i_id])) {
            Redirect::to($sourceURL, array('errors' => array('Character already has this item!')));
        }
        $item = Item::findById($owned->i_id);
        if ($item == null) {
            Redirect::to($sourceURL, array('errors' => array('This item does not exist!')));
        }
        $owned->store();
        Redirect::to($sourceURL, array('message' => 'Item added to character!'));
    }

    public static function deleteItem($c_id) {
        parent::get_user_kick_non_admin();
        $sourceURL = self::cut_server_referer();
        parent::check_param_can_int($c_id, $sourceURL);
        parent::check_post_can_int('item', $sourceURL);

        $owned = Ownership::findOne($c_id, $_POST['item']);
        if ($owned == null) {
            Redirect::to($sourceURL, array('errors' => array('This character did not have the item to begin with!')));
        }
        $owned->delete();
        Redirect::to($sourceURL, array('message' => 'Item deleted from character! '));
    }
    
    private static function checkCategoryIsValid($category) {
        if (isset($category)) {
            if ($category == 'main' || $category == 'Clas' || $category == 'Element' || $category == 'Item') {
                return;
            }
        }
        Redirect::to('/overview', array('errors' => array('invalid search values. Was: ' . $category)));
    }

    private static function convertBooleanIfMainCategory($category, $value) {
        if ($category == 'main') {
            if ($value == 1) {
                return 'TRUE';
            } else {
                return 'FALSE';
            }
        }
        return $value;
    }

    private static function cut_server_referer() {
        $toCut = "http://" . $_SERVER['SERVER_NAME'] . BASE_PATH;
        return str_replace($toCut, "", $_SERVER['HTTP_REFERER']);
    }

}
