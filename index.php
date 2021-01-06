<?php

use Slim\Http\Request as Request;
use Slim\Http\Response as Response;
use Illuminate\Database\Capsule\Manager as DB;

require_once __DIR__ . '/vendor/autoload.php';

//-------- Config --------//
session_start();

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

$app->get('/item/create[/]', function (Request $request, Response $response, array $args) {
    $this->view->render($response, 'createItem.phtml');
})->setName('pageItemCreate');

$app->post('/item/item_created[/]',\mywishlist\controller\Item::class . ':createItem')->setName("itemCreate");

$app->get('/item/{id}[/]', \mywishlist\controller\Item::class . ':showItem')->setName("showItem");

$app->post('/item/modification[/]',\mywishlist\controller\Item::class . ':modifItem')->setName("itemModif");

$app->post('/item/image[/]',\mywishlist\controller\Item::class . ':modifImageItem')->setName("itemImageModif");

$app->post('/item/image/delete[/]',\mywishlist\controller\Item::class . ':deleteImageItem')->setName("deleteImageItem");

$app->post('/item/delete[/]',\mywishlist\controller\Item::class . ':deleteItem')->setName("deleteItem");

//--> Users

$app->get('/register[/]', function (Request $request, Response $response, array $args) {
    $this->view->render($response, 'register.phtml');
})->setName('pageRegister');

$app->post('/register[/]',\mywishlist\controller\User::class . ':register')->setName("register");

$app->get('/login[/]', function (Request $request, Response $response, array $args) {
    $this->view->render($response, 'login.phtml');
})->setName('pageLogin');

$app->post('/login[/]',\mywishlist\controller\User::class . ':login')->setName("login");

$app->post('/profile/modification[/]',\mywishlist\controller\User::class . ':modifyProfile')->setName("modifyProfile");

$app->get('/profile/delete[/]', function (Request $request, Response $response, array $args) {
    $this->view->render($response, 'delete.phtml');
})->setName('pageDelete');

$app->post('/profile/delete[/]', \mywishlist\controller\User::class . ':deleteProfile')->setName("deleteProfile");

//--> Run

$app->run();
