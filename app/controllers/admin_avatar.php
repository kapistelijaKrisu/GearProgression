<?php

class AdminAvatarController extends BaseController {

    public static function adminPage() {
        $admin = parent::get_user_kick_non_admin();
        $data = array(
            'player' => $admin,
            'classes' => Clas::all(),
            'elements' => Element::all(),
            'items' => Item::findAll(),
            'players' => Player::findAll(),
            'avatars' => Avatar::all(array()));
        View::make('admin_avatar.html', $data);
    }

    public static function mod_avatar() {
        parent::get_user_kick_non_admin();
        if (!isset($_POST['mod']) || !isset($_POST['avatar'])) {
            Redirect::to('/admin/character', array('errors' => array('Missing mod or avatar value from post')));
        }
        $jsAvatar = json_decode($_POST['avatar'], true);
        if ($jsAvatar == null || !isset($jsAvatar['id'])) {
            Redirect::to('/admin/character', array('errors' => array('json of avatar is invalid')));
        }

        if ($_POST['mod'] == 'delete') {
            parent::check_param_can_int($jsAvatar['id'], '/admin/character');
            self::delete($jsAvatar);
        } else if ($_POST['mod'] == 'rename') {
            parent::check_param_can_int($jsAvatar['id'], '/admin/character');
            if (!isset($_POST['avatar_name'])) {
                Redirect::to('/admin/character', array('errors' => array('Missing avatar_name from post!')));
            }
            self::renameAvatar($jsAvatar);
        } else if ($_POST['mod'] == 'deleteAll') {
            parent::check_param_can_int($jsAvatar['owner'], '/admin/character');
            self::deleteOwnerOfAvatar($jsAvatar);
        } else if ($_POST['mod'] == 'details') {
            parent::check_param_can_int($jsAvatar['owner'], '/admin/character');
            self::listDetailsOfOwner($jsAvatar);
        } else {
            Redirect::to('/admin/character', array('errors' => array('This mod value is not registered!')));
        }
    }

    private static function delete($jsAvatar) {
        $avatar = Avatar::findById($jsAvatar['id']);
        if ($avatar == null) {
            Redirect::to('/admin/character', array('errors' => array('Character does not exist!')));
        } else {
            $avatar->delete();
            Redirect::to('/admin/character', array('message' => 'Character deleted!'));
        }
    }

    private static function deleteOwnerOfAvatar($jsAvatar) {
        $owner = Player::findById($jsAvatar['owner']);
        if ($owner == null) {
            Redirect::to('/admin/character', array('errors' => array('Player does not exist!')));
        } else if ($owner->admin == true) {
            Redirect::to('/admin/character', array('errors' => array('Admins cannot be deleted!')));
        } else {
            $owner->delete();
            Redirect::to('/admin/character', array('message' => 'Player and all its characters have been deleted!'));
        }
    }

    private static function listDetailsOfOwner($jsAvatar) {
        $player = Player::findById($jsAvatar['owner']);
        if ($player == null) {
            Redirect::to('/admin/character', array('errors' => array('Player does not exist!')));
        } else {
            $avatars = Avatar::findByPlayer($jsAvatar['owner']);
            Redirect::to('/admin/character', array('message' => 'Search completed!', 'owner' => $player, 'owner_avatars' => $avatars));
        }
    }

    private static function renameAvatar($jsAvatar) {
        $avatar = Avatar::findById($jsAvatar['id']);
        if ($avatar == null) {
            Redirect::to('/admin/character' . $p_id, array('errors' => 'Character does not exist!'));
        }
        $avatar->name = $_POST['avatar_name'];
        $errors = $avatar->errors();

        if (sizeof($errors) == 0) {
            $errors = array_merge($errors, $avatar->check_name_is_unique());
            if (sizeof($errors) == 0) {
                $avatar->changeName();
                Redirect::to('/admin/character', array('message' => 'Character rename succesful!'));
            }
        }
        $att = array('avatar_name' => $avatar->name);
        Redirect::to('/admin/character', array('errors' => $errors, 'attributes' => $att));
    }

    public static function store_avatar() {
        $logged_in = parent::get_user_kick_non_admin();
        parent::check_post_can_int('element', '/admin/character');
        parent::check_post_can_int('class', '/admin/character');
        parent::check_post_can_int('player', '/admin/character');
        if (!isset($_POST['character'])) {
            Redirect::to('/admin/character', array('errors' => array('name is missing!')));
        }
        if (!isset($_POST['priority'])) {
            Redirect::to('/admin/character', array('errors' => array('priority is missing!')));
        }
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
