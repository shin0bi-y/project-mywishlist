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

    /**
     * Cree une liste depuis le form et l'ajoute à la BDD
     * @param Request $rq
     * @param Response $rs
     * @param array $args
     * @return Response
     */
    public function createListe(Request $rq, Response $rs, array $args): Response
    {
        $date = date('Y-m-d H:i:s');
        $listName = $rq->getParsedBody()['listName'];
        $description = $rq->getParsedBody()['description'];
        $limitDate = $rq->getParsedBody()['limitDate'];
        $public = $rq->getParsedBody()['public'];
        if ($public == null) $public = 0;


        $list = new \mywishlist\model\Liste();
        $list->listName = filter_var($listName, FILTER_SANITIZE_STRING);
        $list->emailAuthor = $_SESSION['user']['email']; //TODO: recup quand les comptes seront faits
        $list->description = filter_var($description, FILTER_SANITIZE_STRING);
        $list->creationDate = $date;
        $list->limitDate = $limitDate;
        $list->isPublic = $public;
        $list->save();

        $name = $rq->getParsedBody()['listName'];
        $rs->getBody()->write("<h1>nom : $name</h1>"); //TODO: faire l'affichage
        return $rs;
    }

    /**
     * Modifie une liste de la BDD
     * @param Request $rq
     * @param Response $rs
     * @param array $args
     * @return Response
     */
    public function modifListe(Request $rq, Response $rs, array $args): Response
    {
        $listName = $rq->getParsedBody()['listName'];
        $description = $rq->getParsedBody()['description'];
        $limitDate = $rq->getParsedBody()['limitDate'];
        $public = $rq->getParsedBody()['public'];
        if ($public == null) $public = 0;

        $proprietaire = \mywishlist\model\Liste::where('listName', '=', $listName)->first()['emailAuthor'];

        if ($_SESSION['user']['email'] == $proprietaire) {
            \mywishlist\model\Liste::where('idList', '=', $rq->getParsedBody()['idList'])
                ->update([
                    'listName' => $listName,
                    'description' => $description,
                    'limitDate' => $limitDate,
                    'isPublic' => $public
                ]);
        } else {
            throw new \Exception('Vous n\'etes pas le proprietaire de la liste');
        }

        return $rs;
    }

    /**
     * Affiche une liste
     * @param Request $rq
     * @param Response $rs
     * @param array $args
     * @return Response
     */
    public function showListe(Request $rq, Response $rs, array $args): Response
    {
        $id = $args['id'];

        $liste = \mywishlist\model\Liste::where('idList', '=', $id)->first();

        if ($liste != null) {
            /*
            $listName = $liste['listName'];
            $idAuthor = $liste['idAuthor'];
            $description = $liste['description'];
            $creationDate = $liste['creationDate'];
            $limitDate = $liste['limitDate'];

            $item = $liste->items()->first();
            $message = $liste->messages()->first();

            $rs->getBody()->write("<h1>$listName</h1> $idAuthor $description $creationDate $limitDate
            </br> <h2>Items</h2> $item->itemName
            </br> <h2>Messages</h2> $message->message");
            */
            $items = $liste->items()->get();

            $this->c->view->render($rs, 'liste.phtml', [
                "liste" => $liste,
                "items" => $items
            ]);
        }
        return $rs;
    }

    /**
     * Ajoute un message a une liste
     * @param Request $rq
     * @param Response $rs
     * @param array $args
     * @return Response
     */
    public function ajouterMessage(Request $rq, Response $rs, array $args): Response
    {
        $idList = $rq->getParsedBody()['idList'];
        $message = $rq->getParsedBody()['message'];
        $email = $_SESSION['user']['email'];
        $date = date('Y-m-d H:i:s');

        $msg = new \mywishlist\model\Message();
        $msg->idList = filter_var($idList, FILTER_SANITIZE_NUMBER_INT);
        $msg->emailAuthor = filter_var($email, FILTER_SANITIZE_NUMBER_INT);
        $msg->message = filter_var($message, FILTER_SANITIZE_STRING);
        $msg->date = $date;

        $msg->save();
        return $rs;
    }

    public function getAdminListe(Request $rq, Response $rs, array $args): Response
    {
        $liste = \mywishlist\model\Liste::where(['idList' => $args['id']])->firstOrFail();
        $this->loadCookiesFromRequest($rq);

        $this->view->render($rs, 'adminliste.phtml', [
            "liste" => $liste,
            "items" => $liste->items()->get(),
            "uri" => $rq->getUri()
            //"flash" => $this->flash->getMessages(),
            //"showRes" => $this->getShowRes()
        ]);
        return $rs;
    }

}