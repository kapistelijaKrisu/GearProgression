<?php

class Avatar extends BaseModel {

    public $id, $owner_id, $element, $clas, $name, $main, $stats, $ownerships;

    public function __construct($attributes) {
        parent::__construct($attributes);
        $this->ownerships = array();
        $this->validators = array(
            'validate_string_lengths' => array(
                array('min' => 3, 'max' => 20, 'attribute' => 'name')),
            'classes_are_correct' => array(
                'Element' => 'element',
                'Clas' => 'clas'),
            'validate_value_is_boolean' => 'main',
            'check_name_is_unique',
            'check_non_admin_main_avatar',
            'check_non_admin_avatar_limit_count' => 10
        );
    }

    public function check_non_admin_avatar_limit_count($setLimit) {
        $errors = array();
        if (Player::findById($this->owner_id)->admin == true) {
            return $errors;
        }
        $avatars = Avatar::findByPlayer($this->owner_id);
        if (sizeof($avatars) == $setLimit) {
            if ($this->id == null || Avatar::findById($this->id) == null) {
                $errors[] = 'pleb can have only ' . $setLimit . ' characters!';
            }
        } else if (sizeof($avatars) > $setLimit) {
            $errors[] = 'how do u have more than ' . $setLimit . ' character(s) without admin rights!';
        }
        return $errors;
    }

    public function check_non_admin_main_avatar() {
        $errors = array();
        if ($this->main == false) {
            return $errors;
        }
        if (Player::findById($this->owner_id)->admin == true) {
            return $errors;
        }
        $found_mains = Avatar::get_main_avatars($this->owner_id);
        if (sizeof($found_mains) == 1) {
            if ($this->id == null || $this->id != $found_mains[0])
                $errors[] = 'pleb can have only one main!';
        } else if (sizeof($found_mains) > 1) {
            $errors[] = 'how do u have more than one main character without admin rights!';
        }
        return $errors;
    }

    public function check_name_is_unique() {
        $errors = array();
        $avatar = Avatar::get_avatar_by_name($this->name);
        if ($avatar != null) {
            $errors[] = 'Character name is already used!';
        }
        return $errors;
    }

    public function addOwnershipFromRow($row) {
        $item = new Item(array(
            'name' => $row['i_name'],
            'id' => $row['i_id'],
        ));
        $this->ownerships[$row['i_id']] = $item;
 
    }

    public static function extractData($row) {
        $ele = new Element(array('id' => $row['e_id'], 'type' => $row['e_type']));
        $clas = new Clas(array('id' => $row['c_id'], 'name' => $row['c_name']));
        $avatar = new Avatar(array(
            'id' => $row['a_id'],
            'owner_id' => $row['p_id'],
            'clas' => $clas,
            'element' => $ele,
            'name' => $row['name'],
            'main' => $row['main'],
            'stats' => $row['stats']));
        return $avatar;
    }

    public static function getCoreSelect() {
        return 'SELECT'
                . ' Avatar.id as a_id, Avatar.name, Avatar.stats, Avatar.main,'
                . ' Avatar.c_id, Avatar.e_id,'
                . ' Player.id as p_id,'
                . ' Clas.name as c_name,'
                . ' Element.type as e_type,'
                . ' Item.id as i_id,'
                . ' Item.name as i_name'
                . ' FROM Avatar LEFT JOIN Ownership ON Avatar.id = Ownership.a_id'
                . ' LEFT JOIN Item ON Ownership.i_id = Item.id'
                . ' LEFT JOIN Player ON Player.id = Avatar.p_id'
                . ' LEFT JOIN Clas ON Clas.id = Avatar.c_id'
                . ' LEFT JOIN Element ON Element.id = Avatar.e_id';
    }

    public static function loop_many($rows) {
        $avatars = array();

        $counter = -1234;
        $currentAvatar;
        foreach ($rows as $row) {
            if ($counter != $row['a_id']) {
                $counter = $row['a_id'];

                $currentAvatar = Avatar::extractData($row);
                $avatars[] = $currentAvatar;
            }
            $currentAvatar->addOwnershipFromRow($row);
        }
        return $avatars;
    }

    public static function loop_single($rows) {
        $currentAvatar = null;
        foreach ($rows as $row) {
            if ($currentAvatar == null) {
                $currentAvatar = Avatar::extractData($row);
                $avatars[] = $currentAvatar;
            }
            $currentAvatar->addOwnershipFromRow($row);
        }
       
        return $currentAvatar;
    }

    public static function get_main_avatars($player_id) {
        $query = DB::connection()->prepare(Avatar::getCoreSelect()
                . ' WHERE Player.id = :id'
                . ' AND Avatar.main = TRUE'
                . ' ORDER BY a_id');
        $query->execute(array('id' => $player_id));
        $rows = $query->fetchAll();
        return Avatar::loop_many($rows);
    }

    public static function get_avatar_by_name($name) {
        $query = DB::connection()->prepare(Avatar::getCoreSelect()
                . ' WHERE Avatar.name = :name');
        $query->execute(array('name' => $name));
        $rows = $query->fetchAll();
        return Avatar::loop_single($rows);
    }

    public static function findAll() {
        $query = DB::connection()->prepare(Avatar::getCoreSelect()
                . ' ORDER BY a_id');
        $query->execute();
        $rows = $query->fetchAll();
        return Avatar::loop_many($rows);
    }

    public static function findByPlayer($id) {

        $query = DB::connection()->prepare(Avatar::getCoreSelect()
                . ' WHERE Player.id = :id'
                . ' ORDER BY a_id');
        $query->execute(array('id' => $id));
        $rows = $query->fetchAll();
        return Avatar::loop_many($rows);
    }

    public static function findById($id) {
        $query = DB::connection()->prepare(Avatar::getCoreSelect()
                . ' WHERE Avatar.id = :id');
        $query->execute(array('id' => $id));
        $rows = $query->fetchAll();

        $currentAvatar = null;
        foreach ($rows as $row) {
            if ($currentAvatar == null) {
                $currentAvatar = Avatar::extractData($row);
                $avatars[] = $currentAvatar;
            }
            $currentAvatar->addOwnershipFromRow($row);
        }
        return $currentAvatar;
    }

    public function store() {
        $query = DB::connection()->prepare('INSERT INTO Avatar '
                . '(name, p_id, e_id, c_id, main, stats) VALUES '
                . '(:name, :p_id, :e_id, :c_id, :main, :stats) RETURNING id');

        $main = 'FALSE';
        if ($this->main) {
            $main = 'TRUE';
        }
        $query->execute(array(
            'name' => $this->name,
            'p_id' => $this->owner_id,
            'e_id' => $this->element->id,
            'c_id' => $this->clas->id,
            'main' => $main,
            'stats' => $this->stats
        ));

        $row = $query->fetch();
        $this->id = $row['id'];
    }

    public function delete() {
        $query = DB::connection()->prepare('DELETE FROM Avatar WHERE id = :id;');
        $query->execute(array('id' => $this->id));
    }

    public function changeName() {
        $query = DB::connection()->prepare('UPDATE Avatar SET name = :toWhat WHERE id = :id;');
        $query->execute(array(
            'toWhat' => $this->name,
            'id' => $this->id
        ));
    }

}
