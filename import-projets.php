<?php
namespace Kanboard\Plugin\Dosi;
use JsonRPC\Client;
require_once("../../config-test.php");


//recuperer info du csv
$csv = array();
$erreur = array();
$row = 1;
$cptLigne = 0;
if (($handle = fopen("liste_projets-simple.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
        if(trim($data[0]) == "" or trim($data[0]) == "Projet"){
            $erreur[] = "Erreur pas de nom de projet. ligne : ".$cptLigne;
        }else {
            $csv[] = $data;
        }
        $cptLigne++;
    }

    fclose($handle);
}

$mysqli = mysqli_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($mysqli) {
    foreach($csv as $value) {
        //verifie si un projet exist deja avec ce nom
        $querySelect = "SELECT name FROM projects WHERE name='" . mysqli_escape_string($mysqli, $value[0])."'";
        $resQuerySelect = mysqli_query($mysqli, $querySelect);
        $projet = mysqli_fetch_array($resQuerySelect);

        //le projet existe
        if (isset($projet)) {
            $erreur[] = "Un projet avec le nom ".$value[0]." exist deja dans l'application";
        } //le projet existe pas
        else {
            //récupere le nom de la personne
            //chef DOSI
            $queryUser = "SELECT id FROM users WHERE username='" . $value[1] ."'";
            $resQueryUser = mysqli_query($mysqli, $queryUser);
            $chefDosi = mysqli_fetch_array($resQueryUser);
            if ($chefDosi[0] == "") {
                $erreur[] = "le user ".$value[1]." n'a pas été trouvé dans l'application";
            }

            var_dump($queryUser);
            var_dump($erreur);
            var_dump($chefDosi);die;

            //Référrent technique
            $queryUser = "SELECT name FROM users WHERE username='" . $value[2] ."'";
            $resQueryUser = mysqli_query($mysqli, $queryUser);
            $refTech = mysqli_fetch_array($resQueryUser);
            if ($refTech[0] == "") {
                $erreur[] = "le user ".$value[2]." n'a pas été trouvé dans l'application";
            }

            //suppléant technique
            $supExp = explode(',', $value[3]);
            $supTech = "";
            foreach($supExp as $value2) {
                $queryUser = "SELECT name FROM users WHERE username='" . $value2 . "'";
                $resQueryUser = mysqli_query($mysqli, $queryUser);
                $buf = mysqli_fetch_array($resQueryUser);
                if ($buf[0] == "") {
                    $erreur[] = "le user " . $value2 . " n'a pas été trouvé dans l'application";
                }else{
                    $supTech .= $buf[0];
                }
            }

            $queryInsert = "INSERT INTO projects (name, description, identifier, start_date, end_date, owner_id, token, last_modified) VALUES('" . mysqli_escape_string($mysqli, $value[0]) . "', '* Description : ".mysqli_escape_string($mysqli, $value[5])."
* Référent fonctionnel : ".mysqli_escape_string($value[4])."
* Référent technique : ".mysqli_escape_string($refTech[0])."
* Suppléant technique : ".mysqli_escape_string($supTech)."
* Coût : 
* Lien Application : 
* Lien Intracri : 
* Lien FAQ site de la DOSI
* Commentaire : ".mysqli_escape_string($mysqli, $value[6])."' ,NULL,NULL,NULL, ".$chefDosi[0].", '', '".time()."')";
            $resQueryInsert = mysqli_query($mysqli, $queryInsert);
            if (!$resQueryInsert) {
                $erreur[] = "insertion du projet erronée : " . $value[0] ." : ".mysqli_error($mysqli);

            }else {
                $idProjet = mysqli_insert_id ($mysqli);
                //permission
                $queryInsert = "INSERT INTO project_has_users (project_id, user_id, role) VALUES(" . $idProjet.", ".$chefDosi[0].", 'project-manager')";
                $resQueryInsert = mysqli_query($mysqli, $queryInsert);
                if (!$resQueryInsert)
                    $erreur[] = "Ajout du role user du projet " . $value[0] . " echouée. : ".mysqli_error($mysqli);

                //permission visualisatio groupe infra all projet
            }
        }
    }
}else{
    $resPost = "Erreur de connection à la base de donnée";
}

$client = new Client('https://projets-dosi.univ-avignon.fr/jsonrpc.php');
$client->authentication('jsonrpc', 'ceb7959f9cce20163d3cb02f41e7c639a67879c0148e22edd37f283e5af9');

print_r($client->getAllProjects());
/*{
    "jsonrpc": "2.0",
    "method": "createProject",
    "id": 1797076613,
    "params": {
    "name": "PHP client"
    }
}*/

var_dump($erreur);
?>


