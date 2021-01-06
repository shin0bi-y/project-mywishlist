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
        $idList = $_GET['idList'];
        $itemName = $rq->getParsedBody()['itemName'];
        $description = $rq->getParsedBody()['description'];
        $photoPath = $rq->getParsedBody()['photoPath'];
        $idUser = 'jean@hotmail.com'; //TODO : Recup l'email depuis SESSION

        $item->idList = filter_var($idList, FILTER_SANITIZE_NUMBER_INT);
        $item->itemName = filter_var($itemName, FILTER_SANITIZE_STRING);
        $item->description = filter_var($description, FILTER_SANITIZE_STRING);
        $item->photoPath = null; //filter_var($photoPath, FILTER_SANITIZE_URL); On entre ca plus tard avec modifPhotoPath et un upload
        $item->emailAuthor = filter_var($idUser, FILTER_SANITIZE_EMAIL);

        $item->save();

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
        $itemName = $rq->getParsedBody()['itemName'];
        $description = $rq->getParsedBody()['description'];

        \mywishlist\model\Item::where('idItem', '=', $rq->getParsedBody()['idItem'])
            ->update([
                'itemName' => $itemName,
                'description' => $description
            ]);
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
        $target_dir = __DIR__ . "/uploads/";
        $target_file = $target_dir . basename($_FILES["photo"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

        //vaut 1 si le fichier a upload (image) est conforme
        //vaut 0 dans le cas contraire (exemple : fichier .php et non .jpg)
        $correctUpload = true;

        if(isset($_POST["submit"])) {
            $check = getimagesize($_FILES["photo"]["tmp_name"]); //nom temporaire
            if($check !== false) {
                //Le fichier est une image
                $correctUpload = false;
            }
        }

        //On verifie l'extension
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
            $correctUpload = false;
        }

        //On verifie l'etat du boolean correctUpload
        if ($correctUpload == false) {
            //TODO : rediriger ou afficher une erreur d'upload
        } else {
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                //TODO : Montrer que l'upload est reussi
            } else {
                //TODO : Montrer que l'upload a echoue
            }
        }

        //On met a jour le chemin de l'image dans la BDD
        \mywishlist\model\Item::where('idItem', '=', $rq->getParsedBody()['idItem'])
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
        //TODO: verif que la personne voulant delete est l'auteur de la liste contenant l'item
        $id = filter_var($rq->getParsedBody()['idItem'],FILTER_SANITIZE_NUMBER_INT);
        \mywishlist\model\Item::where('idItem','=',$id)->delete();

        return $rs;
    }
}