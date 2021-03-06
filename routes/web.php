<?php


/**
 * @var \Illuminate\Routing\Router $router
 */

$router->get('/', function () {
    return view('welcome');
})->name('home');

$router->get("login", 'Auth\LoginController@showLoginForm')
    ->name("login");

$router->post("login", 'Auth\LoginController@postlogin');

$router->get("login/{token}", 'Auth\LoginController@login')
    ->name('login.token');

$router->get("register", 'Auth\RegisterController@showRegistrationForm')
    ->name('register');

$router->post("register", 'Auth\RegisterController@register');

$router->get('account/activate/{token}', 'Auth\AccountActivationController@activate')
    ->name("activate");

$router->get("logout", 'Auth\LoginController@logout')
    ->name("logout");

$router->get('@{moniker}', 'UserController@show')
    ->name('users.profile');

$router->delete('@{moniker}', 'UserController@destroy')
    ->name('users.delete');

$router->get('@{moniker}/edit', 'UserController@edit')
    ->name('users.edit')
    ->middleware('profile_owner');

$router->put('@{moniker}', 'UserController@update')
    ->name('users.update')
    ->middleware('profile_owner');

$router->get('@{moniker}/{relationship}', 'UserRelationshipController@relation')
    ->where('relationship', '(followers|follows)')
    ->name('users.profile.relationship');

$router->post('@{moniker}/{relationship_action}', 'UserRelationshipController@relationAction')
    ->where('relationship_action', '(follow|unfollow)')
    ->name('users.profile.relationship.action')
    ->middleware('auth');

$router->match(['POST', 'PUT'], '@{moniker}/token', 'ApiTokenController@token')
    ->name('users.api.token.create')
    ->middleware('auth');

$router->get('users', 'UserController@index')
    ->name('users.all');

$router->resource('items', 'ItemController');

$router->resource('category', 'CategoryController', ['only' => ['index', 'show']]);
