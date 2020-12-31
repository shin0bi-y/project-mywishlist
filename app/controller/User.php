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
        $user->email = filter_var($email, FILTER_SANITIZE_STRING);
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

        if (password_verify($password, $password_hash[0])) echo "connecte";
        //TODO : rediriger vers le bon endroit

        return $rs;

    }

}
