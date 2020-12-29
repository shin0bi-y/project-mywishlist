<?php
require_once __DIR__ . '/vendor/autoload.php';

use Slim\Http\Request as Request;
use Slim\Http\Response as Response;

$config = require_once __DIR__ . '/conf/config.php';
$c = new \Slim\Container($config);

$app = new \Slim\App($c);

//Routes
$app->get('/', function (Request $rq, Response $rs, array $args) : Response {
    $rs->getBody()->write("<h1> Home </h1>");
    return $rs;
});

$app->get('/hello/{name}[/]', function (Request $rq, Response $rs, array $args) : Response {
    $name = $args['name'];
    $rs->getBody()->write("<h1> hello world, $name </h1>");
    return $rs;
});

$app->run();
