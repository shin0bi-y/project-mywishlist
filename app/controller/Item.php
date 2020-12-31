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

    /**
     * Cree un item depuis une requete POST
     * @param Request $rq
     * @param Response $rs
     * @param array $args
     * @return Response
     */
    public function createItem(Request $rq, Response $rs, array $args): Response
    {
        $item = new \mywishlist\model\Item();

        $idList = $rq->getParsedBody()['idList'];
        $itemName = $rq->getParsedBody()['itemName'];
        $description = $rq->getParsedBody()['description'];
        $photoPath = $rq->getParsedBody()['photoPath'];
        $idUser = $rq->getParsedBody()['idUser'];

        $item->idList = filter_var($idList, FILTER_SANITIZE_NUMBER_INT);
        $item->itemName = filter_var($itemName, FILTER_SANITIZE_STRING);
        $item->description = filter_var($description, FILTER_SANITIZE_STRING);
        $item->photoPath = filter_var($photoPath, FILTER_SANITIZE_URL);
        $item->idUser = filter_var($idUser, FILTER_SANITIZE_NUMBER_INT);

        $item->save();

        //show item on the web page
        $iName = $rq->getParsedBody()['itemName'];
        $rs->getBody()->write("<h1>nom : $iName</h1>");
        return $rs;

    }

    /**
     * Affiche un item
     * @param Request $rq
     * @param Response $rs
     * @param array $args
     * @return Response
     */
    public function showItem(Request $rq, Response $rs, array $args): Response
    {
        $id = $args['id'];

        $row = \mywishlist\model\Item::where('idItem', '=', $id)->first();

        $itemName = $row['itemName'];
        $description = $row['description'];

        $rs->getBody()->write("<h1>$itemName</h1> $description </br> flemme de mettre les autres");
        return $rs;
    }

    /**
     * Modifie les informations d'un item
     * @param Request $rq
     * @param Response $rs
     * @param array $args
     * @return Response
     */
    public function modifItem(Request $rq, Response $rs, array $args): Response
    {

        //Ici, on ne modifie que le nom et la description
        $itemName = $rq->getParsedBody()['itemName'];
        $description = $rq->getParsedBody()['description'];

        \mywishlist\model\Item::where('idItem', '=', $rq->getParsedBody()['idItem'])
            ->update([
                'itemName' => $itemName,
                'description' => $description
            ]);
        return $rs;
    }

    /**
     * Modifie l'image d'un item
     * @param Request $rq
     * @param Response $rs
     * @param array $args
     * @return Response
     */
    public function modifImageItem(Request $rq, Response $rs, array $args): Response
    {

        //Ici, on modifie le path de l'image
        $photoPath = $rq->getParsedBody()['photoPath'];

        \mywishlist\model\Item::where('idItem', '=', $rq->getParsedBody()['idItem'])
            ->update([
                'photoPath' => $photoPath
            ]);
        return $rs;
    }

    /**
     * Supprime l'image d'un item
     * @param Request $rq
     * @param Response $rs
     * @param array $args
     * @return Response
     */
    public function deleteImageItem(Request $rq, Response $rs, array $args): Response
    {

        \mywishlist\model\Item::where('idItem', '=', $rq->getParsedBody()['idItem'])
            ->update([
                'photoPath' => null
            ]);
        return $rs;
    }

    /**
     * Methode de deletion d'un item en fonction de son ID
     * @param Request $rq
     * @param Response $rs
     * @param array $args
     * @return Response
     */
    public function deleteItem(Request $rq, Response $rs, array $args): Response
    {
        //TODO: verif que la personne voulant delete est l'auteur de la liste contenant l'item
        $id = filter_var($rq->getParsedBody()['idItem'],FILTER_SANITIZE_NUMBER_INT);
        \mywishlist\model\Item::where('idItem','=',$id)->delete();

        return $rs;
    }
}