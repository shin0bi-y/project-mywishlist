<?php

namespace mywishlist\controller;

use Slim\Http\Request;
use Slim\Http\Response;

require_once __DIR__ . '/../../vendor/autoload.php';


class Cagnotte
{

    private \Slim\Container $c;

    /**
     * Constructeur de Cagnotte
     * @param \Slim\Container $c
     */
    public function __construct(\Slim\Container $c)
    {
        $this->c = $c;
    }

    public function createCagnotte(Request $rq, Response $rs, array $args): Response
    {


        $rs = $rs->withRedirect($this->c->router->pathFor("home"));
        return $rs;
    }

}
