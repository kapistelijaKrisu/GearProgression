<?php

class AdminPlayerController extends BaseController {

    public static function adminPage() {
        $admin = parent::get_user_kick_non_admin();
        $data = array(
            'player' => $admin,
            'players' => Player::findAll());
        View::make('admin_player.html', $data);
    }

    public static function store_player() {//to admin
        parent::get_user_kick_non_admin();
        if (!isset($_POST['player_name'])) {
            Redirect::to('/admin/config', array('errors' => array('missing player_name from post!')));
        }
        $attributes = array(
            'name' => $_POST['player_name'],
            'password' => 'asd',
            'admin' => false
        );
        $player = new Player($attributes);
        $errors = $player->errors();

        if (sizeof($errors) == 0) {
            $errors = array_merge($errors, $player->check_name_is_unique());
            if (count($errors) == 0) {
                $player->store();
                Redirect::to('/admin/player', array('message' => 'Player added. Tell the player default password is: "asd".'));
            }
        }
        Redirect::to('/admin/player', array('errors' => $errors, 'name' => $player->name));
    }

    public static function mod_player() {
        parent::get_user_kick_non_admin();
        parent::check_post_can_int('player', '/admin/player');
        if (!isset($_POST['mod'])) {
            Redirect::to('/admin/player', array('errors' => array('Missing mod value from post')));
        }
        
        if ($_POST['mod'] == 'delete') {
            self::delete_player();
        } else if ($_POST['mod'] == 'reset_password') {
            self::reset_player_password();
        } else {
            Redirect::to('/admin/player', array('errors' => array('This mod value is not registered!')));
        }
    }

    private static function delete_player() {
        $player = Player::findById($_POST['player']);
        if ($player == null) {
            Redirect::to('/admin/player', array('errors' => array('Player does not exist!')));
        } else if ($player->admin == false) {
            $player->delete();
            Redirect::to('/admin/player', array('message' => 'Player deleted!'));
        } else {
            Redirect::to('/admin/player', array('errors' => array('Admins will stay forever!')));
        }
    }

    private static function reset_player_password() {
        $player = Player::findById($_POST['player']);
        if ($player == null) {
            Redirect::to('/admin/player', array('errors' => array('Player does not exist!')));
        } else {
            $player->password = 'asd';
            $player->passwordChange();
            Redirect::to('/admin/player', array('message' => 'Password of ' . $player->name . ' has been reseted to "asd"!'));
        }
    }

}
