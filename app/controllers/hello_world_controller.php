<?php

  class HelloWorldController extends BaseController{

    public static function index(){
      // make-metodi renderöi app/views-kansiossa sijaitsevia tiedostoja
   	  View::make('home.html');
    }

    public static function sandbox(){
      // Testaa koodiasi täällä
      View::make('helloworld.html');
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
