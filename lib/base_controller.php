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

    public static function get_user_kick_non_admin() {
        $admin = self::get_user_logged_in();
        if ($admin == null || $admin->admin == false) {
            Redirect::to('/home', array('errors' => array('You dont have the rights')));
        }
        return $admin;
    }

    public static function check_logged_in() {
        if (get_user_logged_in() == null) {
            View::make('login.html', array('errors' => 'log in first please!'));
        }
    }
    public static function check_post_can_int($postIndex, $onFailRedirectTo) {
        try {
            if (isset($_POST[$postIndex])) {
                if ((int) $_POST[$postIndex] == 0) {
                    Redirect::to($onFailRedirectTo, array('errors' => array($postIndex . ' is in invalid format! 0 is not acceptable either')));
                }
            } else {
                Redirect::to($onFailRedirectTo, array('errors' => array($postIndex . ' is not found in post!')));
            }
        } catch (Exception $ex) {
            Redirect::to($onFailRedirectTo, array('errors' => array($postIndex . ' is in invalid format!')));
        }
    }

    public static function check_param_can_int($value, $onFailRedirectTo) {
        try {
            if ((int) $value == 0) {
                Redirect::to($onFailRedirectTo, array('errors' => array('value is in invalid format! 0 is not acceptable either!')));
            }
            
        } catch (Exception $ex) {
            Redirect::to($onFailRedirectTo, array('errors' => array('value is in invalid format!')));
        }
    }

}
