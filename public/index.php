<?php

use App\Core\Access;
use App\Core\Router;

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$session = new Access;
$session::startSession();

$router = new Router();

$router->map('home', '/', 'home', 'home');
$router->map('posts', 'posts/:page', 'posts', 'posts', 'Utilisateurs', array('page'));
$router->map('post', 'post/:slug/:id', 'post', 'post', 'Utilisateurs', array('slug', 'id'));
$router->map('login', 'login', 'login', 'login');
$router->map('signup', 'signup', 'signup', 'signup');

$router->map('admin_home', 'admin/home', 'home', 'home', 'Admin');
$router->map('admin_posts', 'admin/posts/:id', 'posts', 'posts', 'Admin', array('id'));

$router->run();
