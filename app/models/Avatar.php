<?php

class Avatar extends BaseModel {

    public $id, $p_id, $p_name, $e_id, $e_type, $c_id, $c_name, $name, $main, $stats;

    public function _construct($attributes) {
        parent::__construct($attributes);
    }

    public static function findAll() {
        $query = DB::connection()->prepare('SELECT '
                . 'Avatar.id, Avatar.name, Avatar.stats, Avatar.main,'
                . ' Avatar.c_id, Avatar.e_id,'
                . ' Player.name AS p_name, '
                . ' Player.id as p_id,'
                . ' Clas.name as c_name,'
                . ' Element.type as e_type'
                . ' FROM Avatar'
                . ' LEFT JOIN Player ON Player.id = Avatar.p_id'
                . ' LEFT JOIN Clas ON Clas.id = Avatar.c_id'
                . ' LEFT JOIN Element ON Element.id = Avatar.e_id');
        $query->execute();

        $rows = $query->fetchAll();
        $avatars = array();

        foreach ($rows as $row) {
            $avatars[] = new Avatar(array(
                'id' => $row['id'],
                'p_id' => $row['p_id'],
                'p_name' => $row['p_name'],
                'c_name' => $row['c_name'],
                'e_id' => $row['e_id'],
                'e_type' => $row['e_type'],
                'c_id' => $row['c_id'],
                'name' => $row['name'],
                'main' => $row['main'],
                'stats' => $row['stats']
            ));
        }
        return $avatars;
    }
    
    public static function findByPlayer($id) {
        $query = DB::connection()->prepare('SELECT '
                . 'Avatar.id, Avatar.name, Avatar.stats, Avatar.main,'
                . ' Avatar.c_id, Avatar.e_id,'
                . ' Player.name AS p_name, '
                . ' Player.id as p_id,'
                . ' Clas.name as c_name,'
                . ' Element.type as e_type'
                . ' FROM Avatar'
                . ' LEFT JOIN Player ON Player.id = Avatar.p_id'
                . ' LEFT JOIN Clas ON Clas.id = Avatar.c_id'
                . ' LEFT JOIN Element ON Element.id = Avatar.e_id'
                . ' WHERE Player.id = :id');
        $query->execute(array('id' => $id));

        $rows = $query->fetchAll();
        $avatars = array();

        foreach ($rows as $row) {
            $avatars[] = new Avatar(array(
                'id' => $row['id'],
                'p_id' => $row['p_id'],
                'p_name' => $row['p_name'],
                'c_name' => $row['c_name'],
                'e_id' => $row['e_id'],
                'e_type' => $row['e_type'],
                'c_id' => $row['c_id'],
                'name' => $row['name'],
                'main' => $row['main'],
                'stats' => $row['stats']
            ));
        }
        return $avatars;
    }

    public static function findOne($id) {
        
        
        $query = DB::connection()->prepare('SELECT '
                . 'Avatar.id, Avatar.name, Avatar.stats, Avatar.main,'
                . ' Avatar.c_id, Avatar.e_id,'
                . ' Player.name AS p_name, '
                . ' Player.id as p_id,'
                . ' Clas.name as c_name,'
                . ' Element.type as e_type'
                . ' FROM Avatar'
                . ' LEFT JOIN Player ON Player.id = Avatar.p_id'
                . ' LEFT JOIN Clas ON Clas.id = Avatar.c_id'
                . ' LEFT JOIN Element ON Element.id = Avatar.e_id'
                . ' WHERE Avatar.id = :id');

        $query->execute(array('id' => $id));

        $row = $query->fetch();
        if ($row) {
            $avatar = new Avatar(array(
                'id' => $row['id'],
                'p_id' => $row['p_id'],
                'p_name' => $row['p_name'],
                'c_name' => $row['c_name'],
                'e_id' => $row['e_id'],
                'e_type' => $row['e_type'],
                'c_id' => $row['c_id'],
                'name' => $row['name'],
                'main' => $row['main'],
                'stats' => $row['stats']
            ));

            return $avatar;
        }
        return null;
    }
    
    public function save() {
        $query = DB::connection()->prepare('INSERT INTO Avatar '
                . '(name, p_id, e_id, c_id, main, stats) VALUES '
                . '(:name, :p_id, :e_id, :c_id, :main, :stats) RETURNING id');
        $query->execute(array(
            'name' => $this->name,
            'p_id' => $this->p_id,
            'e_id' => $this->e_id,
            'c_id' => $this->c_id,
            'main' => $this->main,
            'stats' => $this->stats
                ));
        
        $row = $query->fetch();
        Kint::trace();
        Kint::dump($row);
        $this->id = $row['id'];
    }    

}
