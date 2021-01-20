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

$app->get('/', function (Request $rq, Response $rs, array $args) {
    $this->view->render($rs, 'home.phtml');
})->setName("home");

//--> Liste

$app->get('/liste/create[/]', function (Request $request, Response $response, array $args) {
    $this->view->render($response, 'createliste.phtml');
})->setName('pageListeCreate');

$app->post('/liste/liste_created[/]', \mywishlist\controller\Liste::class . ':createListe')->setName('listeCreate');

$app->post('/liste/modification[/]', \mywishlist\controller\Liste::class . ':modifListe')->setName('listModif');

$app->get('/liste/{id}/admin', \mywishlist\controller\Liste::class . ':getAdminListe')->setName('listeAdmin');

$app->get('/liste/{id}[/]',\mywishlist\controller\Liste::class . ':showListe')
    ->setName("showListe");

$app->get('/listes[/]', \mywishlist\controller\Liste::class . ':showAllList')->setName('showAllList');

//--> Message

$app->post('/liste/message[/]',\mywishlist\controller\Liste::class . ':ajouterMessage')->setName('addMessage');

//--> Item

$app->post('/item/item_created/{id}',\mywishlist\controller\Item::class . ':createItem')->setName("itemCreate");

$app->get('/item[/]', \mywishlist\controller\Item::class . ':showItem')->setName("showItem");

$app->get('/liste/{id}/item/{idItem}/modification/', function (Request $request, Response $response, array $args) {
    $this->view->render($response, 'modifItem.phtml', ['id'=>$args['id'], 'idItem'=>$args['idItem']]);
})->setName("pageModifItem");

$app->post('/liste/{id}/item/{idItem}/modification_done[/]',\mywishlist\controller\Item::class . ':modifItem')->setName("itemModif");

$app->post('/liste/{id}/item/{idItem}/image_done[/]',\mywishlist\controller\Item::class . ':modifImageItem')->setName("itemImageModif");

$app->post('/liste/{id}/item/{idItem}/image/delete[/]',\mywishlist\controller\Item::class . ':deleteImageItem')->setName("deleteImageItem");

$app->get('/liste/{id}/item/{idItem}/delete[/]',\mywishlist\controller\Item::class . ':deleteItem')->setName("deleteItem");

$app->get('/liste/{id}/item/{idItem}/reservation[/]', function (Request $request, Response $response, array $args) {
    $this->view->render($response, 'reservation.phtml', ['id'=> $args['id'], 'idItem'=>$args['idItem']]);
})->setName("pageReservation");

$app->post('/liste/{id}/item/{idItem}/reservation[/]', \mywishlist\controller\Item::class . ':addReservation')->setName("reservation");

//--> Users

$app->get('/register[/]', function (Request $request, Response $response, array $args) {
    $this->view->render($response, 'register.phtml');
})->setName('pageRegister');

$app->post('/register[/]',\mywishlist\controller\User::class . ':register')->setName("register");

$app->get('/login[/]', function (Request $request, Response $response, array $args) {
    $this->view->render($response, 'login.phtml');
})->setName('pageLogin');

$app->post('/login[/]',\mywishlist\controller\User::class . ':login')->setName("login");

$app->get('/profile/modification[/]', function (Request $request, Response $response, array $args) {
    $this->view->render($response, 'modifProfile.phtml');
})->setName('pageModifyProfile');

$app->post('/profile/modification[/]',\mywishlist\controller\User::class . ':modifyProfile')->setName("modifyProfile");

$app->get('/profile/delete[/]', function (Request $request, Response $response, array $args) {
    $this->view->render($response, 'delete.phtml');
})->setName('pageDeleteProfile');

$app->post('/profile/delete[/]', \mywishlist\controller\User::class . ':deleteProfile')->setName("deleteProfile");

$app->get('/profile/logout[/]',\mywishlist\controller\User::class . ':logout')->setName("logout");

//--> Cagnotte

$app->get('/liste/{id}/item/{idItem}/cagnotte/', \mywishlist\controller\Cagnotte::class . ':showCagnotte')
    ->setName("showCagnotte");

$app->post('/liste/{id}/item/{idItem}/cagnotte/created',\mywishlist\controller\Cagnotte::class . ':createCagnotte')
    ->setName("cagnotteCreate");

//--> Run

$app->run();
