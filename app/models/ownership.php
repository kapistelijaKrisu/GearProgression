<?php

class Ownership extends BaseModel {

    public $a_id, $i_id;

    public function __construct($attributes) {
        parent::__construct($attributes);

        $this->validators = array(
            'validate_values_are_int' => array('a_id', 'i_id'));
    }

    public static function exctractRow($row) {
        $ownership = new Ownership(array(
            'a_id' => $row['a_id'],
            'i_id' => $row['i_id'],
        ));
        return $ownership;
    }

    public static function exctractManyRows($rows) {
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

    public static function findOne($a_id, $i_id) {
        $query = DB::connection()->prepare('SELECT * FROM OwnerShip WHERE a_id = :a_id AND i_id = :i_id LIMIT 1');
        $query->execute(array('a_id' => $a_id, 'i_id' => $i_id));
        $row = $query->fetch();
        return Ownership::exctractRow($row);
    }

    public static function findAvatarOwnerships($id) {
        $query = DB::connection()->prepare('SELECT * FROM OwnerShip WHERE a_id = :id');
        $query->execute(array('id' => $id));
        $rows = $query->fetchAll();
        return Ownership::exctractManyRows($rows);
    }

    public static function findOwnershipsByItemId($id) {
        $query = DB::connection()->prepare('SELECT * FROM OwnerShip WHERE i_id = :id');
        $query->execute(array('id' => $id));
        $rows = $query->fetchAll();
        $ownerships = array();
        return Ownership::exctractManyRows($rows);
    }

    public function store() {
        $query = DB::connection()->prepare('INSERT INTO Ownership (a_id, i_id) VALUES (:a_id, :i_id)');

        $query->execute(array(
            'a_id' => $this->a_id,
            'i_id' => $this->i_id
        ));
    }
    public function delete() {
        $query = DB::connection()->prepare('DELETE FROM Ownership WHERE a_id = :a_id AND i_id = :i_id RETURNING a_id');
        $query->execute(array(
            'a_id' => $this->a_id,
            'i_id' => $this->i_id
        ));
        $row = $query->fetch();
        return $row['a_id'];
    }

}
