<?php

class PlayerController extends BaseController {

    public static function myPage($id) {
        $player = Player::findById($id);
        $items = Item::findAll();
        $avatars = Avatar::findByPlayer($id);
        $classes = Clas::all();
        $elements = Element::all();
        View::make('mypage.html', array('avatars' => $avatars, 'items' => $items,
            'classes' => $classes, 'elements' => $elements, 'player' => $player));
    }

    public static function store() {//to admin
        if (BaseController::get_user_logged_in()->admin == false) {
            Redirect::to('/overview', array('message' => 'De fuc?'));
        }
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
        $logged_in = get_user_logged_in();
        if ($logged_in == null || $logged_in->id != $p_id) {
            Redirect::to('/overview', array('message' => 'De fuc?'));
        }
        $avatar = Avatar::findById($a_id);
        if ($avatar == null) {
            Redirect::to('/player/' . $p_id, array('errors' => 'Character does not exist!'));
        }
        if ($avatar->owner_id != $logged_in->id) {
            Redirect::to('/overview', array('message' => 'De fuc?'));
        }
        
        
        $avatar->name = $_POST['avatar_name'];
        $errors = $avatar->errors();


        if (sizeof($errors) == 0) {
            $avatar->changeName();
            Redirect::to('/player/' . $p_id, array('message' => 'Character rename succesful!'));
        } else {
            $att = array($avatar->id => $avatar->name);

            Redirect::to('/player/' . $logged_in_id, array('errors' => $errors, 'attributes' => $att));
        }
    }

    public static function create_character_to_self() {
        $logged_in = parent::get_user_logged_in();
        if ($logged_in == null) {
            Redirect::to('/overview', array('message' => 'De fuc?'));
        }
      
        $main = false;
        if ($_POST['priority'] == 'main') {
            $main = true;
        }
         $attributes = array(
            'name' => $_POST['character'],
            'element' => Element::findByType($_POST['element']),
             'main' => $main,
             'clas' => Clas::findByName($_POST['class']),
             'owner_id' => $logged_in->id,
             
        );
        
         $avatar = new Avatar($attributes);
         $errors = $avatar->errors();
         if (sizeof($errors) == 0) {
            $avatar->save();
            Redirect::to('/player/' . $logged_in->id, array('message' => 'Character has been listed!'));
        } else {
           $att = array();//$avatar->id => $avatar->name);

            Redirect::to('/player/' . $logged_in->id, array('errors' => $errors, 'attributes' => $att));
        }

    }

}
