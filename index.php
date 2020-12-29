<?php

use Slim\Http\Request as Request;
use Slim\Http\Response as Response;
use mywishlist\DBConnection\ConnectionFactory as ConnectionFactory;
use Slim\Views\PhpRenderer;


require_once __DIR__ . '/vendor/autoload.php';

$config = require_once __DIR__ . '/conf/config.php';

$app = new \Slim\App($config);
$container = $app->getContainer();

//Setup du container
$container['view'] = function ($container) {
    $vars = [
        "rootUri" => $container->request->getUri()->getBasePath(),
        "router" => $container->router,
        "user" => isset($_SESSION['user']) ? $_SESSION['user'] : null
    ];
    $renderer = new PhpRenderer(__DIR__ . '/app/views/', $vars);
    $renderer->setLayout("layout.phtml");
    return $renderer;
};

//Routes

$app->get('/item/{id}[/]', \mywishlist\controller\Item::class . ':showItem');

$app->get('/liste/create', function (Request $request, Response $response, array $args) {
    $this->view->render($response, 'createliste.phtml');
})->setName('pageListeCreate');

$app->post('/liste/listecreated', function (Request $request, Response $response, array $args) use ($container) {
    //$c = new ListeController($container);
    //return $c->createListe($request, $response, $args);
})->setName('listeCreate');

$app->get('/liste/{id}[/]',\mywishlist\controller\Liste::class . ':showListe');

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