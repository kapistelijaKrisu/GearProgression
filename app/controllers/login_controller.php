<?php

class LoginController extends BaseController {

    public static function login() {
        View::make('login.html');
    }
    
    public static function authenticate($user, $password) {
        $query = DB::connection()->prepare('SELECT * FROM Player WHERE name = :name AND password = :password LIMIT 1');
        $query->execute(array('name' => $user, 'password' => $password));
        $row = $query->fetch();
        if ($row) {
            $player = new Player(array(
                'id' => $row['id'],
                'name' => $row['name'],
                'password' => $row['password'],
                'admin' => $row['admin']
            ));
            return $player;
        }
        return null;
    }

    public static function handle_login() {
        $params = $_POST;
        $player = LoginController::authenticate($params['user'], $params['password']);
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
