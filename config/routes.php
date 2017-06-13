<?php

$routes->get('/', function() {
    Redirect::to('/overview');
});

$routes->get('/hiekkalaatikko', function() {
    PageController::sandbox();
});
$routes->get('/login', function() {
    PlayerController::login();
});
$routes->post('/logout', function() {
    PlayerController::handle_logout();
});
$routes->get('/overview', function() {
    PageController::overview();
});
$routes->get('/admin', function() {
    $player = BaseController::get_user_logged_in();
    if ($player != null && $player->admin) {
        PageController::adminPage(array());
        
    }else {
    Redirect::to('/overview', array('message' => 'sneaky'));
    }
});
$routes->get('/player/:id', function($id) {
    PageController::myPage($id);
});
$routes->get('/character/:id', function($id) {
    PageController::characterPage($id);
});

$routes->post('/login', function() {
    PlayerController::handle_login();
});
$routes->post('/admin/newEle', function() {
    ElementController::store();
    Redirect::to('/admin', array('message' => 'Element added.'));
});
$routes->post('/admin/newClas', function() {
    ClasController::store();
    Redirect::to('/admin', array('message' => 'Class added.'));
});
$routes->post('/admin/newItem', function() {
    ItemController::store();
    Redirect::to('/admin', array('message' => 'Item added.'));
});
$routes->post('/admin/newPlayer', function() {
    PlayerController::store();
    
});
$routes->post('/admin/newChar', function() {
    AvatarController::store();
    
});
$routes->post('/admin/delChar', function() {
    Redirect::to('/admin', array('message' => 'Character deleted.'));
});


$routes->post('/admin/delEle', function() {
    Redirect::to('/admin', array('message' => 'Element deleted.'));
});
$routes->post('/admin/delClas', function() {
    Redirect::to('/admin', array('message' => 'Class deleted.'));
});
$routes->post('/admin/delItem', function() {
    Redirect::to('/admin', array('message' => 'Item deleted.'));
});
$routes->post('/admin/delPlayer', function() {
    Redirect::to('/admin', array('message' => 'Player deleted.'));
});
$routes->post('/admin/delChar', function() {
    AvatarController::kill();
    Redirect::to('/overview', array('message' => 'Character deleted.'));
});


$routes->post('/player/:id/newChar/', function() {
    Redirect::to('/overview', array('message' => 'nothing happen.'));
});

$routes->post('/player/:id/delChar', function() {
    PlayerController::deleteSelf();
});
$routes->post('/player/:id/ditch', function() {
    PlayerController::delete();
});

$routes->post('/player/:id/rename', function() {
    PlayerController::rename();
});

