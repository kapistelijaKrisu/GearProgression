<?php

class Avatar extends BaseModel {

    const SELECT_START_PART_PSQL = 'SELECT'
            . ' Avatar.id, Avatar.name, Avatar.main,'
            . ' Avatar.c_id, Avatar.e_id,'
            . ' Avatar.p_id,'
            . ' Clas.name as c_name,'
            . ' Element.name as e_name,'
            . ' Item.id as i_id,'
            . ' Item.name as i_name'
            . ' FROM Avatar'
            . ' LEFT JOIN Ownership ON Avatar.id = Ownership.a_id'
            . ' LEFT JOIN Item ON Ownership.i_id = Item.id'
            . ' INNER JOIN Clas ON Clas.id = Avatar.c_id'
            . ' INNER JOIN Element ON Element.id = Avatar.e_id ';
    const ORDER_BY_PSQL = ' ORDER BY Avatar.main desc, Avatar.name, Avatar.id';
    
    const CATEGORY_TO_PSQL = array(
        'Element' => 'Element.id = :id ',
        'Clas' => 'Clas.id = :id ',
        'main' => 'Avatar.main = :id ',
        'Item' => 'Avatar.id NOT IN (SELECT Avatar.id FROM Avatar LEFT JOIN Ownership ON Avatar.id = Ownership.a_id WHERE ownership.i_id = :id) ');

    public $id, $owner_id, $element, $clas, $name, $main, $ownerships;

    public function __construct($attributes) {
        parent::__construct($attributes);
        if ($this->ownerships == null) {
            $this->ownerships = array();
        }
        $this->validators = array(
            'validate_name' => array('min' => 3, 'max' => 20, 'attribute' => 'name'),
            'validate_attributes_are_boolean' => array('main'),
            'validate_values_are_int' => array('owner_id'),
            'validate_object_classes_are_correct' => array('Element' => 'element', 'Clas' => 'clas')
        );
    }

    // error checks that are not always needed

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
                $errors[] = 'Only admin can have more than one main!';
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

    // database related from here
    public function addOwnershipFromRow($row) {
        $item = new Item(array(
            'name' => $row['i_name'],
            'id' => $row['i_id'],
        ));
        $this->ownerships[$row['i_id']] = $item;
    }

    public static function extractAvatar($row) {
        $ele = new Element(array('id' => $row['e_id'], 'name' => $row['e_name']));
        $clas = new Clas(array('id' => $row['c_id'], 'name' => $row['c_name']));
        $avatar = new Avatar(array(
            'id' => $row['id'],
            'owner_id' => $row['p_id'],
            'clas' => $clas,
            'element' => $ele,
            'name' => $row['name'],
            'main' => $row['main']));
        return $avatar;
    }

    public static function loop_many($rows) {
        $avatars = array();

        $counter = -1234;
        $currentAvatar;
        foreach ($rows as $row) {
            if ($counter != $row['id']) {
                $counter = $row['id'];

                $currentAvatar = Avatar::extractAvatar($row);
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
                $currentAvatar = Avatar::extractAvatar($row);
            }
            $currentAvatar->addOwnershipFromRow($row);
        }
        return $currentAvatar;
    }

    public static function get_main_avatars($player_id) {
        $query = DB::connection()->prepare(Avatar::SELECT_START_PART_PSQL
                . ' WHERE Avatar.p_id = :id'
                . ' AND Avatar.main = TRUE'
                . Avatar::ORDER_BY_PSQL);
        $query->execute(array('id' => $player_id));
        $rows = $query->fetchAll();
        return Avatar::loop_many($rows);
    }

    public static function get_avatar_by_name($name) {
        $query = DB::connection()->prepare(Avatar::SELECT_START_PART_PSQL
                . ' WHERE Avatar.name = :name'
                . Avatar::ORDER_BY_PSQL);
        $query->execute(array('name' => $name));
        $rows = $query->fetchAll();
        return Avatar::loop_single($rows);
    }

    public static function all($options) {
        $queryString = Avatar::SELECT_START_PART_PSQL;
        $param_arr = array();
        $whereAsBoolean = true;
        if (isset($options['category']) && isset($options['category_value'])) {
            $queryString = self::add_where_or_and_to_query($queryString, $whereAsBoolean);
            
            $param_arr['id'] = $options['category_value'];
            $queryString .= Avatar::CATEGORY_TO_PSQL[$options['category']];
            $where_is_set = true;
        }
        if (isset($options['search'])) {
            $queryString = self::add_where_or_and_to_query($queryString, $whereAsBoolean);
            $queryString .= 'Avatar.name LIKE :like ';
            $param_arr['like'] = '%' . $options['search'] . '%';
           // $where_is_set = true; ---add this more options come after
        }
        $queryString .= Avatar::ORDER_BY_PSQL;
        $query = DB::connection()->prepare($queryString);
        $query->execute($param_arr);
        $rows = $query->fetchAll();
        return Avatar::loop_many($rows);
    }
    
    private static function add_where_or_and_to_query($queryString, $whereAsBoolean) {
        if ($whereAsBoolean) {
            return $queryString.' WHERE ';
        } else {
            return $queryString. ' AND ';
        }
    }

    public static function findByPlayer($id) {

        $query = DB::connection()->prepare(Avatar::SELECT_START_PART_PSQL
                . ' WHERE p_id = :id'
                . Avatar::ORDER_BY_PSQL);
        $query->execute(array('id' => $id));
        $rows = $query->fetchAll();
        return Avatar::loop_many($rows);
    }

    public static function findById($id) {
        $query = DB::connection()->prepare(Avatar::SELECT_START_PART_PSQL
                . ' WHERE Avatar.id = :id');
        $query->execute(array('id' => $id));
        $rows = $query->fetchAll();

        $currentAvatar = null;
        foreach ($rows as $row) {
            if ($currentAvatar == null) {
                $currentAvatar = Avatar::extractAvatar($row);
                $avatars[] = $currentAvatar;
            }
            $currentAvatar->addOwnershipFromRow($row);
        }
        return $currentAvatar;
    }

    public function store() {
        $query = DB::connection()->prepare('INSERT INTO Avatar '
                . '(name, p_id, e_id, c_id, main) VALUES '
                . '(:name, :p_id, :e_id, :c_id, :main) RETURNING id');

        $main = 'FALSE';
        if ($this->main) {
            $main = 'TRUE';
        }
        $query->execute(array(
            'name' => $this->name,
            'p_id' => $this->owner_id,
            'e_id' => $this->element->id,
            'c_id' => $this->clas->id,
            'main' => $main
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
