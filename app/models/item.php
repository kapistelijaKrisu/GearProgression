<?php

class Item extends BaseModel {

    public $id, $name;

    public function __construct($attributes) {
        parent::__construct($attributes);

        $this->validators = array(
            'validate_string_lengths' => array(array('min' => 2, 'max' => 20, 'attribute' => 'name'))
        );
    }

    public function check_name_is_unique() {
        $errors = array();
        if ($this->name != null) {
            if (Item::findByName($this->name) != null) {
                $errors[] = 'Item with that name already exists!';
            }
        }
        return $errors;
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

    public function save() {
        $query = DB::connection()->prepare('INSERT INTO Item (name) VALUES (:name) RETURNING id');
        $query->execute(array('name' => $this->name));
        $row = $query->fetch();
        $this->id = $row['id'];
    }

    public function delete() {
        $query = DB::connection()->prepare('DELETE FROM Item WHERE id = :id;');
        $query->execute(array('id' => $this->id));
    }

}
