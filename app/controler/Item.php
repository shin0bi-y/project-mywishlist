<?php

namespace mywishlist\controller;

require_once __DIR__ . '/../../vendor/autoload.php';

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

    public function showItem(Request $rq, Response $rs, array $args) : Response{
        $id = $args['id'];
        $rs->getBody()->write("<h1>Voici l\'item $id</h1>");
        return $rs;
    }
}