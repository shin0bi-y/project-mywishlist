<?php

namespace mywishlist\controller;

use mywishlist\model\Participant;
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

    /**
     * Methode de creation d'une cagnotte pour un item donne
     * @param Request $rq
     * @param Response $rs
     * @param array $args
     * @return Response
     */
    public function createCagnotte(Request $rq, Response $rs, array $args): Response
    {
        $cagnotte = new \mywishlist\model\Cagnotte();
        $prix = \mywishlist\model\Item::query()->select("cout")->where('idItem', '=', $args["idItem"])->pluck("cout")[0];

        $cagnotte->idItem = $args['idItem'];
        $cagnotte->prix = $prix;
        $cagnotte->emailCreator = $_SESSION['user']['email'];
        $cagnotte->save();

        //On affiche la cagnotte une fois qu'elle a ete cree
        $rs = $rs->withRedirect($this->c->router->pathFor("showAllList"));

        return $rs;
    }

    /**
     * Methode d'affichage d'une cagnotte
     * @param Request $rq
     * @param Response $rs
     * @param array $args
     * @return Response
     */
    public function showCagnotte(Request $rq, Response $rs, array $args): Response
    {
        $cagnotte = \mywishlist\model\Cagnotte::query()->select("idItem")->where("idItem", "=", $args["idItem"])->first();
        $prix = \mywishlist\model\Item::query()->select("cout")->where('idItem', '=', $args["idItem"])->pluck("cout")[0];
        $itemName = \mywishlist\model\Item::query()->select("itemName")->where('idItem', '=', $args["idItem"])->pluck("itemName")[0];
        $photoPath = \mywishlist\model\Item::query()->select("photoPath")->where('idItem', '=', $args["idItem"])->pluck("photoPath")[0];

        //On verifie ici si la cagnotte existe ou pas
        if ($cagnotte === null){
            $this->c->view->render($rs, 'cagnotte.phtml', [
                "idItem" => $args["idItem"],
                "id" => $args["id"],
                "itemName" => $itemName,
                "isCagnotte" => false
            ]);
        } else {
            //On recup les participants
            $c = \mywishlist\model\Cagnotte::query()->where("idItem", "=", $args["idItem"])->first();
            $participants = $c->participants()->get();

            $this->c->view->render($rs, 'cagnotte.phtml', [
                "idItem" => $args["idItem"],
                "id" => $args["id"],
                "itemName" => $itemName,
                "prix" => $prix,
                "participants" => $participants,
                "photoPath" => $photoPath,
                "isCagnotte" => true
            ]);
        }

        return $rs;
    }

    /**
     * Methode de participation a une cagnotte existante
     * @param Request $rq
     * @param Response $rs
     * @param array $args
     * @return Response
     */
    public function participer(Request $rq, Response $rs, array $args): Response
    {
        $participation = new \mywishlist\model\Participant();

        $participation->idItem = $args['idItem'];
        $participation->somme = $rq->getParsedBody()['somme'];
        $participation->emailParticipant = $_SESSION['user']['email'];
        $participation->save();

        //On affiche la cagnotte une fois qu'elle a ete cree
        $rs = $rs->withRedirect($this->c->router->pathFor("showAllList"));

        return $rs;
    }

}
