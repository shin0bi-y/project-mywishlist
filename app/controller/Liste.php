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
        $public = 0;
        if(array_key_exists('public', $rq->getParsedBody())) {
            $public = 1;
        }

        $list = new \mywishlist\model\Liste();
        $list->idList = bin2hex(openssl_random_pseudo_bytes(32));
        $list->listName = filter_var($listName, FILTER_SANITIZE_STRING);
        $list->emailAuthor = $_SESSION['user']['email'];
        $list->description = filter_var($description, FILTER_SANITIZE_STRING);
        $list->creationDate = $date;
        $list->limitDate = $limitDate;
        $list->isPublic = $public;
        $list->save();
        $rs = $rs->withRedirect($this->c->router->pathFor("home"));
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
            $items = $liste->items()->get();
            $this->c->view->render($rs, 'liste.phtml', [
                "liste" => $liste,
                "items" => $items
            ]);
        }
        return $rs;
    }

    public function showAllList(Request $rq, Response $rs, array $args): Response
    {
        $publiques = \mywishlist\model\Liste::where('isPublic','=',1)->get();
        $privates = null;
        if(isset($_SESSION['user'])) {
            $privates = \mywishlist\model\Liste::where('emailAuthor','=',$_SESSION['user']['email'])->get();
        }

        $this->c->view->render($rs,'listes.phtml',[
            "publiques" => $publiques,
            "privates" => $privates
        ]);
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