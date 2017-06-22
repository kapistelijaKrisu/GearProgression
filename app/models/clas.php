<?php

class Clas extends BaseModel {

    public $id, $name;

    public function __construct($attributes) {
        parent::__construct($attributes);

        $this->validators = array(
            'validate_string_lengths' => array(array('min' => 2, 'max' => 20, 'attribute' => 'name'))
        );
    }

    public function check_name_is_unique() {
        $errors = array();
        $clas = Clas::findByName($this->name);
        if ($clas != null) {
            $errors[] = 'Class name is already used!';
        }
        return $errors;
    }

    public static function findById($id) {
        $query = DB::connection()->prepare('SELECT * FROM Clas WHERE id = :id LIMIT 1');
        $query->execute(array('id' => $id));
        $row = $query->fetch();

        if ($row) {
            $clas = new Clas(array(
                'id' => $row['id'],
                'name' => $row['name'],
            ));

            return $clas;
        }

        return null;
    }

    public static function findByName($name) {
        $query = DB::connection()->prepare('SELECT * FROM Clas WHERE name = :name LIMIT 1');
        $query->execute(array('name' => $name));
        $row = $query->fetch();

        if ($row) {
            $clas = new Clas(array(
                'id' => $row['id'],
                'name' => $row['name'],
            ));

            return $clas;
        }

        return null;
    }

    public static function all() {
        $query = DB::connection()->prepare('SELECT * FROM Clas');
        $query->execute();
        $rows = $query->fetchAll();
        $classes = array();

        foreach ($rows as $row) {
            $classes[] = new Clas(array(
                'id' => $row['id'],
                'name' => $row['name']));
        }
        return $classes;
    }

    public function save() {
        $query = DB::connection()->prepare('INSERT INTO Clas (name) VALUES (:name) RETURNING id');
        $query->execute(array('name' => $this->name));
        $row = $query->fetch();
        Kint::trace();
        Kint::dump($row);
        $this->id = $row['id'];
    }

    public function delete() {
        $query = DB::connection()->prepare('DELETE FROM Clas WHERE id = :id RETURNING id');
        $query->execute(array('id' => $this->id));
        $row = $query->fetch();
        return $row['id'];
    }

}
