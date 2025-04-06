<?php
require_once(__DIR__ . '/../db.php'); // Inclure la connexion à la base de données


function getJoueursByMatch($id_match) {
    $pdo = connectDB();
    $stmt = $pdo->prepare("
        SELECT joueur.*, participer.Poste, participer.Note, participer.estTitulaire
        FROM participer
        INNER JOIN joueur ON participer.Id_Joueur = joueur.Id_Joueur
        WHERE participer.Id_Match_sport = :id
    ");
    $stmt->execute([':id' => $id_match]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Récupérer les informations d'un match
function getMatchById($id_match) {
    $pdo = connectDB();
    $stmt = $pdo->prepare("SELECT * FROM match_sport WHERE Id_Match_sport = :id");
    $stmt->execute([':id' => $id_match]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Récupérer les joueurs disponibles pour un match
function getJoueursDisponibles($id_match) {
    $pdo = connectDB();
    $stmt = $pdo->prepare("
        SELECT joueur.*, commentaire.Texte AS Commentaire
        FROM joueur
        LEFT JOIN commentaire ON joueur.Id_Joueur = commentaire.Id_Joueur
        WHERE joueur.Statut = 'Actif'
        AND joueur.Id_Joueur NOT IN (
            SELECT Id_Joueur FROM participer WHERE Id_Match_sport = :id
        )
    ");
    $stmt->execute([':id' => $id_match]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Récupérer les joueurs titulaires pour un match
function getJoueursTitulaires($id_match) {
    $pdo = connectDB();
    $stmt = $pdo->prepare("
        SELECT joueur.*, participer.Poste, participer.Note
        FROM participer
        INNER JOIN joueur ON participer.Id_Joueur = joueur.Id_Joueur
        WHERE participer.Id_Match_sport = :id AND participer.estTitulaire = 1
    ");
    $stmt->execute([':id' => $id_match]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Récupérer les joueurs remplaçants pour un match
function getJoueursRemplacants($id_match) {
    $pdo = connectDB();
    $stmt = $pdo->prepare("
        SELECT joueur.*, participer.Poste, participer.Note
        FROM participer
        INNER JOIN joueur ON participer.Id_Joueur = joueur.Id_Joueur
        WHERE participer.Id_Match_sport = :id AND participer.estTitulaire = 0
    ");
    $stmt->execute([':id' => $id_match]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function ajouterOuMettreAJourJoueur($id_match, $id_joueur, $poste, $estTitulaire) {
    $pdo = connectDB();
    $stmt = $pdo->prepare("
        INSERT INTO participer (Id_Match_sport, Id_Joueur, estTitulaire, Poste)
        VALUES (:id_match, :id_joueur, :estTitulaire, :poste)
        ON DUPLICATE KEY UPDATE Poste = :poste, estTitulaire = :estTitulaire
    ");
    $stmt->execute([
        ':id_match' => $id_match,
        ':id_joueur' => $id_joueur,
        ':poste' => $poste,
        ':estTitulaire' => $estTitulaire
    ]);
}

// Retirer un joueur d'un match
function retirerJoueur($id_match, $id_joueur) {
    $pdo = connectDB();
    $stmt = $pdo->prepare("
        DELETE FROM participer WHERE Id_Match_sport = :id_match AND Id_Joueur = :id_joueur
    ");
    $stmt->execute([
        ':id_match' => $id_match,
        ':id_joueur' => $id_joueur
    ]);
}

// Mettre à jour la note d'un joueur pour un match
function updateNoteForJoueur($id_match, $id_joueur, $note) {
    $pdo = connectDB();
    $stmt = $pdo->prepare("
        UPDATE participer
        SET Note = :note
        WHERE Id_Match_sport = :id_match AND Id_Joueur = :id_joueur
    ");
    $stmt->execute([
        ':note' => $note,
        ':id_match' => $id_match,
        ':id_joueur' => $id_joueur
    ]);
}

function getNombreTitulaires($id_match) {
    $pdo = connectDB();
    $stmt = $pdo->prepare("
        SELECT COUNT(*) AS nb_titulaires
        FROM participer
        WHERE Id_Match_sport = :id_match AND estTitulaire = 1
    ");
    $stmt->execute([':id_match' => $id_match]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['nb_titulaires'];
}

function getNombreTitulairesParPoste($id_match, $poste) {
    $pdo = connectDB();
    $stmt = $pdo->prepare("
        SELECT COUNT(*) AS nb_titulaires
        FROM participer
        WHERE Id_Match_sport = :id_match AND estTitulaire = 1 AND Poste = :poste
    ");
    $stmt->execute([
        ':id_match' => $id_match,
        ':poste' => $poste
    ]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['nb_titulaires'];
}

?>
