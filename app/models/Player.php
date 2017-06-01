<?php

class Player extends BaseModel {

    public $id, $name, $password, $admin;

    public function _construct($attributes) {
        parent::__construct($attributes);
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

    public function save() {
        $query = DB::connection()->prepare('INSERT INTO Player (name, password, admin) VALUES (:name, :password, :admin) RETURNING id');
        $query->execute(array(
            'name' => $this->name,
            'password' => $this->password,
            'boolean' => $this->admin));
        
        $row = $query->fetch();
        Kint::trace();
        Kint::dump($row);
        $this->id = $row['id'];
    }

}
