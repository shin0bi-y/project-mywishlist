<?php


namespace mywishlist\controller;

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
        /*
        ConnectionFactory::setConfig($this->c['creds']);
        ConnectionFactory::makeConnection();
        $db = ConnectionFactory::$db;
*/
        $date = date('Y-m-d H:i:s');
        $listName = $rq->getParsedBody()['listName'];
        $description = $rq->getParsedBody()['description'];
        $limitDate = $rq->getParsedBody()['limitDate'];

        $list = new \mywishlist\model\Liste();
        $list->listName = $listName;
        $list->idAuthor = -1;
        $list->description = $description;
        $list->creationDate = $date;
        $list->limitDate = $limitDate;
        $list->save();
        /*
        $st = $db->prepare('INSERT INTO list(listName,idAuthor,description,creationDate,limitDate) values (?,?,?,?,?)');
        $st->execute(array($listName, -1, $description, $date, $limitDate));
*/
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