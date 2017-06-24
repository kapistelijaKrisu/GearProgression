<?php

class Element extends BaseModel {

    public $id, $name;

    public function __construct($attributes) {
        parent::__construct($attributes);
        
        $this->validators = array(
            'validate_name' => array('min' => 3, 'max' => 20, 'attribute' => 'name'),
            );
    }
    
    public function check_name_is_unique() {
        $errors = array();
        $element = Element::findByName($this->name);
        if ($element != null) {
            $errors[] = 'Element name is already used!';
        }
        return $errors;
    }

    public static function findById($id) {
        $query = DB::connection()->prepare('SELECT * FROM Element WHERE id = :id LIMIT 1');
        $query->execute(array('id' => $id));
        $row = $query->fetch();

        if ($row) {
            $element = new Element(array(
                'id' => $row['id'],
                'name' => $row['name'],
            ));

            return $element;
        }

        return null;
    }
    
    public static function findByName($name) {
        $query = DB::connection()->prepare('SELECT * FROM Element WHERE name = :name LIMIT 1');
        $query->execute(array('name' => $name));
        $row = $query->fetch();

        if ($row) {
            $element = new Element(array(
                'id' => $row['id'],
                'name' => $row['name'],
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
                'name' => $row['name'],
                            ));
        }

        return $elements;
    }
    
    public function save() {
        $query = DB::connection()->prepare('INSERT INTO Element (name) VALUES (:name) RETURNING id');
        $query->execute(array('name' => $this->name));
        $row = $query->fetch();
        $this->id = $row['id'];
    }

    public function delete() {
        $query = DB::connection()->prepare('DELETE FROM Element WHERE id = :id RETURNING id');
        $query->execute(array('id' => $this->id));
        $row = $query->fetch();
        return $row['id'];
    }
}
