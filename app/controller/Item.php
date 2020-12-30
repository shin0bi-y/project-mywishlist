<?php

namespace mywishlist\controller;

require_once __DIR__ . '/../../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\Model;
use mywishlist\DBConnection\ConnectionFactory;
use Slim\Http\Request;
use Slim\Http\Response;

class Item
{

    private \Slim\Container $c;

    /**
     * Item controller constructor.
     * @param \Slim\Container $c
     */
    public function __construct(\Slim\Container $c)
    {
        $this->c = $c;
    }

    public function createItem(Request $rq, Response $rs, array $args): Response
    {
        
        $idList = $rq->getParsedBody()['idList'];
        $itemName = $rq->getParsedBody()['itemName'];
        $description = $rq->getParsedBody()['description'];
        $photoPath = $rq->getParsedBody()['photoPath'];
        $idUser = $rq->getParsedBody()['idUser'];

        \mywishlist\model\Item::query()->insert($idList, $itemName, $description, $photoPath, $idUser);

        //$statement = $db->prepare("insert into item (idList, itemName, description, photoPath, idUser) values (?,?,?,?,?)");
        //$statement->execute(array($idList, $itemName, $description, $photoPath, $idUser));

        //show item on the web page
        $iName = $rq->getParsedBody()['itemName'];
        $rs->getBody()->write("<h1>nom : $iName</h1>");
        return $rs;

    }

    public function showItem(Request $rq, Response $rs, array $args) : Response{
        $id = $args['idItem'];

        $rset = \mywishlist\model\Item::query()->select("itemName")->where("idItem", "=", $id);

        $rs->getBody()->write("<h1>Voici l'item : <br>". $rset->get("itemName") ." </h1>"); //TODO a completer plus tard
        return $rs;
    }
}