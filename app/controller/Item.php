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
        $item = new \mywishlist\model\Item();

        $idList = $rq->getParsedBody()['idList'];
        $itemName = $rq->getParsedBody()['itemName'];
        $description = $rq->getParsedBody()['description'];
        $photoPath = $rq->getParsedBody()['photoPath'];
        $idUser = $rq->getParsedBody()['idUser'];

        $item->idList = filter_var($idList,FILTER_SANITIZE_NUMBER_INT);
        $item->itemName = filter_var($itemName,FILTER_SANITIZE_STRING);
        $item->description = filter_var($description,FILTER_SANITIZE_STRING);
        $item->photoPath = filter_var($photoPath,FILTER_SANITIZE_URL);
        $item->idUser = filter_var($idUser,FILTER_SANITIZE_NUMBER_INT);

        $item->save();

        //show item on the web page
        $iName = $rq->getParsedBody()['itemName'];
        $rs->getBody()->write("<h1>nom : $iName</h1>");
        return $rs;

    }

    public function showItem(Request $rq, Response $rs, array $args) : Response{
        $id = $args['id'];

        $row = \mywishlist\model\Item::where('idItem','=',$id)->first();

        $itemName = $row['itemName'];
        $description = $row['description'];

        $rs->getBody()->write("<h1>$itemName</h1> $description </br> flemme de mettre les autres");
        return $rs;
    }

    public function modifItem(Request $rq, Response $rs, array $args) : Response {

        $listName = $rq->getParsedBody()['idItem'];
        $description = $rq->getParsedBody()['description'];
        $limitDate = $rq->getParsedBody()['limitDate'];

        \mywishlist\model\Liste::where('idList','=',$rq->getParsedBody()['idList'])
            ->update([
                'listName' => $listName,
                'description' => $description,
                'limitDate' => $limitDate
            ]);
        return $rs;

    }
}