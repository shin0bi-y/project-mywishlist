<?php

namespace mywishlist\controller;

require_once __DIR__ . '/../../vendor/autoload.php';

use Slim\Http\Request;
use Slim\Http\Response;

class Item
{

    private \Slim\Container $c;
    private int $countImage;
    private string $target_dir = "uploads/";

    /**
     * Item controller constructor.
     * @param \Slim\Container $c
     */
    public function __construct(\Slim\Container $c)
    {
        $this->c = $c;
        $this->countImage = 0;
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

        $item->idList = filter_var($idList, FILTER_SANITIZE_NUMBER_INT);
        $item->itemName = filter_var($itemName, FILTER_SANITIZE_STRING);
        $item->description = filter_var($description, FILTER_SANITIZE_STRING);
        $item->cout = filter_var($cout, FILTER_SANITIZE_NUMBER_FLOAT);
        $item->photoPath = null; //filter_var($photoPath, FILTER_SANITIZE_URL); On entre ca plus tard avec modifPhotoPath et un upload
        $item->emailUser = null;

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

        $rs->getBody()->write("<h1>$itemName</h1> $description </br>");
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

        if ($itemName !== ""){
            \mywishlist\model\Item::where('idItem', '=', $args['idItem'])
                ->update([
                    'itemName' => $itemName
                ]);
        }

        if ($description !== "") {
            \mywishlist\model\Item::where('idItem', '=', $args['idItem'])
                ->update([
                    'description' => $description
                ]);
        }

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
        $target_file = basename($_FILES["submit"]["name"]);
        $imageFileType = strtolower(pathinfo($this->target_dir . $target_file,PATHINFO_EXTENSION));
        $idItem = $args['idItem'];

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
            $this->c->flash->addMessage('badregister', "L'upload a echoue...");
        } else {
            //On fait attention a ne pas overwrite une image deja presente sous le meme nom
            //pour cela, on ajoute le temps a la fin du nom du fichier.
            $newname = explode(".", $target_file);
            $newname[0] = $newname[0] . time();
            $target_file = implode(".", $newname);

            if(!move_uploaded_file($_FILES["submit"]["tmp_name"], $this->target_dir . $target_file)) {
                $this->c->flash->addMessage('badregister', "L'upload a echoue...");
            }

            //On supprime l'ancienne image
            $old = \mywishlist\model\Item::query()->select("photoPath")->where('idItem', '=', $idItem)
                ->pluck("photoPath");

            //On recupere seulement le path
            $old = str_replace('"', "", $old);
            $old = str_replace('[', "", $old);
            $old = str_replace(']', "", $old);
            $old = urldecode($old);

            unlink($old);

            //On met a jour le chemin de l'image dans la BDD
            \mywishlist\model\Item::where('idItem', '=', $idItem) //le idItem est dans l'URL
            ->update([
                'photoPath' => urlencode($this->target_dir . $target_file)
            ]);
        }

        //On redirect vers la liste
        $id = $args['id'];
        $rs = $rs->withRedirect($this->c->router->pathFor('showListe', ['id' => $id]));
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
        $idItem = $args['idItem'];
        //On recupere le path de l'image a supprimer
        $target_file = \mywishlist\model\Item::query()->select("photoPath")->where('idItem', '=', $idItem)
            ->pluck("photoPath");

        //On recupere seulement le path
        $target_file = str_replace('"', "", $target_file);
        $target_file = str_replace('[', "", $target_file);
        $target_file = str_replace(']', "", $target_file);
        $target_file = urldecode($target_file);

        //On supprime l'image a supprimer
        unlink($target_file);

        //On met a jour la table
        \mywishlist\model\Item::where('idItem', '=', $idItem)
            ->update([
                'photoPath' => null
            ]);

        //On redirige vers la liste
        $id = $args['id'];
        $rs = $rs->withRedirect($this->c->router->pathFor('showListe', ['id' => $id]));
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
        $idItem = filter_var($args['idItem'],FILTER_SANITIZE_NUMBER_INT);
        $item = \mywishlist\model\Item::where('idItem', '=', $idItem)->first();

        //On supprime l'image de l'item a supprimer
        if (!$item->photoPath == null){
            $target_file = $item->photoPath;

            //On recupere seulement le path
            $target_file = str_replace('"', "", $target_file);
            $target_file = str_replace('[', "", $target_file);
            $target_file = str_replace(']', "", $target_file);
            $target_file = urldecode($target_file);

            unlink($target_file);
        }

        $id = filter_var($args['id'],FILTER_SANITIZE_NUMBER_INT);
        $liste = \mywishlist\model\Liste::where('idList', '=', $id)->first();
        $emailUser = $_SESSION['user']['email'];



        if ($liste->emailAuthor === $emailUser || $liste->isPublic == 1){
            $cagnotte = \mywishlist\model\Cagnotte::where('idItem', '=', $idItem)->first();
            $cagnotte->delete();
            $item->delete();
        }


        $rs = $rs->withRedirect($this->c->router->pathFor('showListe', ['id' => $id]));
        return $rs;
    }

    /**
     * Methode de reservation d'un item avec l'email du reserveur
     * @param Request $rq
     * @param Response $rs
     * @param array $args
     * @return Response
     */
    public function addReservation(Request $rq, Response $rs, array $args): Response
    {
        $id = $args['id'];
        $idItem = $args['idItem'];
        $email = $_SESSION['user']['email'];

        \mywishlist\model\Item::where('idItem', '=', $idItem)->update([
            'emailUser' => $email
        ]);

        $this->c->flash->addMessage('setreservation', 'Vous avez réservé cet item !');

        $rs = $rs->withRedirect($this->c->router->pathFor('showListe', ['id'=> $id]));
        return $rs;
    }
}