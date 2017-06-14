<?php

class Ownership extends BaseModel {

    public $a_id, $i_id, $owned;

    public function _construct($attributes) {
        parent::__construct($attributes);

        $this->validators = array(
            'validate_references' => null,
            'validate_not_null' => array('owner'));
    }

    public function validate_references() {
        if (is_int($this->a_id) && is_int($this->i_id)) {
            if (Ownership::findAvatarOwnerships($this->a_id) == null) {
                $errors[] = 'This character id does not exist!';
            }
            if (Ownership::findAvatarOwnerships($this->i_id) == null) {
                $errors[] = 'This item id does not exist!';
            }
            
        } else {
            $errors[] = 'ids have to be integer!';
        }
        return $errors;
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

    public function update() {
        $query = DB::connection()->prepare('UPDATE Ownership SET owned = :toWhat WHERE p_id = :p_id AND a_id = :a_id');
        $query->execute(array(
            'toWhat' => $this->owned,
            'a_id' => $this->a_id,
            'p_is' => $this->i_id
        ));
    }

}
