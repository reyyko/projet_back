<?php
header("Content-Type: application/json");

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Requetes/connexion.php';
require_once __DIR__ . '/../Requetes/RequeteUtilisateur.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['login']) || !isset($data['password'])) {
            http_response_code(400);
            echo json_encode(["message" => "Login ou mot de passe manquant"]);
            exit();
        }

        $login = $data['login'];
        $password = $data['password'];

        $requete = new RequeteUtilisateur($login);
        $vrai_login = $requete->getLogin($linkpdo);
        $vrai_mdp = $requete->getMotDePasse($linkpdo);

        if (
            !$vrai_login || !$vrai_mdp ||
            $login !== $vrai_login['login'] ||
            !password_verify($password, $vrai_mdp['mdp'])
        ) {
            http_response_code(401);
            echo json_encode(["message" => "Identifiants invalides"]);
            exit();
        }

        // Authentification OK → génération JWT
        $secret_key = "ta_clé_secrète";
        $payload = [
            "iss" => "localhost",
            "iat" => time(),
            "exp" => time() + 3600,
            "data" => [ "login" => $login ]
        ];

        $jwt = JWT::encode($payload, $secret_key, 'HS256');

        echo json_encode(["token" => $jwt]);
        break;

    case 'GET':
        echo json_encode(["message" => "Bienvenue sur l’API d’authentification"]);
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Méthode non autorisée"]);
        break;
}
