<?php

class PlayerController extends BaseController {

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

    public static function store() {
        $params = $_POST;
        $attributes = array(
            'name' => $params['player_name'],
            'password' => 'asd',
            'admin' => false
        );
        $player = new Player($attributes);
        $errors = $player->errors();

        if (count($errors) == 0) {
            $player->add_new();

            Redirect::to('/admin', array('message' => 'Player added.'));
        } else {
            Redirect::to('/admin', array('errors' => $errors, 'player_attributes' => $attributes));
        }
    }

    public static function rename() {
        $params = $_POST;
        $player = Player::findById(BaseController::get_user_logged_in()->id);

        if ($player == null) {
            Redirect::to('/overview', array('message' => 'De fuc?', 'player' => $player));
        } else {
            $player->name = $params['player_name'];
            $errors = $player->errors();
            if (sizeof($errors) == 0) {
                if ($player->rename()) {

                    Redirect::to('/player/' . $player->id, array('messages' => array('Rename succesful!')));
                } else {
                    Redirect::to('/player/' . $player->id, array('errors' => array('lel uncaught error ' . sizeof($errors))));
                }
            } else {
                $att = array('playerName' => $player->name);

                Redirect::to('/player/' . $player->id, array('errors' => $errors, 'attributes' => $att));
            }
        }
    }

    public static function changePassword() {
        $params = $_POST;
        $player = Player::findById(BaseController::get_user_logged_in()->id);

        if ($player == null) {
            Redirect::to('/overview', array('message' => 'De fuc?', 'player' => $player));
        } else {
            $player->password = $params['player_pass'];
            $errors = $player->errors();
            if (sizeof($errors) == 0) {
                $player->passwordChange();
                Redirect::to('/player/' . $player->id, array('message' => 'Password change succesful!'));
            } else {
                $att = array('playerName' => $player->name);

                Redirect::to('/player/' . $player->id, array('errors' => $errors, 'attributes' => $att));
            }
        }
    }

    public static function deleteSelf() {
        $params = $_SESSION;
        $player = Player::findById($params['player']);

        if ($player == null && $player->admin == false) {
            Redirect::to('/overview', array('message' => 'De fuc?'));
        } else {

            $player->delete();
            $_SESSION['player'] = null;
            Redirect::to('/overview', array('message' => 'ciao!'));
        }
    }

    public static function renameOwnedChar($p_id, $a_id) {
        $logged_in_id = $_SESSION['player'];
        $avatar = Avatar::findById($a_id);
        
        if ($avatar == null || $avatar->p_id != $logged_in_id) {
            Redirect::to('/overview', array('message' => 'De fuc?', 'player' => $player));
        } else {
            $avatar->name = $_POST['avatar_name'];
            $errors = $avatar->errors();
        
            
            if (sizeof($errors) == 0) {
                $avatar->changeName();
                Redirect::to('/player/' . $p_id, array('message' => 'Character rename succesful!'));
            } else {
                $att = array($avatar->id => $avatar->name);
             
                Redirect::to('/player/' . $logged_in_id, array('errors' => $errors, 'attributes' => $att));
                Kint::dump($att);
            }
            /*
            Kint::dump($a_id);
            Kint::dump($p_id);
            Kint::dump($avatar);
            Kint::dump($att);*/
        }
    }
}