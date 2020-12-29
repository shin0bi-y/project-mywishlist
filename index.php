<?php
require_once __DIR__ . '/vendor/autoload.php';

use Slim\Http\Request as Request;
use Slim\Http\Response as Response;
use mywishlist\DBConnection\ConnectionFactory as ConnectionFactory;

$config = require_once __DIR__ . '/conf/config.php';
$c = new \Slim\Container($config);

$app = new \Slim\App($c);

//Routes

$app->get('/item/{id}[/]', \mywishlist\controller\Item::class . ':showItem');

$app->post('/liste/create',\mywishlist\controller\Liste::class . ':createListe');

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

$config["creds"];

/*
ConnectionFactory::setConfig($config["creds"]);
ConnectionFactory::makeConnection();
*/