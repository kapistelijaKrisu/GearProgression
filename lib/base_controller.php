<?php

class BaseController {

    public static function get_user_logged_in() {
        if (isset($_SESSION['player'])) {
            $player_id = $_SESSION['player'];
            $player = Player::findById($player_id);
            return $player;
        }
        return null;
    }

    public static function kick_non_admin() {
        $admin = BaseController::get_user_logged_in();
        if ($admin == null || $admin->admin == false) {
            Redirect::to('/home', array('message' => 'You dont have the rights'));
        }
    }

    public static function check_logged_in() {
        if (get_user_logged_in() == null) {
            View::make('login.html', array('errors' => 'log in first please!'));
        }
    }

    public static function check_admin() {
        $player = get_user_logged_in();
        if ($player == null || $player->admin == false) {
            View::make('home.html', array('errors' => 'You dont have the rights'));
        }
    }

    public static function check_post_can_int($postIndex, $onFailRedirectTo) {
        try {
            if (isset($_POST[$postIndex])) {
                (int) $_POST[$postIndex];
            } else {
                Redirect::to($onFailRedirectTo, array('errors' => array($postIndex . ' is in invalid format!')));
            }
        } catch (Exception $ex) {
            Redirect::to($onFailRedirectTo, array('errors' => array($postIndex . ' is in invalid format!')));
        }
    }

    public static function check_param_can_int($value, $onFailRedirectTo) {
        try {
            (int) $value;
        } catch (Exception $ex) {
            Redirect::to($onFailRedirectTo, array('errors' => array('value is in invalid format!')));
        }
    }

}
