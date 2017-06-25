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
                Redirect::to('/admin/player', array('message' => 'Player added. Tell the ' . $player->name . ' default password is: "asd".'));
            }
        }
        Redirect::to('/admin/player', array('errors' => $errors, 'name' => $player->name));
    }

    public static function mod_player() {
        parent::get_user_kick_non_admin();
        if (!isset($_POST['mod']) || !isset($_POST['player'])) {
            Redirect::to('/admin/player', array('errors' => array('Missing mod or player value from post')));
        }
        $jsPlayer = json_decode($_POST['player'], true);
        if ($jsPlayer == null || !isset($jsPlayer['id'])) {
            Redirect::to('/admin/player', array('errors' => array('json of player is invalid')));
        }
        parent::check_param_can_int($jsPlayer['id'], '/admin/player');

        if ($_POST['mod'] == 'delete') {
            self::delete_player($jsPlayer);
        } else if ($_POST['mod'] == 'reset_password') {
            self::reset_player_password($jsPlayer);
        } else if ($_POST['mod'] == 'search') {
             if (!isset($jsPlayer['name'])) {
                 Redirect::to('/admin/player', array('errors' => array('player name missing from json')));
             }
            self::fetchPlayerAvatars($jsPlayer);
        } else {
            Redirect::to('/admin/player', array('errors' => array('This mod value is not registered!')));
        }
    }

    private static function delete_player($jsPlayer) {
        $player = Player::findById($jsPlayer['id']);
        if ($player == null) {
            Redirect::to('/admin/player', array('errors' => array('Player does not exist!')));
        } else if ($player->admin == false) {
            $player->delete();
            Redirect::to('/admin/player', array('message' => 'Player ' . $player->name . ' deleted!'));
        } else {
            Redirect::to('/admin/player', array('errors' => array('Admins will stay forever!')));
        }
    }

    private static function reset_player_password($jsPlayer) {
        $player = Player::findById($jsPlayer['id']);
        if ($player == null) {
            Redirect::to('/admin/player', array('errors' => array('Player does not exist!')));
        } else {
            $player->password = 'asd';
            $player->passwordChange();
            Redirect::to('/admin/player', array('message' => 'Password of ' . $player->name . ' has been reseted to "asd"!'));
        }
    }

    private static function fetchPlayerAvatars($jsPlayer) {
        $avatars = Avatar::findByPlayer($jsPlayer['id']);
        Redirect::to('/admin/player', array('searched' => $jsPlayer['name'], 'avatars' => $avatars, 'message' => 'Search complete! Number of characters "' . $jsPlayer['name'] . '" owns: ' . sizeof($avatars)));
    }

}
