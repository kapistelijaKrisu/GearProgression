<?php

$routes->get('/', function() {
    Redirect::to('/home');
});
$routes->get('/home', function() {
    HomeController::home();
});

$routes->get('/login', function() {
    LoginController::login();
});
$routes->get('/logout', function() {
    LoginController::handle_logout();
});
$routes->get('/overview/', function() {
    OverviewController::list_characters();
});
$routes->get('/admin/config', function() {
    AdminConfigController::adminPage();
});
$routes->get('/admin/character', function() {
    AdminAvatarController::adminPage();
});
$routes->get('/admin/player', function() {
    AdminPlayerController::adminPage();
});
$routes->get('/player/:id', function($id) {
    PlayerController::myPage($id);
});

$routes->get('/character/:id', function($id) {
    AvatarController::characterPage($id);
});
$routes->get('/:category/:name', function($category, $name) {
    OverviewController::list_characters_by_category($category, $name);
});
//posts begin here

$routes->post('/login', function() {
    LoginController::handle_login();
});

$routes->post('/overview/addItem/:id', function($character_id) {
    OverviewController::addItem($character_id); //checkbox items for admin
});

$routes->post('/overview/deleteItem/:id', function($character_id) {
    OverviewController::deleteItem($character_id); //checkbox items for admin
});

$routes->post('/character/:id/addItem', function($character) {
    AvatarController::addItem($character);
});
$routes->post('/character/:id/deleteItem', function($character) {
    AvatarController::deleteItem($character);
});




$routes->post('/player/:id/newChar/', function() {
    PlayerController::create_character_to_self();
});

$routes->post('/player/:id/renameChar/:a_id', function($p_id, $a_id) {
    PlayerController::renameOwnedAvatar($p_id, $a_id);
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

//admin add

$routes->post('/admin/newEle', function() {
    AdminConfigController::store_element();
});
$routes->post('/admin/newClas', function() {
    AdminConfigController::store_clas();
});
$routes->post('/admin/newItem', function() {
    AdminConfigController::store_item();
});
$routes->post('/admin/newPlayer', function() {
    AdminPlayerController::store_player();
});
$routes->post('/admin/newChar', function() {
    AdminAvatarController::store_avatar();
});

//admin del
$routes->post('/admin/delEle', function() {
    AdminConfigController::delete_element();
});
$routes->post('/admin/delClas', function() {
    AdminConfigController::delete_clas();
});
$routes->post('/admin/delItem', function() {
    AdminConfigController::delete_item();
});

//admin mod
$routes->post('/admin/modPlayer', function() {
    AdminPlayerController::mod_player();
});

$routes->post('/admin/modAvatar', function() {
    AdminAvatarController::mod_avatar();
});




