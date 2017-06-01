<?php

  $routes->get('/', function() {
      Redirect::to('/overview');
  });

  $routes->get('/hiekkalaatikko', function() {
    HelloWorldController::sandbox();
  });
  $routes->get('/login', function() {
    HelloWorldController::login();
  });
  $routes->get('/overview', function() {
    HelloWorldController::overview();
  });
  $routes->get('/admin', function() {
    HelloWorldController::adminPage();
  });
  $routes->get('/mypage/:id', function($id) {
    HelloWorldController::myPage($id);
  });
  $routes->get('/character/:id', function($id) {
    HelloWorldController::character($id);
  });
  
  $routes->post('/login', function() {
    Redirect::to('/overview', array('message' => 'Nothing interesting happens..yet.'));
  });
