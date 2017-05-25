<?php

  $routes->get('/', function() {
    HelloWorldController::index();
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
  $routes->get('/mypage', function() {
    HelloWorldController::myPage();
  });
  $routes->get('/character', function() {
    HelloWorldController::character();
  });
