<?php

class Player extends BaseModel {

    public $id, $name, $password, $admin;

    public function __construct($attributes) {
        parent::__construct($attributes);

        $this->validators = array(
            'validate_string_lengths' => array(
                array('min' => 3, 'max' => 20, 'attribute' => 'password'), 
                array('min' => 3, 'max' => 20, 'attribute' => 'name')),
            'name_is_unique',
            'validate_value_is_boolean' => 'admin');
    }

    public static function authenticate($user, $password) {
        $query = DB::connection()->prepare('SELECT * FROM Player WHERE name = :name AND password = :password LIMIT 1');
        $query->execute(array('name' => $user, 'password' => $password));
        $row = $query->fetch();
        if ($row) {
            $player = new Player(array(
                'id' => $row['id'],
                'name' => $row['name'],
                'password' => $row['password'],
                'admin' => $row['admin']
            ));
            return $player;
        }
        return null;
    }
    
    public function name_is_unique() {
        $errors = array();
        if ($this->name != null) {
            if (Item::findByName($this->name) != null) {
                $errors[] = 'Player name already exists!';
            }
        }
        return $errors;
    }

    public static function findAll() {
        $query = DB::connection()->prepare('SELECT * FROM Player');
        $query->execute();

        $rows = $query->fetchAll();
        $players = array();

        foreach ($rows as $row) {
            $players[] = new Player(array(
                'id' => $row['id'],
                'name' => $row['name'],
                'password' => $row['password'],
                'admin' => $row['admin']
            ));
        }
        return $players;
    }

    public static function findById($id) {
        $query = DB::connection()->prepare('SELECT * FROM Player WHERE id = :id LIMIT 1');
        $query->execute(array('id' => $id));

        $row = $query->fetch();
        if ($row) {
            $player = new Player(array(
                'id' => $row['id'],
                'name' => $row['name'],
                'password' => $row['password'],
                'admin' => $row['admin']
            ));
            return $player;
        }
        return null;
    }

    public static function findByName($name) {
        $query = DB::connection()->prepare('SELECT * FROM Player WHERE name = :name LIMIT 1');
        $query->execute(array('name' => $name));

        $row = $query->fetch();
        if ($row) {
            $player = new Player(array(
                'id' => $row['id'],
                'name' => $row['name'],
                'password' => $row['password'],
                'admin' => $row['admin']
            ));
            return $player;
        }
        return null;
    }

    public function add_new() {
        $query = DB::connection()->prepare('INSERT INTO Player (name, password, admin) VALUES (:name, :password, :admin) RETURNING id');
        $query->execute(array(
            'name' => $this->name,
            'password' => $this->password,
            'boolean' => $this->admin));

        $row = $query->fetch();
        $this->id = $row['id'];
    }

    public function rename() {
        $query = DB::connection()->prepare('UPDATE Player SET name = :toWhat WHERE id = :id;');
        $query->execute(array(
            'toWhat' => $this->name,
            'id' => $this->id
        ));
    }

    public function passwordChange() {
        $query = DB::connection()->prepare('UPDATE Player SET password = :toWhat WHERE id = :id;');
        $query->execute(array(
            'toWhat' => $this->password,
            'id' => $this->id
        ));
    }

    public function delete() {
        $query = DB::connection()->prepare('DELETE FROM Player WHERE id = :id;');
        $query->execute(array('id' => $this->id
        ));
    }

}
