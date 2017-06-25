<?php

class Player extends BaseModel {
    const avatarLimit = 5;
    public $id, $name, $password, $admin;

    public function __construct($attributes) {
        parent::__construct($attributes);

        $this->validators = array(
            'validate_password',
            'validate_name' => array('min' => 4, 'max' => 20, 'attribute' => 'name'),
            'validate_attributes_are_boolean' => array('admin'));
    }

    protected function validate_password() {
        $errors = array();
        if (is_string($this->password)) {
            $minimimPasswordLength = 3;
            if (strlen($this->password) < $minimimPasswordLength) {
                $errors[] = 'Password is too short! Minimum length: '.$minimimPasswordLength;
            }
            return $errors;
        }
        $errors[] = 'Password has to be a string!';
        return $errors;
    }
    
    public function check_name_is_unique() {
        $errors = array();
        $player = Player::findByName($this->name);
        if ($player != null) {
            $errors[] = 'Player name is already used!';
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
    
    public static function avatarCount($player_id) {
        $query = DB::connection()->prepare('SELECT COUNT(Avatar.id) as count FROM Player LEFT JOIN Avatar ON Player.id = Avatar.p_id WHERE Player.id = :id');
        $query->execute(array(
            'id' => $player_id
        ));
        $row = $query->fetch();
        return $row['count'];
    }

    public function store() {
        $query = DB::connection()->prepare('INSERT INTO Player (name, password, admin) VALUES (:name, :password, :admin) RETURNING id');
        
        $admin = 'FALSE';
        if ($this->admin) {
            $admin = 'TRUE';
        }
        
        $query->execute(array(
            'name' => $this->name,
            'password' => $this->password,
            'admin' => $admin));

        $row = $query->fetch();
        $this->id = $row['id'];
    }

    public function rename() {
        $query = DB::connection()->prepare('UPDATE Player SET name = :toWhat WHERE id = :id RETURNING id;');
        $query->execute(array(
            'toWhat' => $this->name,
            'id' => $this->id
        ));
        $row = $query->fetch();
        return $row['id'];
    }

    public function passwordChange() {
        $query = DB::connection()->prepare('UPDATE Player SET password = :toWhat WHERE id = :id RETURNING id');
        $query->execute(array(
            'toWhat' => $this->password,
            'id' => $this->id
        ));
        $row = $query->fetch();
        return $row['id'];
    }

    public function delete() {
        $query = DB::connection()->prepare('DELETE FROM Player WHERE id = :id RETURNING id');
        $query->execute(array('id' => $this->id
        ));
        $row = $query->fetch();
        return $row['id'];
    }

}
