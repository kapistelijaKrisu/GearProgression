<?php


class AdminAvatarController extends BaseController {
    public static function adminPage() {
        parent::adminCheck();
        $everything = array(
            'player' => parent::get_user_logged_in(),
            'classes' => Clas::all(),
            'elements' => Element::all(),
            'items' => Item::findAll(),
            'players' => Player::findAll(),
            'avatars' => Avatar::all(array()));
        View::make('admin_avatar.html', $everything);
    }
    public static function delete() {
        parent::adminCheck();
        parent::check_post_can_int('avatar', '/admin/character');
        
        if (isset($_POST['delete'])) {
            if ($_POST['delete'] == 'player') {
                self::delete_player();
            }else if ($_POST['delete'] == 'character') {
                self::delete_character();
            } else {
                Redirect::to('/admin/character', array('errors' => 'This mod value is not registered!'));
            }
        } else {
            Redirect::to('/admin/character', array('errors' => 'Missing mod value from post'));
        }
        
       
    }
    
    private static function delete_character() {
         $avatar = Avatar::findById($_POST['avatar']);

        if ($avatar == null) {
            Redirect::to('/admin/character', array('errors' => array('Character does not exist!')));
        } else {
            $avatar->delete();
            Redirect::to('/admin/character', array('message' => 'Character deleted!'));
        }
    }
    
    private static function delete_player() {
         $player = Player::findById(Avatar::findById($_POST['avatar'])->owner_id);

        if ($player == null) {
            Kint::dump($player, $_POST);
            Redirect::to('/admin/character', array('errors' => array('Character does not exist!')));
        } else if ($player->admin) {
            Redirect::to('/admin/character', array('message' => 'Admins will stay forever!'));
        } else {
            $avatar->delete();
            Redirect::to('/admin/character', array('message' => 'Character deleted!'));
        }
    }

    public static function store_avatar() {
        parent::adminCheck();

        $main = false;
        if ($_POST['priority'] == 'main') {
            $main = true;
        }
        $avatar_att = array(
            'name' => $_POST['character'],
            'element' => Element::findById($_POST['element']),
            'main' => $main,
            'clas' => Clas::findById($_POST['class']),
            'owner_id' => $_POST['player']
        );


        $avatar = new Avatar($avatar_att);
        $errors = $avatar->errors();

        if (sizeof($errors) == 0) {
            $errors = array_merge($errors, $avatar->check_name_is_unique());
            $errors = array_merge($errors, $avatar->check_non_admin_avatar_limit_count());
            $errors = array_merge($errors, $avatar->check_non_admin_main_avatar());
            if (sizeof($errors) == 0) {
                $avatar->store();
                $items = Item::findAll();

                foreach ($items as $item) {

                    if (isset($_POST[$item->id])) {
                        parent::check_post_can_int($item->id, '/admin');
                        $toSave = new Ownership(array('a_id' => $avatar->id, 'i_id' => $item->id));
                        $toSave->store();
                    }
                }
                Redirect::to('/admin/character', array('message' => 'Character created!'));
            }
        }
        Redirect::to('/admin/character', array('errors' => $errors, 'name' => $avatar->name));
    }

}
