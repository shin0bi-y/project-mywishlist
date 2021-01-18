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

    /**
     * Cree un item depuis une requete POST
     * @param Request $rq
     * @param Response $rs
     * @param array $args
     * @return Response
     */
    public function createItem(Request $rq, Response $rs, array $args): Response
    {
        $item = new \mywishlist\model\Item();

        //Les param suivants sont obligatoires dans le form du front donc pas de verification en backend
        $idList = $args['id'];
        $itemName = $rq->getParsedBody()['name'];
        $description = $rq->getParsedBody()['desc'];
        $cout = $rq->getParsedBody()['prix'];
        //$photoPath = $rq->getParsedBody()['photoPath'];
        $emailUser = $_SESSION['user']['email'];

        $item->idList = filter_var($idList, FILTER_SANITIZE_NUMBER_INT);
        $item->itemName = filter_var($itemName, FILTER_SANITIZE_STRING);
        $item->description = filter_var($description, FILTER_SANITIZE_STRING);
        $item->cout = filter_var($cout, FILTER_SANITIZE_NUMBER_FLOAT);
        $item->photoPath = null; //filter_var($photoPath, FILTER_SANITIZE_URL); On entre ca plus tard avec modifPhotoPath et un upload
        $item->emailUser = filter_var($emailUser, FILTER_SANITIZE_EMAIL);

        $item->save();

        $rs = $rs->withRedirect($this->c->router->pathFor('showListe', ['id' => $idList]));
        return $rs;
    }

    /**
     * Affiche un item
     * @param Request $rq
     * @param Response $rs
     * @param array $args
     * @return Response
     */
    public function showItem(Request $rq, Response $rs, array $args): Response
    {
        $id = $args['id'];

        $row = \mywishlist\model\Item::where('idItem', '=', $id)->first();

        $itemName = $row['itemName'];
        $description = $row['description'];

        $rs->getBody()->write("<h1>$itemName</h1> $description </br> flemme de mettre les autres");
        return $rs;
    }

    /**
     * Modifie les informations d'un item
     * @param Request $rq
     * @param Response $rs
     * @param array $args
     * @return Response
     */
    public function modifItem(Request $rq, Response $rs, array $args): Response
    {
        //Ici, on ne modifie que le nom et la description
        $id = $args['id'];
        $itemName = $rq->getParsedBody()['itemName'];
        $description = $rq->getParsedBody()['description'];

        \mywishlist\model\Item::where('idItem', '=', $args['idItem'])
            ->update([
                'itemName' => $itemName,
                'description' => $description
            ]);
        $rs = $rs->withRedirect($this->c->router->pathFor('showListe', ['id' => $id]));
        return $rs;
    }

    /**
     * Modifie l'image d'un item
     * @param Request $rq
     * @param Response $rs
     * @param array $args
     * @return Response
     */
    public function modifImageItem(Request $rq, Response $rs, array $args): Response
    {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["submit"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

        //vaut 1 si le fichier a upload (image) est conforme
        //vaut 0 dans le cas contraire (exemple : fichier .php et non .jpg)
        $correctUpload = true;

        //On verifie l'extension meme si c'est deja fait dans le form
        //On ne fait jamais confiance a l'utilisateur nous
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
            $correctUpload = false;
        }

        //On verifie l'etat du boolean correctUpload
        if ($correctUpload == false) {
            //TODO : rediriger ou afficher une erreur d'upload
        } else {
            if (move_uploaded_file($_FILES["submit"]["tmp_name"], $target_file)) {
                //TODO : Montrer que l'upload a echoue
            } else {
                //TODO : Montrer que l'upload a echoue
            }
        }

        //On met a jour le chemin de l'image dans la BDD
        //TODO : Modifier l'id par $_GET["idItem"]
        \mywishlist\model\Item::where('idItem', '=', "2") //le idItem est dans l'URL
            ->update([
                'photoPath' => $target_file
            ]);
        return $rs;
    }

    /**
     * Supprime l'image d'un item
     * @param Request $rq
     * @param Response $rs
     * @param array $args
     * @return Response
     */
    public function deleteImageItem(Request $rq, Response $rs, array $args): Response
    {
        //On recupere le path de l'image a supprimer
        $target_file = \mywishlist\model\Item::query()->select("photoPath")->where('idItem', '=', $rq->getParsedBody()['idItem'])
            ->get("photoPath");

        //On supprime l'image a supprimer
        unlink($target_file);

        //On met a jour la table
        \mywishlist\model\Item::where('idItem', '=', $rq->getParsedBody()['idItem'])
            ->update([
                'photoPath' => null
            ]);
        return $rs;
    }

    /**
     * Methode de deletion d'un item en fonction de son ID
     * @param Request $rq
     * @param Response $rs
     * @param array $args
     * @return Response
     */
    public function deleteItem(Request $rq, Response $rs, array $args): Response
    {
        $id = filter_var($rq->getParsedBody()['id'],FILTER_SANITIZE_NUMBER_INT);
        $idItem = filter_var($rq->getParsedBody()['id'],FILTER_SANITIZE_NUMBER_INT);
        $liste = \mywishlist\model\Liste::where('idList', '=', $id)->first();
        $emailUser = $_SESSION['user']['email'];

        if ($liste->emailAuthor == $emailUser) \mywishlist\model\Item::where('idItem','=',$idItem)->delete();
        $rs = $rs->withRedirect($this->c->router->pathFor('showListe', ['id' => $id]));
        return $rs;
    }
}