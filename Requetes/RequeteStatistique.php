<?php
    class RequeteStatistique {
        private PDO $linkpdo;

        public function __construct($linkpdo) {
            $this->linkpdo = $linkpdo;
        }

        //retourne le nombre total de matchs gagnés
        function getNbTotalMatchGagné() {
            $req = 'SELECT COUNT(*) FROM match_sport WHERE SUBSTRING(Resultat,1,2) > SUBSTRING(Resultat,4,5)';
            $check = $this->linkpdo->prepare($req);
            $check-> execute();
            return $check->fetchColumn();
        }

        //retourne le nombre total de matchs perdus
        function getNbTotalMatchPerdu() {
            $req = 'SELECT COUNT(*) FROM match_sport WHERE SUBSTRING(Resultat,1,2) < SUBSTRING(Resultat,4,5)';
            $check = $this->linkpdo->prepare($req);
            $check-> execute();
            return $check->fetchColumn();
        }

        //retourne le nombre total de matchs nuls
        function getNbTotalMatchNul() {
            $req = 'SELECT COUNT(*) FROM match_sport WHERE SUBSTRING(Resultat,1,2) = SUBSTRING(Resultat,4,5)';
            $check = $this->linkpdo->prepare($req);
            $check-> execute();
            return $check->fetchColumn();
        }

        //retourne le pourcentage de matchs gagnés
        function getPourcentageMatchGagné() {
            $req = 'SELECT round(count(CASE WHEN SUBSTRING(Resultat,1,2) > SUBSTRING(Resultat,4,5) THEN 1 END)/count(Id_Match_sport)*100,2) FROM match_sport';
            $check = $this->linkpdo -> prepare($req);
            $check-> execute();
            return $check->fetchColumn();
        }

        //retourne le pourcentage de matchs perdus
        function getPourcentageMatchPerdu() {
            $req = 'SELECT round(count(CASE WHEN SUBSTRING(Resultat,1,2) < SUBSTRING(Resultat,4,5) THEN 1 END)/count(Id_Match_sport)*100,2) FROM match_sport';
            $check = $this->linkpdo -> prepare($req);
            $check-> execute();
            return $check->fetchColumn();
        }

        //retourne le pourcentage de matchs nuls
        function getPourcentageMatchNul() {
            $req = 'SELECT round(count(CASE WHEN SUBSTRING(Resultat,1,2) = SUBSTRING(Resultat,4,5) THEN 1 END)/count(Id_Match_sport)*100,2) FROM match_sport';
            $check = $this->linkpdo -> prepare($req);
            $check-> execute();
            return $check->fetchColumn();
        }

        /*retourne les statistiques qui concernent les joueurs
        retourne le nom, prénom, le statut ainsi que
        le nombre de fois où il était titulaire, le nombre de fois où il était remplaçant,
        sa note moyenne, et le pourcentage de matchs gagnés auxquels il a participé.
        */
        function getStatJoueur() {
            $req = "SELECT j1.Nom, j2.Prenom, j1.Statut,
            COUNT(CASE WHEN estTitulaire = 'TRUE' THEN 1 END) AS total_selections_titulaire, 
            COUNT(CASE WHEN estTitulaire = 'False' THEN 1 END) AS total_selections_remplacant,
            AVG(Note) AS moyenne_evaluations,
            ROUND(COUNT(CASE WHEN SUBSTRING(Resultat,1,2) > SUBSTRING(Resultat,4,5) THEN 1 END)/COUNT(Match_sport.Id_Match_sport)*100,2) AS pourcentage_matchs_gagnes
            FROM joueur j1, participer, Match_sport, joueur j2
            WHERE j1.Id_Joueur = participer.Id_Joueur
            AND Match_sport.Id_Match_sport = participer.Id_Match_sport
            AND j1.Id_Joueur = j2.Id_Joueur
            GROUP BY j1.Nom, j1.Statut, Poste";
            $check = $this->linkpdo -> prepare($req);
            $check-> execute();
            return $check;
        }
    }
?>