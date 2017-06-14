<?php

class Avatar extends BaseModel {

    public $id, $player, $element, $clas, $name, $main, $stats, $ownerships;

    public function __construct($attributes) {
        parent::__construct($attributes);
        $this->ownerships = array();
        $this->validators = array(
            'validate_string_lengths' => array(
                array('min' => 3, 'max' => 20, 'attribute' => 'name')),
            'validate_not_null' => array(
                // 'player' => $this->p_id,
                // 'element' => $this->e_id,
                // 'class' => $this->c_id,
                'main' => $this->main
        ));
    }

    public function addOwnershipFromRow($row) {
        $this->ownerships[$row['i_id']] = new Ownership(array(
            'a_id' => $row['a_id'],
            'i_id' => $row['i_id'],
            'owned' => $row['owned']
        ));
    }

    public static function extractData($row) {
        $ele = new Element(array('id' => $row['e_id'], 'type' => $row['e_type']));
        $clas = new Clas(array('id' => $row['c_id'], 'name' => $row['c_name']));
        $player = new Player(array('id' => $row['p_id'], 'name' => $row['p_name'], 'admin' => $row['p_admin'], 'password' => $row['password']));
        $avatar = new Avatar(array(
            'id' => $row['a_id'],
            'player' => $player,
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
                . ' Player.name AS p_name, '
                . ' Player.id as p_id,'
                . ' Player.admin as p_admin,'
                . ' Player.password as password,'
                . ' Clas.name as c_name,'
                . ' Element.type as e_type,'
                . ' Ownership.i_id,'
                . ' Ownership.owned'
                . ' FROM Avatar LEFT JOIN Ownership ON Avatar.id = Ownership.a_id'
                . ' LEFT JOIN Player ON Player.id = Avatar.p_id'
                . ' LEFT JOIN Clas ON Clas.id = Avatar.c_id'
                . ' LEFT JOIN Element ON Element.id = Avatar.e_id';
    }

    public static function findAll() {
        $query = DB::connection()->prepare(Avatar::getCoreSelect()
                . ' ORDER BY a_id');
        $query->execute();

        $rows = $query->fetchAll();
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

    public static function findByPlayer($id) {

        $query = DB::connection()->prepare(Avatar::getCoreSelect()
                . ' WHERE Player.id = :id'
                . ' ORDER BY a_id');

        $query->execute(array('id' => $id));

        $rows = $query->fetchAll();
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

    public function save() {
        $query = DB::connection()->prepare('INSERT INTO Avatar '
                . '(name, p_id, e_id, c_id, main, stats) VALUES '
                . '(:name, :p_id, :e_id, :c_id, :main, :stats) RETURNING id');
        $query->execute(array(
            'name' => $this->name,
            'p_id' => $this->p_id,
            'e_id' => $this->e_id,
            'c_id' => $this->c_id,
            'main' => $this->main,
            'stats' => $this->stats
        ));

        $row = $query->fetch();
        Kint::trace();
        Kint::dump($row);
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
