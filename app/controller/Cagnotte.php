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
        $cagnotte = new \mywishlist\model\Cagnotte();

        $cagnotte->idItem = $args['idItem'];
        $cagnotte->prix = $args['prix'];
        $cagnotte->emailCreator = $args['emailCreator'];
        $cagnotte->save();

        $rs = $rs->withRedirect($this->c->router->pathFor("showAllList"));
        return $rs;
    }

    public function showCagnotte(Request $rq, Response $rs, array $args): Response
    {
        $cagnotte = \mywishlist\model\Cagnotte::query()->select("idItem")->where("idItem", "=", $args["idItem"])->pluck("idItem")[0];
        $prix = \mywishlist\model\Item::query()->select("cout")->where('idItem', '=', $args["idItem"])->pluck("cout")[0];

        if ($cagnotte === null){
            $this->c->view->render($rs, 'cagnotte.phtml', [
                "idItem" => $args["idItem"],
                "id" => $args["id"],
                "prix" => $prix,
                "isCagnotte" => false
            ]);
        } else {
            $this->c->view->render($rs, 'cagnotte.phtml', [
                "idItem" => $args["idItem"],
                "id" => $args["id"],
                "isCagnotte" => true
            ]);
        }



        return $rs;
    }

}
