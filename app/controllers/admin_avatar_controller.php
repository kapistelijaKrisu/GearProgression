<?php

class AdminAvatarController extends BaseController {

    public static function adminPage() {
        parent::kick_non_admin();
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
        parent::kick_non_admin();
        parent::check_post_can_int('avatar', '/admin/character');

        $avatar = Avatar::findById($_POST['avatar']);
        if ($avatar == null) {
            Redirect::to('/admin/character', array('errors' => array('Character does not exist!')));
        } else {
            $avatar->delete();
            Redirect::to('/admin/character', array('message' => 'Character deleted!'));
        }
    }

    public static function store_avatar() {
        parent::kick_non_admin();

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
