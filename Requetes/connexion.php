<?php
    //connection à la base de données
    try {
        $linkpdo = new PDO("mysql:host=localhost;dbname=equipe_sports", 'root', '');
    }
    catch (Exception $e) {
        die('Erreur : ' . $e->getMessage());
    }
?>