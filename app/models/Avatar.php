<?php

class Avatar extends BaseModel {
    public $id, $p_id, $e_id, $c_id, $name, $main, $stats;
    
    public function _construct($attributes) {
        parent::__construct($attributes);
    }
    
    public static function findAll() {
        $query = DB::connection()->prepare('SELECT * FROM Avatar');
        $query->execute();
        
        $rows = $query->fetchAll();
        $avatars = array();
        
        foreach($rows as $row) {
            $avatars[] = new Avatar(array(
                'id' => $row['id'],
                'p_id' => $row['p_id'],
                'e_id' => $row['e_id'],
                'c_id' => $row['c_id'],
                'name' => $row['name'],
                'main' => $row['main'],
                'stats' => $row['stats']
            ));
        }
        return $avatars;
    }
}
