<?php

class Clas extends BaseModel {
public $id, $name;

    public function __construct($attributes) {
        parent::__construct($attributes);
    }

    public static function findById($id) {
        $query = DB::connection()->prepare('SELECT * FROM Element WHERE id = :id LIMIT 1');
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
        // Alustetaan kysely tietokantayhteydellämme
        $query = DB::connection()->prepare('SELECT * FROM Clas');
        // Suoritetaan kysely
        $query->execute();
        // Haetaan kyselyn tuottamat rivit
        $rows = $query->fetchAll();
        $classes = array();

        // Käydään kyselyn tuottamat rivit läpi
        foreach ($rows as $row) {
            // Tämä on PHP:n hassu syntaksi alkion lisäämiseksi taulukkoon :)
            $classes[] = new Clas(array(
                'id' => $row['id'],
                'name' => $row['name'],
                            ));
        }

        return $classes;
    }

}
