<?php

use Slim\Http\Request as Request;
use Slim\Http\Response as Response;
use mywishlist\DBConnection\ConnectionFactory as ConnectionFactory;
use Illuminate\Database\Capsule\Manager as DB;
use Slim\Views\PhpRenderer;


require_once __DIR__ . '/vendor/autoload.php';

$config = require_once __DIR__ . '/conf/config.php';

$app = new \Slim\App($config);

$db = new DB();
$creds = parse_ini_file($config['creds']);
if ($creds) $db->addConnection($creds);
$db->setAsGlobal();
$db->bootEloquent();


//-------- Routes --------//

$app->get('/', function (Request $rq, Response $rs, array $args) : Response {
    $rs->getBody()->write("<h1> Home </h1>");
    return $rs;
})->setName("home");

//--> Liste

$app->get('/liste/create[/]', function (Request $request, Response $response, array $args) {
    $this->view->render($response, 'createliste.phtml');
})->setName('pageListeCreate');

$app->post('/liste/liste_created[/]', \mywishlist\controller\Liste::class . ':createListe')->setName('listeCreate');

$app->post('/liste/modification[/]', \mywishlist\controller\Liste::class . ':modifListe')->setName('listModif');

$app->get('/liste/{id}[/]',\mywishlist\controller\Liste::class . ':showListe')
    ->setName("showListe");

//--> Message

$app->post('/liste/message[/]',\mywishlist\controller\Liste::class . ':ajouterMessage')->setName('addMessage');

//--> Item

$app->get('/item/{id}[/]', \mywishlist\controller\Item::class . ':showItem')->setName("showItem");

//$app->get('/item/create[/]'); form pour creer un item @nathan

$app->post('/item/item_created[/]',\mywishlist\controller\Item::class . ':createItem')->setName("itemCreate");



//--> Users

//TODO

//--> Run

$app->run();
