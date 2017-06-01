<?php

class Element extends BaseModel {

    public $id, $type;

    public function __construct($attributes) {
        parent::__construct($attributes);
    }

    public static function findById($id) {
        $query = DB::connection()->prepare('SELECT * FROM Element WHERE id = :id LIMIT 1');
        $query->execute(array('id' => $id));
        $row = $query->fetch();

        if ($row) {
            $element = new Element(array(
                'id' => $row['id'],
                'type' => $row['type'],
            ));

            return $element;
        }

        return null;
    }
    
    public static function findByType($type) {
        $query = DB::connection()->prepare('SELECT * FROM Element WHERE type = :type LIMIT 1');
        $query->execute(array('type' => $type));
        $row = $query->fetch();

        if ($row) {
            $element = new Element(array(
                'id' => $row['id'],
                'type' => $row['type'],
            ));

            return $element;
        }

        return null;
    }

    public static function all() {
        // Alustetaan kysely tietokantayhteydellämme
        $query = DB::connection()->prepare('SELECT * FROM Element');
        // Suoritetaan kysely
        $query->execute();
        // Haetaan kyselyn tuottamat rivit
        $rows = $query->fetchAll();
        $elements = array();

        // Käydään kyselyn tuottamat rivit läpi
        foreach ($rows as $row) {
            // Tämä on PHP:n hassu syntaksi alkion lisäämiseksi taulukkoon :)
            $elements[] = new Element(array(
                'id' => $row['id'],
                'type' => $row['type'],
                            ));
        }

        return $elements;
    }
    
    public function save() {
        $query = DB::connection()->prepare('INSERT INTO Element (type) VALUES (:type) RETURNING id');
        $query->execute(array('type' => $this->type));
        $row = $query->fetch();
        Kint::trace();
        Kint::dump($row);
        $this->id = $row['id'];
    }
}
