<?php
require './../Requetes/RequeteJoueur.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_joueur = (int)$_POST['id_joueur'];
    $commentaire = htmlspecialchars($_POST['commentaire']);

    try {
        ajouterOuMettreAJourCommentaire($id_joueur, $commentaire);

        // Redirection après succès
        header("Location: joueur.php?success=1");
        exit();
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>
