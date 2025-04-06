<?php
    class RequeteMatch {

        //cette fonction vérifie si un match existe ou pas
        public function existMatch($linkpdo, $dateMatch, $heure_match, $opponent, $placeMeeting) {
            $req = 'SELECT COUNT(*) FROM match_sport WHERE date_Match = :date_Match 
            AND heure_match = :heure_match AND nom_equipe_adverse = :opponent AND lieu_de_rencontre = :placeMeeting';
            $check = $linkpdo -> prepare($req);
            $check -> execute(array('date_Match' => $dateMatch, 'heure_match' => $heure_match, 'opponent' => $opponent, 'placeMeeting' => $placeMeeting));
            return ($check->fetchcolumn()!=0);
        }

        //cette fonction ajoute un match à la base de données
        public function ajouterMatch($linkpdo, $dateMatch, $heure_match, $opponent, $placeMeeting) {
            $req = 'INSERT INTO match_sport(Date_match, Heure_Match, Lieu_de_rencontre, Nom_equipe_adverse)
            VALUES(:date_Match, :heure_match, :placeMeeting, :opponent )';
            $check = $linkpdo -> prepare($req);
            $check -> execute(array('date_Match' => $dateMatch, 'heure_match' => $heure_match, 'opponent' => $opponent, 'placeMeeting' => $placeMeeting));
        }

        //cette fonction cherche un match en fonction de son id
        public function getMatch($idMatch, $linkpdo) {
            $req = 'SELECT *  FROM match_sport WHERE Id_Match_Sport = :idMatch';
            $check = $linkpdo -> prepare($req);
            $check -> bindparam(':idMatch', $idMatch, PDO::PARAM_INT);
            $check -> execute();
            return $check;
        }

        //cette fonction modifie un match
        public function modifierMatch($linkpdo, $idMatch, $dateMatch, $heure_match, $opponent, $placeMeeting, $result) {
            $req = 'UPDATE match_sport SET date_Match = :dateMatch, heure_match = :heure_match, 
            lieu_de_rencontre = :placeMeeting, nom_equipe_adverse = :opponent, resultat = :result 
            WHERE Id_Match_Sport = :idMatch';
            $check = $linkpdo->prepare($req);
            $check->bindParam(':dateMatch', $dateMatch, PDO::PARAM_STR);
            $check->bindParam(':heure_match', $heure_match, PDO::PARAM_STR);
            $check->bindParam(':opponent', $opponent, PDO::PARAM_STR);
            $check->bindParam(':result', $result, PDO::PARAM_STR);
            $check->bindParam(':placeMeeting', $placeMeeting, PDO::PARAM_STR);
            $check->bindParam(':idMatch', $idMatch, PDO::PARAM_INT);
            $check->execute();
        }

        //cette fonction recherche un match en fonction de la donnée présente dans la barre de recherche
        public function rechercherMatch($linkpdo, $param_recherche) {
            $req = 'SELECT * FROM match_sport WHERE date_match = :search OR heure_match = :search
            OR lieu_de_rencontre = :search OR nom_equipe_adverse = :search or resultat = :search';
            $check = $linkpdo->prepare($req);
            $check->execute(array('search' => $param_recherche));
            return $check;
        }

        //cette fonction retourne tous les matchs qui se trouvent dans la base de données
        public function getMatchs($linkpdo) {
            $req = 'SELECT * FROM match_sport';
            $check = $linkpdo->prepare($req);
            $check->execute();
            return $check;
        }

        //cette fonction supprime un match en fonction de son id
        public function supprimerMatch($linkpdo, $idMatch) {
            //suppression de la liaison entre un joueur et un match
            $query = 'DELETE FROM participer WHERE Id_Match_sport = :id';
            $req = $linkpdo->prepare($query);
            $req -> bindparam(':id', $idMatch, PDO::PARAM_INT);
            $req->execute();

            //suppression du match
            $query = 'DELETE FROM match_sport WHERE Id_Match_sport = :id';
            $req = $linkpdo->prepare($query);
            $req -> bindparam(':id', $idMatch, PDO::PARAM_INT);
            $req->execute();
        }
    }
?>