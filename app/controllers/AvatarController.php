<?php

class AvatarController {
    public static function store() {
        Kint::dump($_POST);
        $params = $_POST;
        $isMain = false;
        if($params['priority'] == 'main') {
            $isMain = true;
        }
        $player = Player::findByName($params['player']);
        if ($player == null) {
            $errors = array('player not found!');
            Redirect::to('/admin', array('errors' => $errors));
            return false;
        }
        $attributes = array(
            'name' => $params['character'],
            'p_id' => Player::findByName($params['player'])->id,
            'e_id' => Element::findByType($params['element'])->id,
            'c_id' => Clas::findByName($params['class'])->id,
            'main' => $isMain,
            'stats' => null              
            );
        $avatar = new Avatar($attributes);
        $errors = $avatar->errors();
        Kint::dump($errors);
        if (count($errors) == 0) {
           // $avatar->save();

          //  Redirect::to('/admin', array('message' => 'Character added.'));
        } else {
          //  Redirect::to('/admin', array('avatar_errors' => $errors, 'avatar_attributes' => $attributes));
        }
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
