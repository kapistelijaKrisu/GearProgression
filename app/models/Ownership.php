<?php

class Ownership extends BaseModel {

    public $a_id, $i_id, $owned;

    public function _construct($attributes) {
        parent::__construct($attributes);
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

}
