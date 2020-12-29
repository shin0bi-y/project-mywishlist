<?php


namespace mywishlist\controller;

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
        $name = $rq->getParsedBody()['titre'];
        $rs->getBody()->write("<h1>nom : $name</h1>");
        return $rs;
    }


}