<?php

class PlayerController extends BaseController {
    
    public static function login() {
        View::make('login.html');
    }
    
    public static function handle_login() {
        $params = $_POST;
        $player = Player::authenticate($params['user'], $params['password']);
        if (!$player){
            View::make('login.html', array('error' => 'Wrong username or password!', 'player' => $player));
        } else {
            $_SESSION['player'] = $player->id;
            Redirect::to('/overview', array('message' => 'login successful '.$player->name.'!', 'player' => $player));
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
            //    $player->save();

            Redirect::to('/admin', array('message' => 'Player added.'));
        } else {
            Redirect::to('/admin', array('playerErrors' => $errors, 'player_attributes' => $attributes));
        }
    }

    public static function rename() {
        $params = $_POST;
        $player = Player::findById(BaseController::get_user_logged_in());

        if ($player == null) {
            Redirect::to('/overview', array('message' => 'De fuc?', 'player' => $player));
        } else {
            Kint::dump($params);
            $player->name = $params['player_name'];
            $errors = $player->errors();
            if (count($errors) == 0) {
                if ($player->rename('name')) {

                    Redirect::to('/player/' . $player->id, array('messages' => array('Rename succesful!')));
                } else {
                    Redirect::to('/player/' . $player->id, array('messages' => 'lel uncaught error'));
                }
            } else {
                Redirect::to('/player/' . $player->id, array('messages' => $errors));
            }
        }
    }

    public static function deleteSelf() {
        $params = $_POST;
        $player = Player::findById(1);

        if ($player == null) {
            Redirect::to('/overview', array('message' => 'De fuc?', 'player' => $player));
        } else {

            //sqldelete

            PlayerController::handle_logout();
            Redirect::to('/overview', array('message' => 'ciao!'));
        } 
    }

}
