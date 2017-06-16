<?php

class Ownership extends BaseModel {

    public $a_id, $i_id;

    public function _construct($attributes) {
        parent::__construct($attributes);

        $this->validators = array(
            'validate_value_is_int' => array('a_id','i_id'));
    }

    public static function findAvatarOwnerships($id) {
        $query = DB::connection()->prepare('SELECT * FROM OwnerShip WHERE a_id = :id');
        $query->execute(array('id' => $id));
        $rows = $query->fetchAll();
        $ownerships = array();


        foreach ($rows as $row) {

            $ownerships[] = new Ownership(array(
                'a_id' => $row['a_id'],
                'i_id' => $row['i_id'],
                'owned' => $row['owned']
            ));
        }

        return $ownerships;
    }

    public static function findOwnershipsByItemId($id) {
        $query = DB::connection()->prepare('SELECT * FROM OwnerShip WHERE i_id = :id');
        $query->execute(array('id' => $id));
        $rows = $query->fetchAll();
        $ownerships = array();


        foreach ($rows as $row) {

            $ownerships[] = new Ownership(array(
                'a_id' => $row['a_id'],
                'i_id' => $row['i_id'],
                'owned' => $row['owned']
            ));
        }

        return $ownerships;
    }

    public function store() {
        $query = DB::connection()->prepare('INSERT INTO Ownership (a_id, i_id) VALUES (:a_id, :i_id)');

        $query->execute(array(
            'a_id' => $this->a_id,
            'i_id' => $this->i_id
        ));
    }

}
