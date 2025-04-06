<?php
header("Content-Type: application/json");

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../middleware/authMiddleware.php';
require_once __DIR__ . '/../Requetes/connexion.php';
require_once __DIR__ . '/../Requetes/RequeteStatistique.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

verifyJWT(); // 🔐 Vérification du token

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["message" => "Méthode non autorisée"]);
    exit();
}

$stat = new RequeteStatistique($linkpdo);

try {
    $response = [
        "nb_matchs_gagnes" => $stat->getNbTotalMatchGagné(),
        "nb_matchs_perdus" => $stat->getNbTotalMatchPerdu(),
        "nb_matchs_nuls" => $stat->getNbTotalMatchNul(),
        "pourcentage_gagnes" => $stat->getPourcentageMatchGagné(),
        "pourcentage_perdus" => $stat->getPourcentageMatchPerdu(),
        "pourcentage_nuls" => $stat->getPourcentageMatchNul()
    ];

    // Statistiques par joueur
    $joueurs = [];
    $result = $stat->getStatJoueur();
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $joueurs[] = $row;
    }
    $response["statistiques_joueurs"] = $joueurs;

    echo json_encode($response);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["message" => "Erreur serveur", "details" => $e->getMessage()]);
}
