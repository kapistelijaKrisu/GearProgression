<?php

class HelloWorldController extends BaseController{
  // ...
  public static function sandbox(){
    $findEle = Element::findByType('potato');
    $allEle = Element::all();
    Kint::dump($findEle);
    Kint::dump($allEle);
    
    $findCla = Clas::findByName('wallet');
    $allCla = Clas::all();
    Kint::dump($findCla);
    Kint::dump($allCla);
    
    $allPla = Player::findAll();
    $fPla = Player::findById(1);
    Kint::dump($allPla);
    Kint::dump($fPla);
    
    $aItem = Item::findAll();
    $fItem = Item::findById(1);
    Kint::dump($aItem);
    Kint::dump($fItem);
    
    $aship= Ownership::findAvatarOwnerships(1);
    $iship = Ownership::findOwnershipsByItemId(1);
    Kint::dump($aship);
    Kint::dump($iship);
    
    $aa = Avatar::findAll();
    Kint::dump($aa);
    
  }

    public static function index(){
      // make-metodi renderöi app/views-kansiossa sijaitsevia tiedostoja
   	  View::make('home.html');
    }
    
    public static function login(){
      View::make('login.html');
    }
    public static function overview(){
      View::make('overview.html');
    }
    
    public static function myPage(){
      View::make('mypage.html');
    }
    public static function character(){
      View::make('character.html');
    }
  }
