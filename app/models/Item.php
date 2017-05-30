<?php

class Item extends BaseModel {

    public $id, $name;

    public function _construct($attributes) {
        parent::__construct($attributes);
    }

    public static function findAll() {
        $query = DB::connection()->prepare('SELECT * FROM Item');
        $query->execute();

        $items = array();
        $rows = $query->fetchAll();

        foreach ($rows as $row) {
            $items[] = new Item(array(
                'id' => $row['id'],
                'name' => $row['name']
            ));
        }
        return $items;
    }

    public static function findById($id) {
        $query = DB::connection()->prepare('SELECT * FROM Item WHERE id = :id LIMIT 1');
        $query->execute(array('id' => $id));

        $row = $query->fetch();

        if ($row) {

            $item = new Item(array(
                'id' => $row['id'],
                'name' => $row['name']
            ));
            return $item;
        }
        return null;
    }
    
    public static function findByName($name) {
        $query = DB::connection()->prepare('SELECT * FROM Item WHERE name = :name LIMIT 1');
        $query->execute(array('name' => $name));

        $row = $query->fetch();

        if ($row) {

            $item = new Item(array(
                'id' => $row['id'],
                'name' => $row['name']
            ));
            return $item;
        }
        return null;
    }

}
