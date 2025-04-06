<?php
    class RequeteUtilisateur {
        private string $login;

        public function __construct($login) {
            $this->login = $login;
        }

        //cette fonction retourne le login pour l'authentification
        public function getLogin($linkpdo) {
            $req = 'SELECT login FROM utilisateur WHERE login = :identifiant';
            $check = $linkpdo -> prepare($req);
            $check -> execute(array('identifiant' => $this->login));
            return $check->fetch();
        }

        //cette fonction retourne le mot de passe pour l'authentification
        public function getMotDePasse($linkpdo) {
            $req = 'SELECT mdp FROM utilisateur WHERE login = :identifiant';
            $check = $linkpdo -> prepare($req);
            $check -> execute(array('identifiant' => $this->login));
            return $check->fetch();
        }
        
        // Méthode unique pour récupérer login + mot de passe d'un coup
        public function getUtilisateur($linkpdo) {
            $req = 'SELECT login, mdp FROM utilisateur WHERE login = :identifiant';
            $check = $linkpdo->prepare($req);
            $check->execute(array('identifiant' => $this->login));
            return $check->fetch(PDO::FETCH_ASSOC);
        }
    }
?>