<?php

$routes->get('/', function() {
    Redirect::to('/overview');
});

$routes->get('/login', function() {
    LoginController::login();
});
$routes->get('/logout', function() {
    LoginController::handle_logout();
});
$routes->get('/overview', function() {
    ItemController::overview();
});
$routes->get('/admin', function() {
    AdminController::adminPage();
});
$routes->get('/player/:id', function($id) {
    PlayerController::myPage($id);
});

$routes->post('/login', function() {
    LoginController::handle_login();
});

$routes->get('/character/:id', function($id) {
    AvatarController::characterPage($id);
});
//posts begin here



$routes->post('/overview/addItem/:id', function($character_id) {
    ItemController::addItem($character_id); //checkbox items for admin
});

$routes->post('/overview/deleteItem/:id', function($character_id) {
    ItemController::deleteItem($character_id); //checkbox items for admin
});

$routes->post('/character/:id/addItem', function($character) {
    AvatarController::addItem($character);
});




$routes->post('/player/:id/newChar/', function() {
    PlayerController::create_character_to_self();
});

$routes->post('/player/:id/renameChar/:a_id', function($p_id, $a_id) {
    PlayerController::renameOwnedChar($p_id, $a_id);
});
$routes->post('/player/:id/ditch', function() {
    PlayerController::deleteSelf();
});

$routes->post('/player/:id/rename', function() {
    PlayerController::renamePlayer();
});

$routes->post('/player/:id/password', function() {
    PlayerController::changePassword();
});



$routes->post('/admin/newEle', function() {
    AdminController::store_element();
});
$routes->post('/admin/newClas', function() {
    AdminController::store_clas();
});
$routes->post('/admin/newItem', function() {
    AdminController::store_item();
});
$routes->post('/admin/newPlayer', function() {
    AdminController::store_player();
});
$routes->post('/admin/newChar', function() {
    AdminController::store_avatar();
});


$routes->post('/admin/delEle', function() {
    AdminController::delete_element();
});
$routes->post('/admin/delClas', function() {
    AdminController::delete_clas();
});
$routes->post('/admin/delItem', function() {
    AdminController::delete_item();
});
$routes->post('/admin/delPlayer', function() {
    AdminController::delete_player();
});
$routes->post('/admin/delChar', function() {
    AdminController::delete_character();
});




