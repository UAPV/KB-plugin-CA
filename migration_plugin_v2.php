<?php
namespace Kanboard\Plugin\Dosi;
use JsonRPC\Client;
require_once("../../config-prod.php");

/*

$mysqli = mysqli_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($mysqli) {
    //!!!!!!!!!!!!!!!!!!!!!!que en dev!!!!!!!!!!!!!!!!!
    /*$query = "UPDATE `users` SET `email`=''";
    $res = mysqli_query($mysqli, $query);
    if (!$res) {
        echo "erreur 0";
        die;
    }*/

    //recurrent => exploitation
/*    $query = "UPDATE `project_has_categories` SET `name`='Exploitation' WHERE name like 'Récurrent'";
    $res = mysqli_query($mysqli, $query);
    if (!$res){
        echo "erreur 1";
        die;
    }
    $query = "UPDATE `valide_projet` SET `last_cat`='Exploitation' WHERE last_cat like 'Récurrent'";
    $res = mysqli_query($mysqli, $query);
    if (!$res){
        echo "erreur 2";
        die;
    }


    // projet
    $query = "UPDATE `project_has_categories` SET `name`='Projet' WHERE name != 'Abandonné' and name != 'Stand-by' and name != 'Réccurent' and name != 'Exploitation'";
    $res = mysqli_query($mysqli, $query);
    if (!$res){
        echo "erreur 3";
        die;
    }

    //categori date => date de fin si il y en n'a pas
    //// + ferme les projet terminer en mettant date d'aujourd'hui
    /// + stand-by
    $querySelect = "SELECT project_id, last_cat FROM valide_projet";
    $resQuerySelect = mysqli_query($mysqli, $querySelect);

    while($value = mysqli_fetch_assoc($resQuerySelect)){

        $endDate = '';
        //DATE
        if($value['last_cat'] != "Terminé") {
            $explode = explode(',', $value['last_cat']);
            if (count($explode) > 0) {
                foreach ($explode as $key => $value2) {
                    if (!ctype_digit($value2)) { //categories non autoriser
                        //verifie que ce ne soit pas une année avec un mois
                        preg_match("/20/", $value2, $preg);
                        if (count($preg) > 0) {
                            foreach (explode(" ", $value2) as $value3) {
                                if (ctype_digit($value3)) {
                                    $endDate = $value3 . "-12-31";
                                }
                            }
                        }
                    } else { // date
                        $endDate = $value2 . "-12-31";
                    }
                }
            }

            if (ctype_digit($value['last_cat'])) {
                if ($endDate == "")
                    $endDate = $value['last_cat'] . "-12-31";
                $query = "UPDATE `projects` SET `end_date`='" . $endDate . "' WHERE id = " . $value['project_id'] . " and end_date != '' ";
                $res = mysqli_query($mysqli, $query);
                if (!$res) {
                    echo "erreur 5";
                    echo mysqli_error($mysqli);
                    die;
                }
            }
        }else{ //Termine
            $now = new \DateTime(date("Y-m-d"));
            $now = $now->sub(\DateInterval::createFromDateString('1 days'));
            $query = "UPDATE `projects` SET `end_date`='" . $now->format('Y-m-d') . "', is_active=0 WHERE id = " . $value['project_id'];
            $res = mysqli_query($mysqli, $query);
            if (!$res) {
                echo "erreur 6";
                echo mysqli_error($mysqli);
                die;
            }
        }

        //Exploitation
        // renouvellement => date de fin
        if($value['last_cat'] == "Exploitation") {
            $querySelect = "SELECT description FROM projects where id=" . $value['project_id'];
            $resQuerySelectDesc = mysqli_query($mysqli, $querySelect);
            $res = mysqli_fetch_array($resQuerySelectDesc);
            $dataLignes = explode("\n", $res[0]);

            $endDateRen = '';
            $keyRenouvellement = null;
            foreach ($dataLignes as $key => $dataligne) {
                $str = htmlentities($dataligne, ENT_NOQUOTES, 'utf-8');
                $str = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
                $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'
                $dataligneSansAccent = preg_replace('#&[^;]+;#', '', $str); // supprime les autres caractères

                //recuperation des info du renouvellement
                $renReg = preg_match("/renouvellement/i", $dataligneSansAccent);
                if ($renReg == 1) {
                    $renExpl = explode(':', $dataligne, 2);
                    if (count($renExpl) < 2) {
                        echo "Erreur 7";
                        echo "Erreur renouvellement dans la description";
                    } else
                        $ren = $renExpl[1];

                    if (!ctype_digit(trim($ren))) { //categories non autoriser
                        //verifie que ce ne soit pas une année avec un mois
                        preg_match("/20/", $ren, $preg);
                        if (count($preg) > 0) {
                            foreach (explode(" ", $ren) as $value3) {
                                if (ctype_digit(trim($value3))) {
                                    $endDateRen = trim($value3). "-12-31";
                                }
                            }
                        }
                    } else { // date
                        $endDateRen = trim($ren ). "-12-31";
                    }
                    $keyRenouvellement = $key;
                    var_dump($value['project_id']);
                    var_dump($renExpl);
                    var_dump($ren);
                    var_dump($endDateRen);
                }
            }
            var_dump("entre if");
            var_dump($endDateRen);
            if($endDateRen != '') {
                //suppression renouvellement dans la description
                unset($dataLignes[$keyRenouvellement]);

                $query = "UPDATE `projects` SET `end_date`='" . $endDateRen . "', `description` = '" . mysqli_escape_string($mysqli, implode("\n", $dataLignes)) . "' WHERE id = " . $value['project_id'];
                var_dump($query);
                $res = mysqli_query($mysqli, $query);
                if (!$res) {
                    echo "erreur 8";
                    echo mysqli_error($mysqli);
                    die;
                }
                // projet
                $query = "UPDATE `valide_projet` SET `last_renouvellement`='" . $endDateRen . "' WHERE project_id=" . $value['project_id'];
                $res = mysqli_query($mysqli, $query);
                if (!$res) {
                    echo "erreur 9";
                    echo mysqli_error($mysqli);
                    die;
                }
            }//si pas renouvellemnt dans la description on vide le champ end_date et start_date
            else{
                $query = "UPDATE `projects` SET `end_date`='', `start_date` = '' WHERE id = " . $value['project_id'];

                $res = mysqli_query($mysqli, $query);
                if (!$res){
                    echo "erreur 10";
                    echo mysqli_error($mysqli);
                    die;
                }
            }
        }
    }



    // projet
    $query = "UPDATE `valide_projet` SET `last_cat`='Projet' WHERE last_cat != 'Abandonné' and last_cat not like '%Stand-by%' and last_cat != 'Réccurent' and last_cat != 'Exploitation'";
    $res = mysqli_query($mysqli, $query);
    if (!$res){
        echo "erreur 11";
        die;
    }
    // projet
    $query = "UPDATE `valide_projet` SET `last_cat`='Stand-by' WHERE last_cat like '%Stand-by%' ";
    $res = mysqli_query($mysqli, $query);
    if (!$res){
        echo "erreur 12";
        die;
    }



}*/

?>


