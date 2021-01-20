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
        $cagnotte->date = time();
        $cagnotte->save();

        $rs = $rs->withRedirect($this->c->router->pathFor("showAllList"));
        return $rs;
    }

    public function showCagnotte(Request $rq, Response $rs, array $args): Response
    {
        $cagnotte = \mywishlist\model\Cagnotte::query()->select("idItem")->where("idItem", "=", $args["idItem"])->pluck("idItem")[0];
        $item = \mywishlist\model\Item::query()->where('idItem', '=', $args["idItem"])->get();

        if ($cagnotte === null){
            $this->c->view->render($rs, 'cagnotte.phtml', [
                "item" => $item,
                "isCagnotte" => false
            ]);
        } else {
            $this->c->view->render($rs, 'cagnotte.phtml', [
                "item" => $item,
                "isCagnotte" => true
            ]);
        }



        return $rs;
    }

}
