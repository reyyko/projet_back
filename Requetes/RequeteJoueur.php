<?php
require_once __DIR__ . '/../db.php'; // Inclure la connexion à la base de données

// Ajouter un joueur
function ajouterJoueur($nom, $prenom, $numero_licence, $date_naissance, $taille, $poids, $statut) {
    

    $pdo = connectDB();
    $stmt = $pdo->prepare("
        INSERT INTO JOUEUR (Nom, Prenom, Numero_Licence, Date_de_Naissance, Taille, Poids, Statut) 
        VALUES (:Nom, :Prenom, :Numero_Licence, :Date_de_Naissance, :Taille, :Poids, :Statut)
    ");
    $stmt->execute([
        ':Nom' => htmlspecialchars($nom),
        ':Prenom' => htmlspecialchars($prenom),
        ':Numero_Licence' => htmlspecialchars($numero_licence),
        ':Date_de_Naissance' => $date_naissance,
        ':Taille' => htmlspecialchars($taille),
        ':Poids' => htmlspecialchars($poids),
        ':Statut' => htmlspecialchars($statut)
    ]);
}


// Récupérer tous les joueurs
function getTousLesJoueurs() {
    $pdo = connectDB();
    $stmt = $pdo->query("
        SELECT Id_Joueur, Nom, Prenom, Numero_Licence, Date_de_Naissance, Taille, Poids, Statut 
        FROM JOUEUR
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// Supprimer un joueur par ID
function supprimerJoueur($id_joueur) {
    $pdo = connectDB();
    $stmt = $pdo->prepare("DELETE FROM JOUEUR WHERE Id_Joueur = :id");
    $stmt->execute([':id' => $id_joueur]);

    // Réinitialisation des IDs après suppression
    $pdo->exec("SET @autoid = 0");
    $pdo->exec("UPDATE JOUEUR SET Id_Joueur = (@autoid := @autoid + 1)");
    $pdo->exec("ALTER TABLE JOUEUR AUTO_INCREMENT = 1");
}

function supprimerJoueurs($id_joueur) {
    $pdo = connectDB();

    // 1. Supprimer les commentaires liés au joueur
    $pdo->prepare("DELETE FROM commentaire WHERE Id_Joueur = :id")->execute([':id' => $id_joueur]);

    // 2. Supprimer les participations liées (si nécessaire)
    $pdo->prepare("DELETE FROM participer WHERE Id_Joueur = :id")->execute([':id' => $id_joueur]);

    // 3. Supprimer le joueur
    $pdo->prepare("DELETE FROM joueur WHERE Id_Joueur = :id")->execute([':id' => $id_joueur]);

    // 4. Réindexer les ID (optionnel, mais tu le fais déjà)
    $pdo->exec("SET @autoid = 0");
    $pdo->exec("UPDATE joueur SET Id_Joueur = (@autoid := @autoid + 1)");
    $pdo->exec("ALTER TABLE joueur AUTO_INCREMENT = 1");
}



// Récupérer un joueur par ID
function getJoueurById($id_joueur) {
    $pdo = connectDB();
    $stmt = $pdo->prepare("SELECT * FROM JOUEUR WHERE Id_Joueur = :id");
    $stmt->execute([':id' => $id_joueur]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Mettre à jour un joueur
function mettreAJourJoueur($id_joueur, $nom, $prenom, $numero_licence, $date_naissance, $taille, $poids, $statut) {
    $pdo = connectDB();
    $stmt = $pdo->prepare("
        UPDATE JOUEUR 
        SET Nom = :Nom, Prenom = :Prenom, Numero_Licence = :Numero_Licence, 
            Date_de_Naissance = :Date_de_Naissance, Taille = :Taille, Poids = :Poids, Statut = :Statut
        WHERE Id_Joueur = :id
    ");
    $stmt->execute([
        ':Nom' => htmlspecialchars($nom),
        ':Prenom' => htmlspecialchars($prenom),
        ':Numero_Licence' => htmlspecialchars($numero_licence),
        ':Date_de_Naissance' => $date_naissance,
        ':Taille' => htmlspecialchars($taille),
        ':Poids' => htmlspecialchars($poids),
        ':Statut' => htmlspecialchars($statut),
        ':id' => $id_joueur
    ]);
}

function ajouterOuMettreAJourCommentaire($id_joueur, $commentaire) {
    $pdo = connectDB();
    $sql = "
        INSERT INTO commentaire (Id_Joueur, Texte)
        VALUES (:id_joueur, :texte)
        ON DUPLICATE KEY UPDATE Texte = :texte
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id_joueur' => $id_joueur,
        ':texte' => $commentaire,
    ]);
}


function getTousLesJoueursAvecCommentaires() {
    $pdo = connectDB();
    $sql = "
        SELECT joueur.Id_Joueur, joueur.Nom, joueur.Prenom, joueur.Numero_Licence, 
               joueur.Date_de_Naissance, joueur.Taille, joueur.Poids, joueur.Statut, 
               commentaire.Texte AS Commentaire
        FROM joueur
        LEFT JOIN commentaire ON joueur.Id_Joueur = commentaire.Id_Joueur
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


?>
