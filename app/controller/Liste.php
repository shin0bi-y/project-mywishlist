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

    public function createListe(Request $rq, Response $rs, array $args) : Response {
        ConnectionFactory::setConfig($this->c['creds']);
        ConnectionFactory::makeConnection();
        $db = ConnectionFactory::$db;

        $date = date('Y-m-d H:i:s');
        $listName = $rq->getParsedBody()['listName'];
        $description = $rq->getParsedBody()['description'];
        $limitDate = $rq->getParsedBody()['limitDate'];

        $st = $db->prepare('INSERT INTO list(listName,idAuthor,description,creationDate,limitDate) values (?,?,?,?,?)');
        $st->execute(array($listName,-1,$description,$date,$limitDate));

        $name = $rq->getParsedBody()['listName'];
        $rs->getBody()->write("<h1>nom : $name</h1>");
        return $rs;
    }


}