<?php
require './../Requetes/RequeteJoueur.php'; // Inclure les fonctions SQL pour les joueurs

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];

    try {
        // Supprimer les relations associées et le joueur
        supprimerJoueur($id);

        // Redirection après suppression
        header("Location: joueur.php?deleted=1");
        exit();
    } catch (PDOException $e) {
        // Afficher un message d'erreur en cas d'échec
        echo "Erreur lors de la suppression : " . $e->getMessage();
    }
} else {
    // Si aucun ID n'est fourni, rediriger vers la page joueur
    header("Location: joueur.php");
    exit();
}
?>
