<?php
use App\Core\Router;

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$router = new Router();

$router->map('home', 'GET', '/home', 'home', 'home');
$router->map('articles', 'GET', '/articles/:page/:id', 'articles', 'articles');

$router->run();
