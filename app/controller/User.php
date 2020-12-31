<?php

namespace mywishlist\controller;

use Slim\Http\Request;
use Slim\Http\Response;

require_once __DIR__ . '/../../vendor/autoload.php';


class User
{

    private \Slim\Container $c;

    /**
     * Constructeur de User
     * @param \Slim\Container $c
     */
    public function __construct(\Slim\Container $c)
    {
        $this->c = $c;
    }

    /**
     * Methode de creation de compte utilisateur
     * @param Request $rq
     * @param Response $rs
     * @param array $args
     * @return Response
     */
    public function register(Request $rq, Response $rs, array $args): Response
    {
        $user = new \mywishlist\model\User();

        $name = $rq->getParsedBody()['name'];
        $email = $rq->getParsedBody()['email'];
        $password = $rq->getParsedBody()['password'];

        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $user->name = filter_var($name, FILTER_SANITIZE_STRING);
        $user->email = filter_var($email, FILTER_SANITIZE_EMAIL);
        $user->password = filter_var($password_hash, FILTER_SANITIZE_STRING);
        $user->save();

        return $rs;

    }

    /**
     * Methode de creation de compte utilisateur
     * @param Request $rq
     * @param Response $rs
     * @param array $args
     * @return Response
     */
    public function login(Request $rq, Response $rs, array $args): Response
    {

        //TODO : modifier la table pour rendre faire de l'email la cle primaire, il peut servir d'ID
        $email = $rq->getParsedBody()['email'];
        $password = $rq->getParsedBody()['password'];

        $password_hash = \mywishlist\model\User::query()->select("password")->where("email", "=", $email)->pluck("password");

        if (password_verify($password, $password_hash[0])) $rs->getBody()->write("<h1>Connecte !</h1>");
        //TODO : rediriger vers le bon endroit

        return $rs;

    }

    /**
     * Methode de creation de compte utilisateur
     * @param Request $rq
     * @param Response $rs
     * @param array $args
     * @return Response
     */
    public function modifyProfile(Request $rq, Response $rs, array $args): Response
    {
        //TODO gerer l'auth (voir cours en ligne sur Arche) pour verifier que le user est connecte

        //L'email est prerempli et non modifiable
        //Il est visible et grise
        //Il sert a identifier le User qui veut modifier son compte
        $email = $rq->getParsedBody()['email'];

        //Le password est un champ obligatoire
        //On le recupere pour verifier que l'action du user
        $password = $rq->getParsedBody()['currentPassword'];

        if (password_verify($password, $password_hash = \mywishlist\model\User::query()->select("password")->where("email", "=", $email)->pluck("password")[0])){
            //On recupere le champ du nom
            //S'il est rempli on update
            if (array_key_exists('name', $rq->getParsedBody()) && $rq->getParsedBody()["name"] !== ''){
                echo "passage name";
                $name = $rq->getParsedBody()['name'];
                $name = filter_var($name, FILTER_SANITIZE_STRING);
                \mywishlist\model\User::where('email', '=', $rq->getParsedBody()['email'])
                    ->update(['name' => $name]);
            }

            //Idem avec le suppose nouveau password
            if (array_key_exists('newPassword', $rq->getParsedBody()) && $rq->getParsedBody()["newPassword"] !== '') {
                echo "passage pass";
                $newPassword = $rq->getParsedBody()['newPassword'];
                $newPassword = filter_var($newPassword, FILTER_SANITIZE_STRING);
                $password_hash = password_hash($newPassword, PASSWORD_DEFAULT);
                \mywishlist\model\User::where('email', '=', $rq->getParsedBody()['email'])
                    ->update(['password' => $password_hash]);
            } else echo "bruh";

        } else {
            //TODO : afficher une erreur
            echo "bad password";
        }

        return $rs;

    }



}
