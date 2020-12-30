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

    public function createListe(Request $rq, Response $rs, array $args): Response
    {
        $date = date('Y-m-d H:i:s');
        $listName = $rq->getParsedBody()['listName'];
        $description = $rq->getParsedBody()['description'];
        $limitDate = $rq->getParsedBody()['limitDate'];

        $list = new \mywishlist\model\Liste();
        $list->listName = filter_var($listName,FILTER_SANITIZE_STRING);
        $list->idAuthor = -1; //TODO: recup quand les comptes seront faits
        $list->description = filter_var($description,FILTER_SANITIZE_STRING);
        $list->creationDate = $date;
        $list->limitDate = $limitDate;
        $list->save();

        $name = $rq->getParsedBody()['listName'];
        $rs->getBody()->write("<h1>nom : $name</h1>");
        return $rs;
    }

    public function showListe(Request $rq, Response $rs, array $args): Response
    {
        $id = $args['id'];

        ConnectionFactory::setConfig($this->c['creds']);
        ConnectionFactory::makeConnection();
        $db = ConnectionFactory::$db;

        $st = $db->prepare('SELECT listName,idAuthor,description,creationDate,limitDate from list where idList = ?');
        $st->execute(array($id));
        $row = $st->fetch();

        $listName = $row['listName'];
        $idAuthor = $row['idAuthor'];
        $description = $row['description'];
        $creationDate = $row['creationDate'];
        $limitDate = $row['limitDate'];

        $rs->getBody()->write("<h1>$listName</h1> $idAuthor $description $creationDate $limitDate");
        return $rs;
    }


}