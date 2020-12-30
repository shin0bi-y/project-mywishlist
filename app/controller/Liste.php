<?php


namespace mywishlist\controller;

use Illuminate\Database\Capsule\Manager as DB;
use mywishlist\DBConnection\ConnectionFactory;
use Slim\Http\Request;
use Slim\Http\Response;

require_once __DIR__ . '/../../vendor/autoload.php';


class Liste
{

    private \Slim\Container $c;

    /**
     * Liste controller constructor.
     * @param \Slim\Container $c
     */
    public function __construct(\Slim\Container $c)
    {
        $this->c = $c;
    }

    /**
     * Cree une liste depuis le form et l'ajoute à la BDD
     * @param Request $rq
     * @param Response $rs
     * @param array $args
     * @return Response
     */
    public function createListe(Request $rq, Response $rs, array $args): Response
    {
        $date = date('Y-m-d H:i:s');
        $listName = $rq->getParsedBody()['listName'];
        $description = $rq->getParsedBody()['description'];
        $limitDate = $rq->getParsedBody()['limitDate'];

        $list = new \mywishlist\model\Liste();
        $list->listName = filter_var($listName, FILTER_SANITIZE_STRING);
        $list->idAuthor = 1; //TODO: recup quand les comptes seront faits
        $list->description = filter_var($description, FILTER_SANITIZE_STRING);
        $list->creationDate = $date;
        $list->limitDate = $limitDate;
        $list->save();

        $name = $rq->getParsedBody()['listName'];
        $rs->getBody()->write("<h1>nom : $name</h1>"); //TODO: faire l'affichage
        return $rs;
    }

    /**
     * Modifie une liste de la BDD
     * @param Request $rq
     * @param Response $rs
     * @param array $args
     * @return Response
     */
    public function modifListe(Request $rq, Response $rs, array $args): Response
    {
        //TODO:verif que l'utilisateur est bien le proprietaire de la liste

        $listName = $rq->getParsedBody()['listName'];
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

    public function showListe(Request $rq, Response $rs, array $args): Response
    {
        $id = $args['id'];

        $row = \mywishlist\model\Liste::where('idList','=',$id)->first();

        if($row != null) {
            $listName = $row['listName'];
            $idAuthor = $row['idAuthor'];
            $description = $row['description'];
            $creationDate = $row['creationDate'];
            $limitDate = $row['limitDate'];

            $item = $row->items()->first();
            $message = $row->messages()->first();

            $rs->getBody()->write("<h1>$listName</h1> $idAuthor $description $creationDate $limitDate
            </br> <h2>Items</h2> $item->itemName
            </br> <h2>Messages</h2> $message->message");
        }
        return $rs;
    }

    public function ajouterMessage(Request $rq, Response $rs, array $args): Response
    {
        $idList = $rq->getParsedBody()['idList'];
        $message = $rq->getParsedBody()['message'];
        $idAuthor = -1; //TODO: idAuthor par compte
        $date = date('Y-m-d H:i:s');

        $msg = new \mywishlist\model\Message();
        $msg->idList = filter_var($idList,FILTER_SANITIZE_NUMBER_INT);
        $msg->idAuthor = filter_var($idAuthor,FILTER_SANITIZE_NUMBER_INT);
        $msg->message = filter_var($message,FILTER_SANITIZE_STRING);
        $msg->date = $date;

        $msg->save();
        return $rs;
    }

}