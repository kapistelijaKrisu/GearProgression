<?php

class PlayerController extends BaseController {

    public static function myPage($id) {
        $logged_in = parent::get_user_logged_in();
        parent::check_param_can_int($id, '/home');
        if ($logged_in == null) {
            Redirect::to('/overview', array('errors' => array('Log in first to see your page!')));
        } else if ($logged_in->id != $id) {
            Redirect::to('/overview', array('errors' => array('You can only access your own page...not others!')));
        }
        $items = Item::findAll();
        $avatars = Avatar::findByPlayer($id);
        $classes = Clas::all();
        $elements = Element::all();
        View::make('player.html', array('avatars' => $avatars, 'items' => $items,
            'classes' => $classes, 'elements' => $elements, 'player' => $logged_in));
    }

    public static function renamePlayer() {
        $player = parent::get_user_logged_in();
        if ($player == null) {
            Redirect::to('/overview', array('errors' => array('Not logged in!')));
        }
        if (!isset($_POST['name'])) {
            Redirect::to('/overview', array('errors' => array('Missing name from post!')));
        }

        $player->name = $_POST['name'];
        $errors = $player->errors();
        if (sizeof($errors) == 0) {
            array_merge($errors, $player->check_name_is_unique());
            if (sizeof($errors) == 0) {

                $player->rename();
                Redirect::to('/player/' . $player->id, array('message' => 'You have renamed yourself!'));
            }
        }
        $att = array('playerName' => $player->name);
        Redirect::to('/player/' . $player->id, array('errors' => $errors, 'attributes' => $att));
    }

    public static function changePassword() {
        $player = parent::get_user_logged_in();

        if ($player == null) {
            Redirect::to('/overview', array('errors' => array('You are not logged in!')));
        }
        if (!isset($_POST['password'])) {
            Redirect::to('/player/' . $player->id, array('errors' => array('missing player_pass from form')));
        }
        $player->password = $_POST['password'];
        $errors = $player->errors();
        if (sizeof($errors) == 0) {
            $player->passwordChange();
            Redirect::to('/player/' . $player->id, array('message' => 'Password change succesful!'));
        } else {
            $att = array('password' => $player->password);
            Redirect::to('/player/' . $player->id, array('errors' => $errors, 'attributes' => $att));
        }
    }

    public static function deleteSelf() {
        $player = parent::get_user_logged_in();

        if ($player == null) {
            Redirect::to('/overview', array('errors' => array('How can you delete yourself when you do not exist?')));
        } else if ($player->admin == true) {
            Redirect::to('/player/' . $player->id, array('errors' => array('Admins are stuck here forever!')));
        }

        $player->delete();
        $_SESSION['player'] = null;
        Redirect::to('/overview', array('message' => 'Ciao!'));
    }

    public static function renameOwnedAvatar($p_id, $a_id) {
        $logged_in = parent::get_user_logged_in();
        parent::check_param_can_int($p_id, '/home');
        parent::check_param_can_int($a_id, '/home');
        if ($logged_in == null || $logged_in->id != $p_id) {
            Redirect::to('/home', array('errors' => array('Not yours!')));
        }
        if (!isset($_POST['avatar_name'])) {
            Redirect::to('/home', array('errors' => array('missing avatar_name form form!')));
        }
        $avatar = Avatar::findById($a_id);
        if ($avatar == null) {
            Redirect::to('/player/' . $p_id, array('errors' => array('Character does not exist!')));
        }
        if ($avatar->owner_id != $logged_in->id) {
            Redirect::to('/overview', array('message' => 'Not yours!'));
        }

        $avatar->name = $_POST['avatar_name'];
        $errors = $avatar->errors();

        if (sizeof($errors) == 0) {
            $errors = array_merge($errors, $avatar->check_name_is_unique());
            if (sizeof($errors) == 0) {
                $avatar->changeName();
                Redirect::to('/player/' . $p_id, array('message' => 'Character rename succesful!'));
            }
        }

        Redirect::to('/player/' . $logged_in->id, array('errors' => $errors, 'att' => array($avatar->id, $avatar->name)));
    }

    public static function create_character_to_self() {
        $logged_in = parent::get_user_logged_in();
        if ($logged_in == null) {
            Redirect::to('/overview', array('errors' => array('Log in first to see your page!')));
        }
        parent::check_post_can_int('element', '/player/' . $logged_in->id);
        parent::check_post_can_int('class', '/player/' . $logged_in->id);
        if (!isset($_POST['character'])) {
            Redirect::to('/player/' . $logged_in->id, array('errors' => array('name is missing!')));
        }
        if (!isset($_POST['priority'])) {
            Redirect::to('/player/' . $logged_in->id, array('errors' => array('priority is missing!')));
        }
        $main = false;
        if ($_POST['priority'] == 'main') {
            $main = true;
        }
        $attributes = array(
            'name' => $_POST['character'],
            'element' => Element::findById($_POST['element']),
            'main' => $main,
            'clas' => Clas::findById($_POST['class']),
            'owner_id' => $logged_in->id,
        );

        $avatar = new Avatar($attributes);
        $errors = $avatar->errors();

        if (sizeof($errors) == 0) {
            $errors = array_merge($errors, $avatar->check_name_is_unique());
            $errors = array_merge($errors, $avatar->check_non_admin_main_avatar());
            if (Player::avatarCount($logged_in->id) >= Player::avatarLimit && $logged_in->admin == false) {
                $errors[] = 'You don\'t have the rights to create over 5 characters';
            }

            if (sizeof($errors) == 0) {
                $avatar->store();
                $items = Item::findAll();
                foreach ($items as $item) {
                    if (isset($_POST[$item->id])) {
                        $toSave = new Ownership(array('a_id' => $avatar->id, 'i_id' => $item->id));
                        $toSave->store();
                    }
                }
                Redirect::to('/player/' . $logged_in->id, array('message' => 'Character has been listed!'));
            }
        }
        $arr = array();
        $arr['charName'] = $avatar->name;
        Redirect::to('/player/' . $logged_in->id, array('errors' => $errors, 'attributes' => $arr));
    }

}
