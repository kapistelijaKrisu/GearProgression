<?php

class LoginController extends BaseController {

    public static function login() {
        View::make('login.html');
    }

    public static function handle_login() {
        $params = $_POST;
        $player = Player::authenticate($params['user'], $params['password']);
        if (!$player) {
            View::make('login.html', array('errors' => 'Wrong username or password!', 'player' => $player));
        } else {
            $_SESSION['player'] = $player->id;
            Redirect::to('/overview', array('message' => 'login successful ' . $player->name . '!', 'player' => $player));
        }
    }

    public static function handle_logout() {

        $_SESSION['player'] = null;
        Redirect::to('/overview', array('message' => 'logout successful'));
    }

}
