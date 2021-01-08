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

        if (array_key_exists('email', $rq->getParsedBody()) && $rq->getParsedBody()["email"] !== '' &&
            array_key_exists('password', $rq->getParsedBody()) && $rq->getParsedBody()["password"] !== '' &&
            array_key_exists('name', $rq->getParsedBody()) && $rq->getParsedBody()["name"] !== '' &&
            filter_var($rq->getParsedBody()['email'], FILTER_VALIDATE_EMAIL)) // verifie que c'est bien un email
        {
            $name = $rq->getParsedBody()['name'];
            $email = $rq->getParsedBody()['email'];
            $password = $rq->getParsedBody()['password'];

            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $user->name = filter_var($name, FILTER_SANITIZE_STRING);
            $user->email = filter_var($email, FILTER_VALIDATE_EMAIL);
            $user->password = filter_var($password_hash, FILTER_SANITIZE_STRING);
            $user->save();
            $rs = $rs->withRedirect($this->c->router->pathFor("home"));

            //TODO : l'insertion de l'email peut produire une erreur car PrimaryKey
        }

        return $rs;
    }

    /**
     * Methode de connexion d'un utilisateur
     * @param Request $rq
     * @param Response $rs
     * @param array $args
     * @return Response
     */
    public function login(Request $rq, Response $rs, array $args): Response
    {

        if (array_key_exists('email', $rq->getParsedBody()) && $rq->getParsedBody()["email"] !== '' &&
            array_key_exists('password', $rq->getParsedBody()) && $rq->getParsedBody()["password"] !== '') {
            $email = $rq->getParsedBody()['email'];
            $password = $rq->getParsedBody()['password'];
            $password_hash = \mywishlist\model\User::query()->select("password")->where("email", "=", $email)->pluck("password");

            if (password_verify($password, $password_hash[0])) {
                $rs = $rs->withRedirect($this->c->router->pathFor("home"));
                $_SESSION['user'] = array();
                $_SESSION['user']['email'] = $email;
            }
            //TODO : rediriger vers le bon endroit
        } else {
            echo "bruh";
            //TODO : afficher erreur car un champ est mal rempli
        }

        return $rs;
    }

    /**
     * Methode de modification de compte utilisateur
     * @param Request $rq
     * @param Response $rs
     * @param array $args
     * @return Response
     */
    public function modifyProfile(Request $rq, Response $rs, array $args): Response
    {
        //TODO gerer l'auth (voir cours en ligne sur Arche) pour verifier que le user est connecte et preremplir l'email

        //L'email est prerempli et non modifiable
        //Il est visible et grise
        //Il sert a identifier le User qui veut modifier son compte
        $email = $rq->getParsedBody()['email'];

        //Le currentPassword est un champ obligatoire
        //On le recupere pour verifier que l'action du user
        $password = $rq->getParsedBody()['currentPassword'];

        if (password_verify($password, \mywishlist\model\User::query()->select("password")->where("email", "=", $email)->pluck("password")[0])) {

            //On recupere le champ du nom
            //S'il est rempli et non nul on update
            if (array_key_exists('name', $rq->getParsedBody()) && $rq->getParsedBody()["name"] !== '') {
                $name = $rq->getParsedBody()['name'];
                $name = filter_var($name, FILTER_SANITIZE_STRING);
                \mywishlist\model\User::where('email', '=', $rq->getParsedBody()['email'])
                    ->update(['name' => $name]);
            }

            //Idem avec le suppose nouveau password
            if (array_key_exists('newPassword', $rq->getParsedBody()) && $rq->getParsedBody()["newPassword"] !== '') {
                $newPassword = $rq->getParsedBody()['newPassword'];
                $newPassword = filter_var($newPassword, FILTER_SANITIZE_STRING);
                $password_hash = password_hash($newPassword, PASSWORD_DEFAULT);
                \mywishlist\model\User::where('email', '=', $rq->getParsedBody()['email'])
                    ->update(['password' => $password_hash]);
            }

        } else {
            //TODO : afficher une erreur pour mauvais mot de passe courant (currentPassword)
            echo "bad password";
        }

        return $rs;
    }

    /**
     * Methode de suppression du compte
     *
     * @param Request $rq
     * @param Response $rs
     * @param array $args
     * @return Response
     */
    public function deleteProfile(Request $rq, Response $rs, array $args): Response
    {
        //identifie le user qui veut supprimer son compte
        $email = $_SESSION['user']['email'];
        //il devra retaper son mdp pour autoriser la suppression
        $password = $rq->getParsedBody()['password'];

        //on verifie que le compte existe
        if (count(\mywishlist\model\User::query()->select("email")->where("email", "=", $email)->pluck("email")) != 0) {
            $password_hash = \mywishlist\model\User::query()->select("password")->where("email", "=", $email)->pluck("password");

            //on verifie son password
            if ($password_hash[0] != '') {
                if (password_verify($password, $password_hash[0])) {
                    //si il est bon, on supprime le compte
                    \mywishlist\model\User::where('email', '=', $email)->delete();
                    $this->c->flash->addMessage('deletesuccess', 'Votre compte a été supprimé');
                    session_unset();
                    $rs = $rs->withRedirect($this->c->router->pathFor("home"));
                } else {
                    //sinon on previent le user que le mdp n'est pas bon
                    $this->c->flash->addMessage('wrongpassword', 'Mauvais mot de passe');
                    $rs = $rs->withRedirect($this->c->router->pathFor('pageDelete'));
                }
            }
        } else {
            //si il n'existe pas
            $rs->getBody()->write("<h1>Compte inexistant</h1>");
        }

        //TODO : Trigger qui supprimer les listes crees par le user + redirection

        return $rs;
    }

    public function logout(Request $rq, Response $rs, array $args): Response
    {
        session_unset();
        $rs = $rs->withRedirect($this->c->router->pathFor("home"));
        return $rs;
    }

}
