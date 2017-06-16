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

    public static function check_logged_in() {
        if (get_user_logged_in() == null) {
            View::make('login.html', array('error' => 'log in first please!'));
        }
    }
    
    public static function check_admin() {
        $player = get_user_logged_in();
        if ($player == null || $player->admin == false) {
            View::make('overview.html', array('message' => 'de fuc'));
        }
    }
    
}
