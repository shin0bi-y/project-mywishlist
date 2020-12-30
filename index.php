<?php

use Slim\Http\Request as Request;
use Slim\Http\Response as Response;
use mywishlist\DBConnection\ConnectionFactory as ConnectionFactory;
use Slim\Views\PhpRenderer;

require_once __DIR__ . '/vendor/autoload.php';

$config = require_once __DIR__ . '/conf/config.php';

$app = new \Slim\App($config);

//-------- Routes --------//

//--> Liste

$app->get('/liste/create[/]', function (Request $request, Response $response, array $args) {
    $this->view->render($response, 'createliste.phtml');
})->setName('pageListeCreate');

$app->post('/liste/liste_created[/]', function (Request $request, Response $response, array $args) use ($container) {
    //$c = new ListeController($container);
    //return $c->createListe($request, $response, $args);
})->setName('listeCreate');

$app->get('/liste/{id}[/]',\mywishlist\controller\Liste::class . ':showListe')
    ->setName("showListe");

$app->get('/', function (Request $rq, Response $rs, array $args) : Response {
    $rs->getBody()->write("<h1> Home </h1>");
    return $rs;
})->setName("home");

$app->get('/hello/{name}[/]', function (Request $rq, Response $rs, array $args) : Response {
    $name = $args['name'];
    $rs->getBody()->write("<h1> hello world, $name </h1>");
    return $rs;
});

//--> Item

$app->get('/liste/{id}/{idItem}[/]', \mywishlist\controller\Item::class . ':showItem')
    ->setName("showItem");



//--> Users

//TODO

//--> Run

$app->run();
