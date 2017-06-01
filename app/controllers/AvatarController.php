<?php

class AvatarController {
    public static function store() {
        $params = $_POST;
        
        $isMain = false;
        if($params['priority'] == 'main') {
            $isMain = true;
        }
        echo Clas::findByName($params['class'])->id;
        
        $avatar = new Avatar(array(
            'name' => $params['character'],
            'p_id' => Player::findByName($params['player'])->id,
            'e_id' => Element::findByType($params['element'])->id,
            'c_id' => Clas::findByName($params['class'])->id,
            'main' => $isMain,
            'stats' => null              
            ));
        
        Kint::dump($params);
        $avatar->save();
    }
    
    /*CREATE TABLE Avatar(
    id SERIAL PRIMARY KEY,
    p_id INTEGER REFERENCES Player(id) NOT NULL,
    e_id INTEGER REFERENCES Element(id) NOT NULL,
    c_id INTEGER REFERENCES clas(id) NOT NULL,
    name character varying(20) NOT NULL UNIQUE,
    main boolean NOT NULL,
    stats cidr 
--NOT NULL UNIQUE  pitää tutkia enemmän lähdettä*/

}
