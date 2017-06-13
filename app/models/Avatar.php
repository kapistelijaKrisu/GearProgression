<?php

class Avatar extends BaseModel {

    public $id, $p_id, $p_name, $e_id, $e_type, $c_id, $c_name, $name, $main, $stats, $ownerships;

    public function __construct($attributes) {
        parent::__construct($attributes);
        $this->validators = array('validate_string_length' => array('min' => 3, 'max' => 20, 'attribute' => 'p_name'));
    //    $this->validators = array('validate_string_length' => array('min' => 3, 'max' => 20, 'attribute' => 'p_name'));
        $this->validators = array('validate_not_null' => array(
                'name' => $this->name,
                'player' => $this->p_id,
                'element' => $this->e_id,
                'class' => $this->c_id,
                'main' => $this->main             
        ));
    }

    public static function findAll() {
        $query = DB::connection()->prepare('SELECT '
                . 'Avatar.id as a_id, Avatar.name, Avatar.stats, Avatar.main,'
                . ' Avatar.c_id, Avatar.e_id,'
                . ' Player.name AS p_name, '
                . ' Player.id as p_id,'
                . ' Clas.name as c_name,'
                . ' Element.type as e_type,'
                . ' Ownership.i_id,'
                . ' Ownership.owned'
                . ' FROM Avatar LEFT JOIN Ownership ON Avatar.id = Ownership.a_id'
                . ' LEFT JOIN Player ON Player.id = Avatar.p_id'
                . ' LEFT JOIN Clas ON Clas.id = Avatar.c_id'
                . ' LEFT JOIN Element ON Element.id = Avatar.e_id'
                . ' ORDER BY a_id');
        $query->execute();

        $rows = $query->fetchAll();
        $avatars = array();

        $counter = -1234;
        $currentAvatar;
        foreach ($rows as $row) {
            if ($counter != $row['a_id']) {
                $counter = $row['a_id'];


                $ownership = array();
                $ownerships[$row['i_id']] = new Ownership(array(
                        'a_id' => $row['a_id'],
                        'i_id' => $row['i_id'],
                        'owned' => $row['owned']
                ));

                $currentAvatar = new Avatar(array(
                    'id' => $row['a_id'],
                    'p_id' => $row['p_id'],
                    'p_name' => $row['p_name'],
                    'c_name' => $row['c_name'],
                    'e_id' => $row['e_id'],
                    'e_type' => $row['e_type'],
                    'c_id' => $row['c_id'],
                    'name' => $row['name'],
                    'main' => $row['main'],
                    'stats' => $row['stats'],
                    'ownerships' => $ownerships,
                ));
                $avatars[] = $currentAvatar;
            } else {
                $currentAvatar->ownerships[$row['i_id']] = new Ownership(array(
                    'a_id' => $row['a_id'],
                    'i_id' => $row['i_id'],
                    'owned' => $row['owned']
                ));
            }
        }
        return $avatars;
    }

    public static function findByPlayer($id) {

        $query = DB::connection()->prepare('SELECT '
                . 'Avatar.id as a_id, Avatar.name, Avatar.stats, Avatar.main,'
                . ' Avatar.c_id, Avatar.e_id,'
                . ' Player.name AS p_name, '
                . ' Player.id as p_id,'
                . ' Clas.name as c_name,'
                . ' Element.type as e_type,'
                . ' Ownership.i_id,'
                . ' Ownership.owned'
                . ' FROM Avatar LEFT JOIN Ownership ON Avatar.id = Ownership.a_id'
                . ' LEFT JOIN Player ON Player.id = Avatar.p_id'
                . ' LEFT JOIN Clas ON Clas.id = Avatar.c_id'
                . ' LEFT JOIN Element ON Element.id = Avatar.e_id'
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

                $ownership = array();
                $ownerships[$row['i_id']] = new Ownership(array(
                        'a_id' => $row['a_id'],
                        'i_id' => $row['i_id'],
                        'owned' => $row['owned']
                ));

                $currentAvatar = new Avatar(array(
                    'id' => $row['a_id'],
                    'p_id' => $row['p_id'],
                    'p_name' => $row['p_name'],
                    'c_name' => $row['c_name'],
                    'e_id' => $row['e_id'],
                    'e_type' => $row['e_type'],
                    'c_id' => $row['c_id'],
                    'name' => $row['name'],
                    'main' => $row['main'],
                    'stats' => $row['stats'],
                    'ownerships' => $ownerships
                ));
                $avatars[] = $currentAvatar;
            } else {
                $currentAvatar->ownerships[$row['i_id']] = new Ownership(array(
                    'a_id' => $row['a_id'],
                    'i_id' => $row['i_id'],
                    'owned' => $row['owned']
                ));
            }
        }
        return $avatars;
    }

    public static function findOne($id) {



        $query = DB::connection()->prepare('SELECT'
                . ' Avatar.id, Avatar.name, Avatar.stats, Avatar.main,'
                . ' Avatar.c_id, Avatar.e_id,'
                . ' Player.name AS p_name, '
                . ' Player.id as p_id,'
                . ' Clas.name as c_name,'
                . ' Element.type as e_type,'
                . ' Ownership.i_id,'
                . ' Ownership.owned'
                . ' FROM Avatar LEFT JOIN Ownership ON Avatar.id = Ownership.a_id'
                . ' LEFT JOIN Player ON Player.id = Avatar.p_id'
                . ' LEFT JOIN Clas ON Clas.id = Avatar.c_id'
                . ' LEFT JOIN Element ON Element.id = Avatar.e_id'
                . ' WHERE Avatar.id = :id');

        $query->execute(array('id' => $id));

        $rows = $query->fetchAll();

        $currentAvatar = null;
        foreach ($rows as $row) {
            if ($currentAvatar == null) {
                
                $ownership = array();
                $ownerships[$row['i_id']] = new Ownership(array(
                        'a_id' => $row['id'],
                        'i_id' => $row['i_id'],
                        'owned' => $row['owned']
                ));

                $currentAvatar = new Avatar(array(
                    'id' => $row['id'],
                    'p_id' => $row['p_id'],
                    'p_name' => $row['p_name'],
                    'c_name' => $row['c_name'],
                    'e_id' => $row['e_id'],
                    'e_type' => $row['e_type'],
                    'c_id' => $row['c_id'],
                    'name' => $row['name'],
                    'main' => $row['main'],
                    'stats' => $row['stats'],
                    'ownerships' => $ownerships
                ));
            } else {
                $currentAvatar->ownerships[$row['i_id']] = new Ownership(array(
                    'id' => $row['id'],
                    'i_id' => $row['i_id'],
                    'owned' => $row['owned']
                ));
            }
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

}
