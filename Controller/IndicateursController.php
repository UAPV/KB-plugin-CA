<?php
namespace Kanboard\Plugin\Dosi\Controller;
use Kanboard\Controller\BaseController;
use Kanboard\Model\Link;
use Kanboard\Model\Column;
use Kanboard\Model\Task;
use Kanboard\Model\TaskLink;
use Kanboard\Model\Project;
use Kanboard\Model\User;
use JsonRPC\Client;
use JsonRPC\HttpClient;

/**
 * Indicateurs
 *
 * @package controller
 * @author  Jade Tavernier
 */
class IndicateursController extends BaseController
{
    var $mysqli = null;
    var $url_api = null;
    var $key_api = null;
    var $admins = null;

    /**
     * Indicateurs index page
     *
     * @access public
     */
    public function index()
    {
        $cptNbProjetsStandByPerim = 0;
        $cptNbActivitesModif = 0;
        $cptNbActivitesAttente = 0;
        $cptNbActivitesAnomalie = 0;
        $cptNbExploit = 0;
        $cptNbExploitPerim = 0;
        $etats = array("Abandonné", "En cours", "En retard", "Futur", "En anomalie", "Stand-by", "Terminé");
        $cptEtats=array("Abandonné" => 0, "En anomalie" => 0, "Stand-by" => 0, "En cours" => 0, "Terminé" => 0, "Futur" => 0, "En retard" => 0);
        $columnRenvoullement = array();

        //histogramme année -3 jusqua année +3
        $histogrammeAnnee = array();
        $annee = date('Y')-3;
        for ($i=0; $i < 7; $i++){
            $histogrammeAnnee[] = $annee;
            $annee++;
        }
        $arrayData = array(0,0,0,0,0,0,0);
        $arrayDataName = array('','','','','','','');
        $histogramme = array(array("name" => "Renouvellements", "data" => $arrayData), array("name" => "Projets terminés", "data" => $arrayData), array("name" => "Projets abandonnés", "data" => $arrayData), array("name" => "Projets en stand-by", "data" => $arrayData), array("name" => "Projets en cours", "data" => $arrayData), array("name" => "Projets Futurs", "data" => $arrayData));
        $histogrammeName = array(array("name" => "Renouvellements", "data" => $arrayDataName), array("name" => "Projets terminés", "data" => $arrayDataName), array("name" => "Projets abandonnés", "data" => $arrayDataName), array("name" => "Projets en stand-by", "data" => $arrayDataName), array("name" => "Projets en cours", "data" => $arrayDataName), array("name" => "Projets Futurs", "data" => $arrayDataName));

        $liste = array();
        $listeModif = array();
        $listeEnAttente = array();
        $listeStandByPerim = array();
        $listeRenPerim = array();
        $listeAnomalie = array();

        //recherche les uid du personnel DOSI$user = $this->getUser();
        $uids = $this->searchUidsDosi();


        $user = $this->getUser();
        $droitValide = $this->isAdmin($user);
        if(count($uids) == 0){
            $this->response->html($this->helper->layout->pageLayout('dosi:indicateurs/noAccess', array('title' => t('Catalogue d\'activités DOSI'), 'message' => 'ERREUR au niveau du LDAP'), 'dosi:layout'));
        }else {
            if ($this->mysqli = mysqli_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_NAME)) {
                $tabTotal = $this->searchProjets($uids);

                foreach ($tabTotal as $donnees) {
                    $catForm = $this->miseEnFormeCat($donnees['categories']);

                    //on comptabilise seulement les projets valide
                    if($donnees['valide'] != null && $donnees['valide'] == "1") {
                        //seulement les projets
                        if ($this->isProjet($donnees)){
                            //le projet n'est pas encore passé (si le projet a plusieur categorie il passe plusieurs fois)
                            if (!array_key_exists($donnees['idProject'], $liste) && !array_key_exists($donnees['idProject'], $listeModif)) {
                                if ($donnees['last_cat'] == '' || $donnees['last_cat'] == null)
                                    $donnees['last_cat'] = '-';

                                $infoDesc = $this->getInfoDesc($donnees['name'], $donnees['description'], $erreur);

                                //verifie si il y a eu modification du nom et ou categorie de projet
                                $projetModif = $this->projetModif($donnees['name'], $donnees, $erreur);
                                if (!$projetModif) {
                                    $liste[$donnees['idProject']] = array(  "name" =>$donnees['name'],
                                        "priorite" => $donnees['priorite'],
                                        "owner" => $donnees['owner'],
                                        "refTech" => $infoDesc['refTech'],
                                        "supTech" => $infoDesc['supTech'],
                                        "fonctionnel" => $infoDesc['fonctionnel'],
                                        "categories" => $donnees['categories'],
                                        "description" => $infoDesc['description'],
                                        "start_date" => $donnees['start_date'],
                                        "type" => 'Projet',
                                        "end_date" => $donnees['end_date']);
                                } else {
                                    $cptNbActivitesModif++;
                                    $listeModif[$donnees['idProject']] = array("name" =>$donnees['name'],
                                        "priorite" => $donnees['priorite'],
                                        "owner" => $donnees['owner'],
                                        "refTech" => $infoDesc['refTech'],
                                        "supTech" => $infoDesc['supTech'],
                                        "fonctionnel" => $infoDesc['fonctionnel'],
                                        "categories" => $donnees['categories'],
                                        "description" => $infoDesc['description'],
                                        "start_date" => $donnees['start_date'],
                                        "end_date" => $donnees['end_date'],
                                        "last_name" => $donnees['last_name'],
                                        "last_cat" => $donnees['last_cat'],
                                        "last_chef_DOSI" => $donnees['last_chef_DOSI'],
                                        "last_ref_tech" => $donnees['last_ref_tech'],
                                        "last_sup_tech" => $donnees['last_sup_tech'],
                                        "last_fonctionnel" => $donnees['last_fonctionnel'],
                                        "type" => 'Projet',
                                        "last_description" => $donnees['last_description']);

                                }
                            } // le projet est deja passé 1 fois
                            else {
                                //le projet est dans la liste des projets non modifié
                                if (array_key_exists($donnees['idProject'], $liste)) {
                                    $concatCategories = $liste[$donnees['idProject']]['categories'] . ", " . $donnees['categories'];
                                    $bufDonnees = $donnees;
                                    $bufDonnees['categories'] = $concatCategories;
                                    $projetModif = $this->projetModif($donnees['name'], $bufDonnees, $erreur);
                                    $liste[$donnees['idProject']]['categories'] = $concatCategories;
                                    //on verifie quand ajoutant ce categories qu'il soit toujours egale au last_cat sinon on transfert dans la liste modif
                                    if ($projetModif) {
                                        $cptNbActivitesModif++;
                                        $listeModif[$donnees['idProject']] = $liste[$donnees['idProject']];
                                        $listeModif[$donnees['idProject']]["last_name"] = $donnees['last_name'];
                                        $listeModif[$donnees['idProject']]["last_cat"] = $donnees['last_cat'];
                                        unset($liste[$donnees['idProject']]);
                                    }
                                }//le projet est dans la liste des projets modifié
                                else {
                                    $concatCategories = $listeModif[$donnees['idProject']]["categories"] . ", " . $donnees['categories'];
                                    $bufDonnees = $donnees;
                                    $bufDonnees['categories'] = $concatCategories;
                                    $projetModif = $this->projetModif($donnees['name'], $bufDonnees, $erreur);
                                    $listeModif[$donnees['idProject']]["categories"] = $concatCategories;
                                    //on verifie quand ajoutant ce categories qu'il ne soit pas egale au last_cat sinon on transfert dans la liste normal
                                    if (!$projetModif) {
                                        $cptNbActivitesModif--;
                                        unset($listeModif[$donnees['idProject']]["last_name"]);
                                        unset($listeModif[$donnees['idProject']]["last_cat"]);
                                        $liste[$donnees['idProject']] = $listeModif[$donnees['idProject']];
                                        unset($listeModif[$donnees['idProject']]);
                                    }else {
                                        if(!array_key_exists($donnees['idProject'], $listeModif))
                                            $cptNbActivitesModif++;
                                    }
                                }
                            }

                            if (!$projetModif) {
                                //recherche les différents etats (En cours, Futur, Fermé, stand-by, En anomalie, abandonné)
                                $now = new \DateTime(date("Y-m-d"));
                                $startDate = new \DateTime($donnees['start_date']);
                                $endDate = new \DateTime($donnees['end_date']);

                                if (strstr($catForm, "stand")) {
                                    $liste[$donnees['idProject']]['categories'] = "Stand-by";
                                    $cptEtats["Stand-by"]++;
                                    $histogramme = $this->compteurHistogrammeAccueil($histogramme, 3, $startDate, $endDate, $donnees, $histogrammeAnnee, $histogrammeName);

                                    if($donnees['end_date'] != "" and $endDate < $now) {
                                        $cptNbProjetsStandByPerim++;
                                        $listeStandByPerim[]=$donnees['name'];
                                    }
                                } elseif (strstr($catForm, "abandonne")) {
                                    $liste[$donnees['idProject']]['categories'] = "Abandonné";
                                    $cptEtats["Abandonné"]++;
                                    $histogramme = $this->compteurHistogrammeAccueil($histogramme, 2, $startDate, $endDate, $donnees, $histogrammeAnnee, $histogrammeName);

                                } elseif (strstr($catForm, "projet")) {
                                    //anomalie si le projet est ferme mais que la date de fin et dans le futur
                                    if (!$donnees['is_active'] and $donnees['end_date'] != "" and $endDate < $now) {
                                        $liste[$donnees['idProject']]['categories'] = "En anomalie";
                                        $cptEtats["En anomalie"]++;
                                    }else if ($donnees['start_date'] != "" and $startDate > $now) {
                                        $liste[$donnees['idProject']]['categories'] = "Futur";
                                        $cptEtats["Futur"]++;
                                        $histogramme = $this->compteurHistogrammeAccueil($histogramme, 5, $startDate, $endDate, $donnees, $histogrammeAnnee, $histogrammeName);

                                    }else if ($donnees['end_date'] != "" and $endDate > $now) {
                                        $liste[$donnees['idProject']]['categories'] = "En cours";
                                        $cptEtats["En cours"]++;
                                        $histogramme = $this->compteurHistogrammeAccueil($histogramme, 4, $startDate, $endDate, $donnees, $histogrammeAnnee, $histogrammeName);

                                    }else if ($donnees['end_date'] != "" and $endDate < $now) {
                                        if ($donnees['is_active']) {
                                            $liste[$donnees['idProject']]['categories'] = "En retard";
                                            $cptEtats['En retard']++;
                                            $histogramme = $this->compteurHistogrammeAccueil($histogramme, 4, $startDate, $endDate, $donnees, $histogrammeAnnee, $histogrammeName);
                                        }else {
                                            $liste[$donnees['idProject']]['categories'] = "Terminé";
                                            $cptEtats["Terminé"]++;
                                            $histogramme = $this->compteurHistogrammeAccueil($histogramme, 1, $startDate, $endDate, $donnees, $histogrammeAnnee, $histogrammeName);
                                        }
                                    } else if ($donnees['start_date'] != "" and $startDate < $now) {
                                        $liste[$donnees['idProject']]['categories'] = "En cours";
                                        $cptEtats["En cours"]++;
                                        $histogramme = $this->compteurHistogrammeAccueil($histogramme, 4, $startDate, $endDate, $donnees, $histogrammeAnnee, $histogrammeName);
                                    }else {
                                        $liste[$donnees['idProject']]['categories'] = "En anomalie";
                                        $cptEtats["En anomalie"]++;
                                        $cptNbActivitesAnomalie++;
                                        $listeAnomalie[]=$donnees['name'];
                                    }
                                } else {
                                    $liste[$donnees['idProject']]['categories'] = "En anomalie";
                                    $cptEtats["En anomalie"]++;
                                    $cptNbActivitesAnomalie++;
                                    $listeAnomalie[]=$donnees['name'];
                                }
                            }
                        }//exploit
                        else{
                            if (!array_key_exists($donnees['idProject'], $liste) && !array_key_exists($donnees['idProject'], $listeModif)) {
                                if ($donnees['last_cat'] == '' || $donnees['last_cat'] == null)
                                    $donnees['last_cat'] = '-';

                                $infoDesc = $this->getInfoDesc($donnees['name'], $donnees['description'], $erreur);

                                //verifie si il y a eu modification du nom et ou categorie de projet
                                $projetModif = $this->projetModif($donnees['name'], $donnees, $erreur);
                                if (!$projetModif) {
                                    $now = new \DateTime(date("Y-m-d"));
                                    $endDate = new \DateTime($donnees['end_date']);
                                    $startDate = new \DateTime($donnees['end_date']);
                                    if ($donnees['end_date'] != "" and $endDate < $now) {
                                        $cptNbExploitPerim++;
                                        $listeRenPerim[]=$donnees['name'];
                                    }else{
                                        $cptNbExploit++;
                                    }

                                    if($donnees['end_date'] != "") {
                                        if(!isset($columnRenvoullement[$endDate->getTimestamp() * 1000]))
                                            $columnRenvoullement[$endDate->getTimestamp() * 1000] = array("name" => $donnees['name'], "x" => $endDate->getTimestamp() * 1000, "y" => 1);
                                        else{
                                            $columnRenvoullement[$endDate->getTimestamp() * 1000] = array("name" => $columnRenvoullement[$endDate->getTimestamp() * 1000]['name'].'<br> '.$donnees['name'], "x" => $endDate->getTimestamp() * 1000, "y" => $columnRenvoullement[$endDate->getTimestamp() * 1000]['y']+1);
                                        }
                                        $histogramme = $this->compteurHistogrammeAccueil($histogramme, 0, $startDate, $endDate, $donnees, $histogrammeAnnee, $histogrammeName);
                                    }
                                    $liste[$donnees['idProject']] = array(  "name" =>$donnees['name'],
                                        "priorite" => $donnees['priorite'],
                                        "owner" => $donnees['owner'],
                                        "refTech" => $infoDesc['refTech'],
                                        "supTech" => $infoDesc['supTech'],
                                        "fonctionnel" => $infoDesc['fonctionnel'],
                                        "categories" => $donnees['categories'],
                                        "description" => $infoDesc['description'],
                                        "type" => "Exploitation",
                                        "renouvellement" => $donnees['end_date']);
                                } else {
                                    $cptNbActivitesModif++;
                                    $listeModif[$donnees['idProject']] = array("name" =>$donnees['name'],
                                        "priorite" => $donnees['priorite'],
                                        "owner" => $donnees['owner'],
                                        "refTech" => $infoDesc['refTech'],
                                        "supTech" => $infoDesc['supTech'],
                                        "fonctionnel" => $infoDesc['fonctionnel'],
                                        "categories" => $donnees['categories'],
                                        "description" => $infoDesc['description'],
                                        "last_name" => $donnees['last_name'],
                                        "last_cat" => $donnees['last_cat'],
                                        "last_chef_DOSI" => $donnees['last_chef_DOSI'],
                                        "last_ref_tech" => $donnees['last_ref_tech'],
                                        "last_sup_tech" => $donnees['last_sup_tech'],
                                        "last_fonctionnel" => $donnees['last_fonctionnel'],
                                        "last_description" => $donnees['last_description'],
                                        "last_renouvellement" => $donnees['last_renouvellement'],
                                        "type" => "Exploitation",
                                        "renouvellement" => $donnees['end_date']);

                                }
                            } else {
                                if (array_key_exists($donnees['idProject'], $liste)) {
                                    $concatCategories = $liste[$donnees['idProject']]['categories'] . ", " . $donnees['categories'];
                                    $bufDonnees = $donnees;
                                    $bufDonnees['categories'] = $concatCategories;
                                    $projetModif = $this->projetModif($donnees['name'], $bufDonnees, $erreur);
                                    $liste[$donnees['idProject']]['categories'] = $concatCategories;
                                    //on verifie quand ajoutant ce categories qu'il soit toujours egale au last_cat sinon on transfert dans la liste modif
                                    if ($projetModif) {
                                        $cptNbActivitesModif++;
                                        $listeModif[$donnees['idProject']] = $liste[$donnees['idProject']];
                                        $listeModif[$donnees['idProject']]["last_name"] = $donnees['last_name'];
                                        $listeModif[$donnees['idProject']]["last_cat"] = $donnees['last_cat'];
                                        unset($liste[$donnees['idProject']]);
                                    }
                                } else {
                                    $concatCategories = $listeModif[$donnees['idProject']]["categories"] . ", " . $donnees['categories'];
                                    $bufDonnees = $donnees;
                                    $bufDonnees['categories'] = $concatCategories;
                                    $projetModif = $this->projetModif($donnees['name'], $bufDonnees, $erreur);
                                    $listeModif[$donnees['idProject']]["categories"] = $concatCategories;
                                    //on verifie quand ajoutant ce categories qu'il ne soit pas egale au last_cat sinon on transfert dans la liste normal
                                    if (!$projetModif) {
                                        $cptNbActivitesModif--;
                                        $now = new \DateTime(date("Y-m-d"));
                                        $endDate = new \DateTime($donnees['end_date']);
                                        $startDate = new \DateTime($donnees['end_date']);

                                        if ($donnees['end_date'] != "" and $endDate < $now) {
                                            $cptNbExploitPerim++;
                                            $listeRenPerim[]=$donnees['name'];
                                        }else{
                                            $cptNbExploit++;
                                        }
                                        if($donnees['end_date'] != ""){
                                            if(!isset($columnRenvoullement[$endDate->getTimestamp() * 1000]))
                                                $columnRenvoullement[$endDate->getTimestamp() * 1000] = array("name" => $donnees['name'], "x" => $endDate->getTimestamp() * 1000, "y" => 1);
                                            else{
                                                $columnRenvoullement[$endDate->getTimestamp() * 1000] = array("name" => $columnRenvoullement[$endDate->getTimestamp() * 1000]['name'].'<br> '.$donnees['name'], "x" => $endDate->getTimestamp() * 1000, "y" => $columnRenvoullement[$endDate->getTimestamp() * 1000]['y']+1);
                                            }
                                            $histogramme = $this->compteurHistogrammeAccueil($histogramme, 0, $startDate, $endDate, $donnees, $histogrammeAnnee);
                                        }
                                        unset($listeModif[$donnees['idProject']]["last_name"]);
                                        unset($listeModif[$donnees['idProject']]["last_cat"]);
                                        $liste[$donnees['idProject']] = $listeModif[$donnees['idProject']];
                                        unset($listeModif[$donnees['idProject']]);
                                    }else {
                                        if(!array_key_exists($donnees['idProject'], $listeModif))
                                            $cptNbActivitesModif++;
                                    }
                                }
                            }
                            if (!$projetModif) {
                                //recherche les différents categories
                                if (strstr($catForm, "stand")) {
                                    $liste[$donnees['idProject']]['categories'] = "Stand-by";
                                    $cptEtats["Stand-by"]++;
                                } elseif (strstr($catForm, "abandonne")) {
                                    $liste[$donnees['idProject']]['categories'] = "Abandonné";
                                    $cptEtats["Abandonné"]++;
                                } else {
                                    $now = new \DateTime(date("Y-m-d"));
                                    $startDate = new \DateTime($donnees['start_date']);
                                    $endDate = new \DateTime($donnees['end_date']);

                                    if (!$donnees['is_active'] ) {
                                        $liste[$donnees['idProject']]['categories'] = "Terminé";
                                        $cptEtats["Terminé"]++;
                                    }else if ($donnees['end_date'] != "" and $endDate > $now) {
                                        $liste[$donnees['idProject']]['categories'] = "En cours";
                                        $cptEtats["En cours"]++;
                                    }else if ($donnees['end_date'] != "" and $endDate < $now) {
                                        $liste[$donnees['idProject']]['categories'] = "En retard";
                                        $cptEtats['En retard']++;
                                    } else {
                                        $liste[$donnees['idProject']]['categories'] = "En anomalie";
                                        $cptEtats["En anomalie"]++;
                                    }
                                }
                            }

                        }
                    }else {
                        $cptNbActivitesAttente++;
                        $infoDesc = $this->getInfoDesc($donnees['name'], $donnees['description'], $erreur);
                        $listeEnAttente[$donnees['idProject']] = array(  "name" =>$donnees['name'],
                            "priorite" => $donnees['priorite'],
                            "owner" => $donnees['owner'],
                            "refTech" => $infoDesc['refTech'],
                            "supTech" => $infoDesc['supTech'],
                            "fonctionnel" => $infoDesc['fonctionnel'],
                            "categories" => $donnees['categories'],
                            "description" => $infoDesc['description'],
                            "start_date" => $donnees['start_date']);
                        if($this->isProjet($donnees)){
                            $listeEnAttente[$donnees['idProject']]["end_date"] = $donnees['end_date'];
                        }else{
                            $listeEnAttente[$donnees['idProject']]["renouvellement"] = $donnees['end_date'];
                        }
                    }
                }

            } else // Mais si elle rate…
            {
                $this->response->html($this->helper->layout->pageLayout('dosi:indicateurs/noAccess', array('title' => t('Catalogue d\'activités DOSI'), 'message' => 'Erreur de connection à la base de donnée'), 'dosi:layout'));
            }

            mysqli_close($this->mysqli);
        }

        $this->sendAllNotificationModifValid($listeModif);
        $this->sendAllNotificationEnAttente($listeEnAttente);

        $this->response->html($this->helper->layout->pageLayout('dosi:indicateurs/index', array(
            'cptNbProjetsStandByPerim' => $cptNbProjetsStandByPerim,
            'cptNbProjetsEnRetard' => $cptEtats['En retard'],
            'cptNbActivitesModif' => $cptNbActivitesModif,
            'cptNbActivitesAttente' => $cptNbActivitesAttente,
            'cptNbActivitesAnomalie' => $cptNbActivitesAnomalie,
            'cptNbExploit' => $cptNbExploit,
            'cptNbExploitPerim' => $cptNbExploitPerim,
            'cptNbProjetEnCours' => $cptEtats["En cours"]+$cptEtats['En retard'],
            'cptEtats' => $cptEtats,
            'liste' => $liste,
            'listeModif' => $listeModif,
            'listeAnomalie' => $listeAnomalie,
            'listeRenPerim' => $listeRenPerim,
            'listeStandByPerim' => $listeStandByPerim,
            'histogrammeAnnee' => $histogrammeAnnee,
            'histogramme' => $histogramme,
            'histogrammeName' => $histogrammeName,
            'droitValide' => $droitValide,
            'etats' => $etats,
            'title' => t('Catalogue d\'activités DOSI')), 'dosi:layout'));
    }

    /**
     * Indicateurs projet page
     *
     * @access public
     */
    public function projets()
    {
        $erreur = array();
        $cptNbProjets = 0;
        $liste = array();
        $listeModif = array();
        $resPost = "";
        $etats = array("Abandonné", "En cours", "En retard", "Futur", "En anomalie", "Stand-by", "Terminé");
        $cptEtats=array("Abandonné" => 0, "En anomalie" => 0, "Stand-by" => 0, "En cours" => 0, "Terminé" => 0, "Futur" => 0, "En retard" => 0);

        $user = $this->getUser();
        $droitValide = $this->isAdmin($user);

        if(isset($_POST['idProjet'])){
            //ajoute l'ancre pour retourné a l'endroit de l'action
            $ancre = $_POST['ancre'];
            if ($this->mysqli = mysqli_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_NAME)) {
                $value = json_decode($_POST['value'],true);
                //verifie si le projet exist dans la table valide_projet
                $projetValide = $this->isExistTableValide($_POST['idProjet']);
                //le projet n'est pas dans la table valide projet ce qui ne doit pas se produire normalement :)
                if(isset($projetValide)){
                    //met a jour la table
                    $queryUpdate = "UPDATE valide_projet set valide=".$_POST['valide'].", modifie=".$_POST['modifie'].", priorite='".$_POST['priorite']."', last_name ='".mysqli_escape_string($this->mysqli,$value['name'])."', last_cat='".mysqli_escape_string($this->mysqli,$value['categories'])."'
                     , last_chef_DOSI='".mysqli_escape_string($this->mysqli,$value['owner'])."', last_ref_tech='".mysqli_escape_string($this->mysqli,$value['refTech'])."', last_sup_tech='".mysqli_escape_string($this->mysqli,$value['supTech'])."', last_fonctionnel='".mysqli_escape_string($this->mysqli,$value['fonctionnel'])."', last_description='".mysqli_escape_string($this->mysqli,$value['description'])."', last_renouvellement='".mysqli_escape_string($this->mysqli,$value['renouvellement'])."'
                     WHERE project_id=".$_POST['idProjet'];
                    $resQueryUpdate = mysqli_query($this->mysqli, $queryUpdate);
                    if(!$resQueryUpdate)
                        $resPost = "la mise à jour de l'activité à echouée.";

                    if($resPost == ''){
                        //Click bouton "modifier"
                        if($_POST['modifie'] != $projetValide['modifie']){
                            $resPost = "L'activité ".$value['name']." à été modifié.";
                            $this->sendNotificationAdmin(array('id' => $_POST['idProjet'], 'name' => $value['name']), 'modif');
                        }else if ($_POST['valide'] != $projetValide['valide']) {//click bouton refuser
                            $resPost = "L'activité ".$value['name']." à été refusé.";
                            $this->sendNotificationAdmin(array('id' => $_POST['idProjet'], 'name' => $value['name']), 'refus');
                        }else { //changement priorité
                            $resPost = "L'activité ".$value['name']." à changé de priorité. (".$projetValide['priorite']." => ".$_POST['priorité'].")";
                            $this->sendNotificationAdmin(array('id' => $_POST['idProjet'], 'name' => $value['name']), $_POST['priorité']);
                        }
                    }

                }else
                    $resPost = "Erreur le projet n'a pas été trouvé dans la table valide projet";
            }else{
                $resPost = "Erreur de connection à la base de donnée";
            }
        }

        //recherche les uid du personnel DOSI
        $uids = $this->searchUidsDosi();

        if(count($uids) == 0){
            $this->response->html($this->helper->layout->pageLayout('dosi:indicateurs/noAccess', array('title' => t('Catalogue d\'activités DOSI'), 'message' => 'ERREUR au niveau du LDAP'), 'dosi:layout'));
        }else {
            if ($this->mysqli = mysqli_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_NAME)) {
                $tabTotal = $this->searchProjets($uids);

                foreach ($tabTotal as $donnees) {
                    $catForm = $this->miseEnFormeCat($donnees['categories']);

                    //on comptabilise seulement les projets valide
                    if($donnees['valide'] != null && $donnees['valide'] == "1") {
                        //seulement les projets
                        if ($this->isProjet($donnees)){
                            if (!array_key_exists($donnees['idProject'], $liste) && !array_key_exists($donnees['idProject'], $listeModif)) {
                                if ($donnees['last_cat'] == '' || $donnees['last_cat'] == null)
                                    $donnees['last_cat'] = '-';

                                $infoDesc = $this->getInfoDesc($donnees['name'], $donnees['description'], $erreur);

                                //verifie si il y a eu modification du nom et ou categorie de projet
                                $projetModif = $this->projetModif($donnees['name'], $donnees, $erreur);
                                if (!$projetModif) {
                                    $cptNbProjets++;
                                    $liste[$donnees['idProject']] = array(  "name" =>$donnees['name'],
                                        "priorite" => $donnees['priorite'],
                                        "owner" => $donnees['owner'],
                                        "refTech" => $infoDesc['refTech'],
                                        "supTech" => $infoDesc['supTech'],
                                        "fonctionnel" => $infoDesc['fonctionnel'],
                                        "categories" => $donnees['categories'],
                                        "description" => $infoDesc['description'],
                                        "start_date" => $donnees['start_date'],
                                        "end_date" => $donnees['end_date']);
                                } else {
                                    $listeModif[$donnees['idProject']] = array("name" =>$donnees['name'],
                                        "priorite" => $donnees['priorite'],
                                        "owner" => $donnees['owner'],
                                        "refTech" => $infoDesc['refTech'],
                                        "supTech" => $infoDesc['supTech'],
                                        "fonctionnel" => $infoDesc['fonctionnel'],
                                        "categories" => $donnees['categories'],
                                        "description" => $infoDesc['description'],
                                        "start_date" => $donnees['start_date'],
                                        "end_date" => $donnees['end_date'],
                                        "last_name" => $donnees['last_name'],
                                        "last_cat" => $donnees['last_cat'],
                                        "last_chef_DOSI" => $donnees['last_chef_DOSI'],
                                        "last_ref_tech" => $donnees['last_ref_tech'],
                                        "last_sup_tech" => $donnees['last_sup_tech'],
                                        "last_fonctionnel" => $donnees['last_fonctionnel'],
                                        "last_description" => $donnees['last_description']);

                                }
                            } else {
                                if (array_key_exists($donnees['idProject'], $liste)) {
                                    $concatCategories = $liste[$donnees['idProject']]['categories'] . ", " . $donnees['categories'];
                                    $bufDonnees = $donnees;
                                    $bufDonnees['categories'] = $concatCategories;
                                    $projetModif = $this->projetModif($donnees['name'], $bufDonnees, $erreur);
                                    $liste[$donnees['idProject']]['categories'] = $concatCategories;
                                    //on verifie quand ajoutant ce categories qu'il soit toujours egale au last_cat sinon on transfert dans la liste modif
                                    if ($projetModif) {
                                        $listeModif[$donnees['idProject']] = $liste[$donnees['idProject']];
                                        $listeModif[$donnees['idProject']]["last_name"] = $donnees['last_name'];
                                        $listeModif[$donnees['idProject']]["last_cat"] = $donnees['last_cat'];
                                        unset($liste[$donnees['idProject']]);
                                    }
                                } else {
                                    $concatCategories = $listeModif[$donnees['idProject']]["categories"] . ", " . $donnees['categories'];
                                    $bufDonnees = $donnees;
                                    $bufDonnees['categories'] = $concatCategories;
                                    $projetModif = $this->projetModif($donnees['name'], $bufDonnees, $erreur);
                                    $listeModif[$donnees['idProject']]["categories"] = $concatCategories;
                                    //on verifie quand ajoutant ce categories qu'il ne soit pas egale au last_cat sinon on transfert dans la liste normal
                                    if (!$projetModif) {
                                        $cptNbProjets++;
                                        unset($listeModif[$donnees['idProject']]["last_name"]);
                                        unset($listeModif[$donnees['idProject']]["last_cat"]);
                                        $liste[$donnees['idProject']] = $listeModif[$donnees['idProject']];
                                        unset($listeModif[$donnees['idProject']]);
                                    }
                                }
                            }

                            if (!$projetModif) {
                                //recherche les différents categories
                                if (strstr($catForm, "stand")) {
                                    $liste[$donnees['idProject']]['categories'] = "Stand-by";
                                    $cptEtats["Stand-by"]++;
                                } elseif (strstr($catForm, "abandonne")) {
                                    $liste[$donnees['idProject']]['categories'] = "Abandonné";
                                    $cptEtats["Abandonné"]++;
                                } elseif (strstr($catForm, "projet")) {
                                    $now = new \DateTime(date("Y-m-d"));
                                    $startDate = new \DateTime($donnees['start_date']);
                                    $endDate = new \DateTime($donnees['end_date']);

                                    //anomalie si le projet est ferme mais que la date de fin et dans le futur
                                    if (!$donnees['is_active'] and $donnees['end_date'] != "" and $endDate > $now) {
                                        $liste[$donnees['idProject']]['categories'] = "En anomalie";
                                        $cptEtats["En anomalie"]++;
                                    }else if ($donnees['start_date'] != "" and $startDate > $now) {
                                        $liste[$donnees['idProject']]['categories'] = "Futur";
                                        $cptEtats["Futur"]++;
                                    }else if ($donnees['end_date'] != "" and $endDate > $now) {
                                        $liste[$donnees['idProject']]['categories'] = "En cours";
                                        $cptEtats["En cours"]++;
                                    }else if ($donnees['end_date'] != "" and $endDate < $now) {
                                        if ($donnees['is_active']) {
                                            $liste[$donnees['idProject']]['categories'] = "En retard";
                                            $cptEtats['En retard']++;
                                        }else {
                                            $liste[$donnees['idProject']]['categories'] = "Terminé";
                                            $cptEtats["Terminé"]++;
                                        }
                                    } else if ($donnees['start_date'] != "" and $startDate < $now) {
                                        $liste[$donnees['idProject']]['categories'] = "En cours";
                                        $cptEtats["En cours"]++;
                                    }else {
                                        $liste[$donnees['idProject']]['categories'] = "En anomalie";
                                        $cptEtats["En anomalie"]++;
                                    }
                                } else {
                                    $liste[$donnees['idProject']]['categories'] = "En anomalie";
                                    $cptEtats["En anomalie"]++;
                                }
                            }
                        }
                    }
                }

            } else // Mais si elle rate…
            {
                $this->response->html($this->helper->layout->pageLayout('dosi:indicateurs/noAccess', array('title' => t('Catalogue d\'activités DOSI'), 'message' => 'Erreur de connection à la base de donnée')));
            }

            mysqli_close($this->mysqli);
        }

        $this->sendAllNotificationModifValid($listeModif);
        $this->response->html($this->helper->layout->pageLayout('dosi:indicateurs/projets', array(
            'cptNbProjets' => $cptNbProjets,
            'cptAbandonne' => $cptEtats['Abandonné'],
            'cptStandBy' => $cptEtats['Stand-by'],
            'cptTermine' => $cptEtats['Terminé'],
            'cptEnCours' => $cptEtats['En cours'],
            'cptFutur' => $cptEtats['Futur'],
            'cptSansCat' => $cptEtats['En anomalie'],
            'cptRetard' => $cptEtats['En retard'],
            'liste' => $liste,
            'listeModif' => $listeModif,
            'resPost' => $resPost,
            'droitValide' => $droitValide,
            'etats' => $etats,
            'title' => t('Catalogue d\'activité DOSI')), 'dosi:layout'));
    }

    /**
     * Indicateurs exploitation page
     *
     * @access public
     */
    public function exploit()
    {
        $cptNbExploit = 0;
        $cptNbPerim = 0;
        $liste = array();
        $listeModif = array();
        $resPost = "";
        $droit = false;
        $columnRenvoullement = array();
        $etats = array("Abandonné", "En cours", "En retard", "En anomalie", "Stand-by", "Terminé");
        $cptEtats=array("Abandonné" => 0, "En anomalie" => 0, "Stand-by" => 0, "En cours" => 0, "Terminé" => 0, "Futur" => 0, "En retard" => 0);

        $user = $this->getUser();
        $droitValide = $this->isAdmin($user);

        if(isset($_POST['idProjet'])){
            //ajoute l'ancre pour retourné a l'endroit de l'action
            $ancre = $_POST['ancre'];
            if ($this->mysqli = mysqli_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_NAME)) {
                $value = json_decode($_POST['value'],true);
                //verifie si le projet exist dans la table valide_projet
                $projetValide = $this->isExistTableValide($_POST['idProjet']);
                //le projet n'est pas dans la table valide projet ce qui ne doit pas se produire normalement :)
                if(isset($projetValide)){
                    //met a jour la table
                    $queryUpdate = "UPDATE valide_projet set valide=".$_POST['valide'].", modifie=".$_POST['modifie'].", priorite='".$_POST['priorite']."', last_name ='".mysqli_escape_string($this->mysqli,$value['name'])."', last_cat='".mysqli_escape_string($this->mysqli,$value['etat'])."'
                     , last_chef_DOSI='".mysqli_escape_string($this->mysqli,$value['owner'])."', last_ref_tech='".mysqli_escape_string($this->mysqli,$value['refTech'])."', last_sup_tech='".mysqli_escape_string($this->mysqli,$value['supTech'])."', last_fonctionnel='".mysqli_escape_string($this->mysqli,$value['fonctionnel'])."', last_description='".mysqli_escape_string($this->mysqli,$value['description'])."', last_renouvellement='".mysqli_escape_string($this->mysqli,$value['renouvellement'])."'
                     WHERE project_id=".$_POST['idProjet'];
                    $resQueryUpdate = mysqli_query($this->mysqli, $queryUpdate);
                    if(!$resQueryUpdate)
                        $resPost = "la mise à jour de l'activité à echouée.";

                    if($resPost == ''){
                        //Click bouton "modifier"
                        if($_POST['modifie'] != $projetValide['modifie']){
                            $resPost = "L'activité ".$value['name']." à été modifié.";
                            $this->sendNotificationAdmin(array('id' => $_POST['idProjet'], 'name' => $value['name']), 'modif');
                        }else if ($_POST['valide'] != $projetValide['valide']) {//click bouton refuser
                            $resPost = "L'activité ".$value['name']." à été refusé.";
                            $this->sendNotificationAdmin(array('id' => $_POST['idProjet'], 'name' => $value['name']), 'refus');
                        }else { //changement priorité
                            $resPost = "L'activité ".$value['name']." à changé de priorité. (".$projetValide['priorite']." => ".$_POST['priorité'].")";
                            $this->sendNotificationAdmin(array('id' => $_POST['idProjet'], 'name' => $value['name']), $_POST['priorité']);
                        }
                    }
                }else
                    $resPost = "Erreur l'activité n'a pas été trouvé dans la table valide projet";
            }else{
                $resPost = "Erreur de connection à la base de donnée";
            }
        }

        //recherche les uid du personnel DOSI
        $uids = $this->searchUidsDosi();

        if(count($uids) == 0){
            $this->response->html($this->helper->layout->pageLayout('dosi:indicateurs/noAccess', array('title' => t('Catalogue d\'activités DOSI'), 'message' => 'ERREUR au niveau du LDAP'), 'dosi:layout'));
        }else {
            if ($this->mysqli = mysqli_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_NAME)) {
                $tabTotal = $this->searchProjets($uids);

                foreach ($tabTotal as $donnees) {
                    if($donnees['valide'] != null && $donnees['valide'] == "1") {
                        $donnees['categories'] = $this->getAllCategoriesProjets($donnees['idProject']);

                        $donnees['type'] = $this->getTypeActivite($donnees['categories']);

                        if ($this->isExploitation($donnees)) {
                            $donnees['etat'] = $this->getCategorieExploit($donnees);

                            if ($donnees['last_cat'] == '' || $donnees['last_cat'] == null)
                                $donnees['last_cat'] = 'En anomalie';

                            $infoDesc = $this->getInfoDesc($donnees['name'], $donnees['description'], $erreur);

                            //verifie si il y a eu modification du nom et ou categorie de projet
                            $projetModif = $this->projetModif($donnees['name'], $donnees, $erreur);
                            if (!$projetModif) {
                                $now = new \DateTime(date("Y-m-d"));
                                $endDate = new \DateTime($donnees['end_date']);
                                if ($donnees['end_date'] != "" and $endDate < $now) {
                                    $cptNbPerim++;
                                } else {
                                    $cptNbExploit++;
                                }

                                if ($donnees['end_date'] != "") {
                                    if (!isset($columnRenvoullement[$endDate->getTimestamp() * 1000]))
                                        $columnRenvoullement[$endDate->getTimestamp() * 1000] = array("name" => $donnees['name'], "x" => $endDate->getTimestamp() * 1000, "y" => 1);
                                    else {
                                        $columnRenvoullement[$endDate->getTimestamp() * 1000] = array("name" => $columnRenvoullement[$endDate->getTimestamp() * 1000]['name'] . '<br> ' . $donnees['name'], "x" => $endDate->getTimestamp() * 1000, "y" => $columnRenvoullement[$endDate->getTimestamp() * 1000]['y'] + 1);
                                    }
                                }
                                $liste[$donnees['idProject']] = array("name" => $donnees['name'],
                                    "priorite" => $donnees['priorite'],
                                    "owner" => $donnees['owner'],
                                    "refTech" => $infoDesc['refTech'],
                                    "supTech" => $infoDesc['supTech'],
                                    "fonctionnel" => $infoDesc['fonctionnel'],
                                    "categories" => $donnees['categories'],
                                    "etat" => $donnees['etat'],
                                    "type" => $donnees['type'],
                                    "description" => $infoDesc['description'],
                                    "renouvellement" => $donnees['end_date']);
                            } else {
                                $listeModif[$donnees['idProject']] = array("name" => $donnees['name'],
                                    "priorite" => $donnees['priorite'],
                                    "owner" => $donnees['owner'],
                                    "refTech" => $infoDesc['refTech'],
                                    "supTech" => $infoDesc['supTech'],
                                    "fonctionnel" => $infoDesc['fonctionnel'],
                                    "categories" => $donnees['categories'],
                                    "etat" => $donnees['etat'],
                                    "type" => $donnees['type'],
                                    "description" => $infoDesc['description'],
                                    "last_name" => $donnees['last_name'],
                                    "last_cat" => $donnees['last_cat'],
                                    "last_chef_DOSI" => $donnees['last_chef_DOSI'],
                                    "last_ref_tech" => $donnees['last_ref_tech'],
                                    "last_sup_tech" => $donnees['last_sup_tech'],
                                    "last_fonctionnel" => $donnees['last_fonctionnel'],
                                    "last_description" => $donnees['last_description'],
                                    "last_renouvellement" => $donnees['last_renouvellement'],
                                    "renouvellement" => $donnees['end_date']);

                            }
                        }

                        if (!$projetModif) {
                            $liste[$donnees['idProject']]['categories'] = $donnees['categories'];
                            $liste[$donnees['idProject']]['etat'] = $donnees['etat'];
                            $cptEtats[$donnees['etat']]++;
                        }
                    }
                }

            } else // Mais si elle rate…
            {
                $this->response->html($this->helper->layout->pageLayout('dosi:indicateurs/noAccess', array('title' => t('Catalogue d\'activités DOSI'), 'message' => 'Erreur de connection à la base de donnée'), 'dosi:layout'));
            }

            mysqli_close($this->mysqli);

        }

        $this->sendAllNotificationModifValid($listeModif);
        $this->response->html($this->helper->layout->pageLayout('dosi:indicateurs/exploit', array(
            'columnRenvoullement' => array_values($columnRenvoullement),
            'cptNbExploit' => $cptNbExploit,
            'cptNbPerim' => $cptNbPerim,
            'cptAbandonne' => $cptEtats['Abandonné'],
            'cptStandBy' => $cptEtats['Stand-by'],
            'cptTermine' => $cptEtats['Terminé'],
            'cptEnCours' => $cptEtats['En cours'],
            'cptSansCat' => $cptEtats['En anomalie'],
            'cptRetard' => $cptEtats['En retard'],
            'liste' => $liste,
            'listeModif' => $listeModif,
            'resPost' => $resPost,
            'droit' => $droit,
            'droitValide' => $droitValide,
            'cptEtats' => $cptEtats,
            'etats' => $etats,
            'title' => t('Catalogue d\'activité DOSI')), 'dosi:layout'));
    }

    /**
     * Indicateurs modifié page
     *
     * @access public
     */
    public function modif()
    {
        $cptNbModif = 0;
        $liste = array();
        $listeModif = array();
        $resPost = "";
        $droit = false;
        $etats = array("Abandonné", "En anomalie", "Stand-by", "En cours", "Terminé", "Futur");

        $user = $this->getUser();
        $droitValide = $this->isAdmin($user);
        if(isset($_POST['idProjet'])){
            //ajoute l'ancre pour retourné a l'endroit de l'action
            $ancre = $_POST['ancre'];
            if ($this->mysqli = mysqli_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_NAME)) {
                $value = json_decode($_POST['value'],true);
                //verifie si le projet exist dans la table valide_projet
                $projetValide = $this->isExistTableValide($_POST['idProjet']);

                if(isset($projetValide)){
                    //met a jour la table
                    $queryUpdate = "UPDATE valide_projet set valide=".$_POST['valide'].", modifie=".$_POST['modifie'].", priorite='".$_POST['priorite']."', last_name ='".mysqli_escape_string($this->mysqli,$value['name'])."', last_cat='".mysqli_escape_string($this->mysqli,$value['categories'])."'
                     , last_chef_DOSI='".mysqli_escape_string($this->mysqli,$value['owner'])."', last_ref_tech='".mysqli_escape_string($this->mysqli,$value['refTech'])."', last_sup_tech='".mysqli_escape_string($this->mysqli,$value['supTech'])."', last_fonctionnel='".mysqli_escape_string($this->mysqli,$value['fonctionnel'])."', last_description='".mysqli_escape_string($this->mysqli,$value['description'])."', last_renouvellement='".mysqli_escape_string($this->mysqli,$value['renouvellement'])."'
                     WHERE project_id=".$_POST['idProjet'];

                    $resQueryUpdate = mysqli_query($this->mysqli, $queryUpdate);
                    if(!$resQueryUpdate)
                        $resPost = "la mise à jour de l'activité à echouée.";

                    if($resPost == ''){
                        //Click bouton "modifier"
                        if($_POST['modifie'] != $projetValide['modifie']){
                            $resPost = "L'activité ".$value['name']." à été modifié.";
                            $this->sendNotificationAdmin(array('id' => $_POST['idProjet'], 'name' => $value['name']), 'modif');
                        }else if ($_POST['valide'] != $projetValide['valide']) {//click bouton refuser
                            $resPost = "L'activité ".$value['name']." à été refusé.";
                            $this->sendNotificationAdmin(array('id' => $_POST['idProjet'], 'name' => $value['name']), 'refus');
                        }else { //changement priorité
                            $resPost = "L'activité ".$value['name']." à changé de priorité. (".$projetValide['priorite']." => ".$_POST['priorité'].")";
                            $this->sendNotificationAdmin(array('id' => $_POST['idProjet'], 'name' => $value['name']), $_POST['priorité']);
                        }
                    }
                }else //le projet n'est pas dans la table valide projet ce qui ne doit pas se produire normalement :)
                    $resPost = "Erreur l'activité n'a pas été trouvé dans la table valide projet";
            }else{
                $resPost = "Erreur de connection à la base de donnée";
            }
        }

        //recherche les uid du personnel DOSI
        $uids = $this->searchUidsDosi();

        if(count($uids) == 0){
            $this->response->html($this->helper->layout->pageLayout('dosi:indicateurs/noAccess', array('title' => t('Catalogue d\'activités DOSI'), 'message' => 'ERREUR au niveau du LDAP'), 'dosi:layout'));
        }else {
            if ($this->mysqli = mysqli_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_NAME)) {
                $tabTotal = $this->searchProjets($uids);

                foreach ($tabTotal as $donnees) {
                    if($donnees['valide'] != null && $donnees['valide'] == "1") {

                        $donnees['categories'] = $this->getAllCategoriesProjets($donnees['idProject']);

                        $donnees['type'] = $this->getTypeActivite($donnees['categories']);
                        if($this->isProjet($donnees))
                            $donnees['etat'] = $this->getEtatProjet($donnees);
                        else
                            $donnees['etat'] = $this->getEtatExploit($donnees);

                        //$this->validAllModif($donnees);

                        $infoDesc = $this->getInfoDesc($donnees['name'], $donnees['description'], $erreur);

                        //verifie si il y a eu modification du nom et ou categorie de projet
                        $projetModif = $this->projetModif($donnees['name'], $donnees, $erreur);

                        if (!$projetModif) {
                            $liste[$donnees['idProject']] = array(  "name" =>$donnees['name'],
                                "priorite" => $donnees['priorite'],
                                "owner" => $donnees['owner'],
                                "refTech" => $infoDesc['refTech'],
                                "supTech" => $infoDesc['supTech'],
                                "fonctionnel" => $infoDesc['fonctionnel'],
                                "etat" => $donnees['etat'],
                                "type" => $donnees['type'],
                                "description" => $infoDesc['description'],
                                "renouvellement" => $donnees['end_date']);
                        } else {
                            $cptNbModif++;
                            $listeModif[$donnees['idProject']] = array("name" =>$donnees['name'],
                                "priorite" => $donnees['priorite'],
                                "owner" => $donnees['owner'],
                                "refTech" => $infoDesc['refTech'],
                                "supTech" => $infoDesc['supTech'],
                                "fonctionnel" => $infoDesc['fonctionnel'],
                                "etat" => $donnees['etat'],
                                "type" => $donnees['type'],
                                "description" => $infoDesc['description'],
                                "last_name" => $donnees['last_name'],
                                "last_cat" => $donnees['last_cat'],
                                "last_chef_DOSI" => $donnees['last_chef_DOSI'],
                                "last_ref_tech" => $donnees['last_ref_tech'],
                                "last_sup_tech" => $donnees['last_sup_tech'],
                                "last_fonctionnel" => $donnees['last_fonctionnel'],
                                "last_description" => $donnees['last_description']);

                            if($this->isProjet($donnees)) {
                                $listeModif[$donnees['idProject']]['start_date'] = $donnees['start_date'];
                                $listeModif[$donnees['idProject']]['end_date'] = $donnees['end_date'];
                            }else {
                                $listeModif[$donnees['idProject']]['renouvellement'] = $donnees['end_date'];
                                $listeModif[$donnees['idProject']]['last_renouvellement'] = $donnees['last_renouvellement'];
                            }
                        }
                    }

                }

            } else // Mais si elle rate…
            {
                $this->response->html($this->helper->layout->pageLayout('dosi:indicateurs/noAccess', array('title' => t('Catalogue d\'activités DOSI'), 'message' => 'Erreur de connection à la base de donnée'), 'dosi:layout'));
            }

            mysqli_close($this->mysqli);

        }

        $this->sendAllNotificationModifValid($listeModif);
        $this->response->html($this->helper->layout->pageLayout('dosi:indicateurs/modif', array(
            'cptNbModif' => $cptNbModif,
            'liste' => $liste,
            'listeModif' => $listeModif,
            'resPost' => $resPost,
            'droit' => $droit,
            'droitValide' => $droitValide,
            'etats' => $etats,
            'title' => t('Catalogue d\'activité DOSI')), 'dosi:layout'));
    }

    /**
     * Indicateurs en attente page
     *
     * @access public
     */
    public function attente()
    {
        $cptNbAttente = 0;
        $listeNonValide = array();
        $resPost = "";
        $droit = false;
        $ancre = null;
        $etats = array("Abandonné", "Sans categories", "Stand-by", "En cours", "Terminé", "Futur");

        $user = $this->getUser();
        $droitValide = $this->isAdmin($user);

        //Post
        if(isset($_POST['idProjet'])){
            //ajoute l'ancre pour retourné a l'endroit de l'action
            $ancre = $_POST['ancre'];
            if ($this->mysqli = mysqli_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_NAME)) {
                $value = json_decode($_POST['value'],true);
                //verifie si le projet exist dans la table valide_projet
                $projetValide = $this->isExistTableValide($_POST['idProjet']);
                //le projet n'est pas dans la table valide projet ce qui ne doit pas se produire normalement :)
                if(isset($projetValide)){
                    //met a jour la table
                    $queryUpdate = "UPDATE valide_projet set valide=".$_POST['valide'].", modifie=".$_POST['modifie'].", priorite='".$_POST['priorite']."', last_name ='".mysqli_escape_string($this->mysqli,$value['name'])."', last_cat='".mysqli_escape_string($this->mysqli,$value['categories'])."'
                     , last_chef_DOSI='".mysqli_escape_string($this->mysqli,$value['owner'])."', last_ref_tech='".mysqli_escape_string($this->mysqli,$value['refTech'])."', last_sup_tech='".mysqli_escape_string($this->mysqli,$value['supTech'])."', last_fonctionnel='".mysqli_escape_string($this->mysqli,$value['fonctionnel'])."', last_description='".mysqli_escape_string($this->mysqli,$value['description'])."', last_renouvellement='".mysqli_escape_string($this->mysqli,$value['renouvellement'])."'
                     WHERE project_id=".$_POST['idProjet'];
                    $resQueryUpdate = mysqli_query($this->mysqli, $queryUpdate);
                    if(!$resQueryUpdate)
                        $resPost = "la mise à jour de l'activité à echouée.";
                }else{
                    $queryInsert = "INSERT INTO valide_projet (project_id, valide, modifie, priorite, last_name, last_cat, last_chef_DOSI, last_ref_tech, last_sup_tech, last_fonctionnel, last_description, last_renouvellement) 
                                  VALUES(".$_POST['idProjet'].",".$_POST['valide'].",".$_POST['modifie'].",'".$_POST['priorite']."','".mysqli_escape_string($this->mysqli,$value['name'])."','".mysqli_escape_string($this->mysqli,$value['categories'])."','".mysqli_escape_string($this->mysqli,$value['owner'])."','".mysqli_escape_string($this->mysqli,$value['refTech'])."','".mysqli_escape_string($this->mysqli,$value['supTech'])."','".mysqli_escape_string($this->mysqli,$value['fonctionnel'])."','".mysqli_escape_string($this->mysqli,$value['description'])."','".mysqli_escape_string($this->mysqli,$value['renouvellement'])."')";
                    $resQueryInsert = mysqli_query($this->mysqli, $queryInsert);
                    if(!$resQueryInsert)
                        $resPost = "la mise a jour (création) de l'activité à echouée.";
                }

                if($resPost == ''){
                    //Click bouton "modifier"
                    if($_POST['modifie'] != $projetValide['modifie']){
                        $resPost = "L'activité ".$value['name']." à été modifié.";
                        $this->sendNotificationAdmin(array('id' => $_POST['idProjet'], 'name' => $value['name']), 'modif');
                    }else if ($_POST['valide'] != $projetValide['valide']) {//click bouton refuser
                        $resPost = "L'activité ".$value['name']." à été refusé.";
                        $this->sendNotificationAdmin(array('id' => $_POST['idProjet'], 'name' => $value['name']), 'refus');
                    }else { //changement priorité
                        $resPost = "L'activité ".$value['name']." à changé de priorité. (".$projetValide['priorite']." => ".$_POST['priorité'].")";
                        $this->sendNotificationAdmin(array('id' => $_POST['idProjet'], 'name' => $value['name']), $_POST['priorité']);
                    }
                }
                $resPost = "Erreur de connection à la base de donnée";
            }
        }

        //recherche les uid du personnel DOSI
        $uids = $this->searchUidsDosi();

        if(count($uids) == 0){
            $this->response->html($this->helper->layout->pageLayout('dosi:indicateurs/noAccess', array('title' => t('Catalogue d\'activités DOSI'), 'message' => 'ERREUR au niveau du LDAP'), 'dosi:layout'));
        }else {
            if ($this->mysqli = mysqli_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_NAME)) {
                $tabTotal = $this->searchProjets($uids);

                foreach ($tabTotal as $donnees) {
                    if ($donnees['categories'] == '' || $donnees['categories'] == null)
                        $donnees['categories'] = '-';

                    //on comptabilise seulement les projets non valide
                    if($donnees['valide'] == null || $donnees['valide'] != "1") {
                        if (!array_key_exists($donnees['idProject'], $listeNonValide)) {
                            $cptNbAttente ++;
                            $date = new \DateTime();
                            $date->setTimestamp($donnees['last_modified']);
                            $infoDesc = $this->getInfoDesc($donnees['name'], $donnees['description'], $erreur);
                            $newCategories = $donnees['categories'];
                            $listeNonValide[$donnees['idProject']] = array(  "name" =>$donnees['name'],
                                "modifie" => false,
                                "owner" => $donnees['owner'],
                                "priorite" => $donnees['priorite'],
                                "refTech" => $infoDesc['refTech'],
                                "supTech" => $infoDesc['supTech'],
                                "fonctionnel" => $infoDesc['fonctionnel'],
                                "categories" => $donnees['categories'],
                                "description" => $infoDesc['description']);

                            if($this->isProjet($donnees)) {
                                $listeNonValide[$donnees['idProject']]['type'] = 'Projet';
                                $listeNonValide[$donnees['idProject']]['start_date'] = $donnees['start_date'];
                                $listeNonValide[$donnees['idProject']]['end_date'] = $donnees['end_date'];
                            }else {
                                $listeNonValide[$donnees['idProject']]['type'] = 'Exploitation';
                                $listeNonValide[$donnees['idProject']]['renouvellement'] = $donnees['end_date'];
                            }
                        }else{
                            $newCategories = $listeNonValide[$donnees['idProject']]["categories"] . ", " . $donnees['categories'];

                            $listeNonValide[$donnees['idProject']]["categories"] = $newCategories;
                        }
                    }
                }
            } else // Mais si elle rate…
            {
                $this->response->html($this->helper->layout->pageLayout('dosi:indicateurs/noAccess', array('title' => t('Catalogue d\'activités DOSI'), 'message' => 'Erreur de connection à la base de donnée'), 'dosi:layout'));
            }
            mysqli_close($this->mysqli);
        }
        $this->response->html($this->helper->layout->pageLayout('dosi:indicateurs/attente', array(
            'etats' => $etats,
            'listeNonValide' => $listeNonValide,
            'cptNbAttente' => $cptNbAttente,
            'resPost' => $resPost,
            'droit' => $droit,
            'droitValide' => $droitValide,
            'title' => t('Catalogue d\'activité DOSI'),
            'ancre' => $ancre), 'dosi:layout'));
    }


    /*
     * Recupere le référent fonctionnel, référent technique et la description du projet dans la description du projet (convention DOSI)
     * UPDATE projects SET description = REPLACE(description, 'Correspondant fonctionnel', 'Référent fonctionnel')
    UPDATE projects SET description = REPLACE(description, 'Chef de projet Technique', 'Référent technique')
     */
    private function getInfoDesc($name, $data, &$erreur){
        $fonctionnel = "";
        $desc = "";
        $tech = "";
        $supTech = "";
        $ren = "";
        $wiki = "";

        $dataLignes = explode("\n", $data);
        foreach($dataLignes as $dataligne) {
            $dataligneSansAccent = $this->wd_remove_accents($dataligne);
            //recuperation des info de la description référent fonctionnel + description
            $fonctReg = preg_match("/referent fonctionnel/i", $dataligneSansAccent);
            if ($fonctReg == 1){
                $fonctExpl = explode(':', $dataligne, 2);
                if (count($fonctExpl) < 2) {
                    $erreur[$name][] = "Erreur référent fonctionnel dans la description";
                } else
                    $fonctionnel = $fonctExpl[1];

                continue;
            }

            //recuperation des info de la description référent technique + description
            $techReg = preg_match("/referent technique/i", $dataligneSansAccent);
            if ($techReg == 1){
                $techRegExpl = explode(':', $dataligne, 2);
                if (count($techRegExpl) < 2) {
                    $erreur[$name][] = "Erreur référent technique dans la description";
                } else
                    $tech = $techRegExpl[1];

                continue;
            }

            //recuperation des info de la description suppleant technique + description
            $supReg = preg_match("/suppleant technique/i", $dataligneSansAccent);
            if ($supReg == 1){
                $supRegExpl = explode(':', $dataligne, 2);
                if (count($supRegExpl) < 2) {
                    $erreur[$name][] = "Erreur supléant technique dans la description";
                } else
                    $supTech = $supRegExpl[1];

                continue;
            }

            //description
            $descReg = preg_match("/description/i", $dataligneSansAccent);
            if ($descReg == 1){
                $descExpl = explode(':', $dataligne, 2);
                if (count($descExpl) < 2) {
                    $erreur[$name][] = "Erreur champ description dans la description";
                } else
                    $desc = $descExpl[1];

                continue;
            }

            //ajout du lien wiki dans la description
            $wikiReg = preg_match("/intracri/i", $dataligneSansAccent);
            if ($wikiReg == 1){
                $wikiExpl = explode(':', $dataligne, 2);
                if (count($wikiExpl) < 2) {
                    $erreur[$name][] = "Erreur champ lien intracri dans la description";
                } else {
                    if(trim($wikiExpl[1]) != "")
                        $wiki = "</br><a href=\"" . $wikiExpl[1] . "\" target='_blank'>wiki</a>";
                }
                continue;
            }
        }
        //var_dump(array("fonctionnel" => $fonctionnel, "description" => $desc, "refTech" => $tech, "supTech" => $supTech, "renouvellement" => $ren));
        return array("fonctionnel" => $fonctionnel, "description" => $desc.$wiki, "refTech" => $tech, "supTech" => $supTech, "wiki" => $wiki);
    }


    /*
     * Permet de voir si un projet a été modifié
     * $flagInfoDesc = true : doit recherche les info dans la description
     * $flagInfoDesc = false : les info sont deja dans le tableau (refTech, supTech, description)
     *      */
    private function projetModif($name, $donnees, &$erreur, $flagInfoDesc = true){
        //si le projet est pas actif ne pas le prendre en compte
        if($donnees['is_active'] == false)
            return false;
        if($flagInfoDesc) {
            $infoDesc = $this->getInfoDesc($name, $donnees['description'], $erreur);
            $donnees['refTech'] = $infoDesc['refTech'];
            $donnees['supTech'] = $infoDesc['supTech'];
            $donnees['fonctionnel'] = $infoDesc['fonctionnel'];
            $donnees['description'] = $infoDesc['description'];
            $donnees['renouvellement'] = $donnees['end_date'];
        }

        if($donnees['modifie'] == 1) {
            var_dump("modifie");
            return true;
        }elseif(strtolower($donnees['last_name']) != strtolower($donnees['name'])){
            var_dump("name");
            return true;
        } elseif($this->miseEnFormeCat($donnees['last_cat']) != $this->miseEnFormeCat($donnees['etat'])){
            var_dump("etat");
            return true;
        } elseif(strtolower($donnees['last_chef_DOSI']) != strtolower($donnees['owner'])){
            var_dump("owner");
            return true;
        }elseif(strtolower($donnees['last_ref_tech']) != strtolower($donnees['refTech'])){
            var_dump("refTech");
            return true;
        }elseif(strtolower($donnees['last_sup_tech']) != strtolower($donnees['supTech'])){
             var_dump("supTech");
            return true;
        }elseif(strtolower($donnees['last_fonctionnel']) != strtolower($donnees['fonctionnel'])){
            var_dump("fonctionnel");
            return true;
        }elseif(strtolower($donnees['last_description']) != strtolower($donnees['description'])){
            var_dump("description");
            return true;
        }elseif($this->isExploitation($donnees) && strtolower($donnees['last_renouvellement']) != strtolower($donnees['renouvellement'])){
            var_dump("description");
            return true;
        }


        return false;
    }

    public function modifPriorite(){
        $resPost = "";
        if(isset($_POST['idProjet'])){
            //ajoute l'ancre pour retourné a l'endroit de l'action
            $ancre = $_POST['ancre'];
            if ($this->mysqli = mysqli_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_NAME)) {
                //recupere les info du projet
                $querySelect = "SELECT name FROM projects WHERE id=".$_POST['idProjet'];
                $resQuerySelect = mysqli_query($this->mysqli, $querySelect);
                $projet = mysqli_fetch_array($resQuerySelect);

                //verifie si le projet exist dans la table valide_projet
                $querySelectV = "SELECT * FROM valide_projet WHERE project_id=".$_POST['idProjet'];
                $resQuerySelectV = mysqli_query($this->mysqli, $querySelectV);
                $projetValide = mysqli_fetch_array($resQuerySelectV);
                $value = json_decode($_POST['value'],true);

                //le projet existe dans la table valide_projet
                if(isset($projetValide)){
                    $queryUpdate = "UPDATE valide_projet set priorite='".$_POST['priorite']."' WHERE project_id=".$_POST['idProjet'];
                    $resQueryUpdate = mysqli_query($this->mysqli, $queryUpdate);
                    if(!$resQueryUpdate)
                        $resPost = "la mise à jour de l'activité à echouée.";
                }
                //le projet existe pas dans la table valide_projet
                else{
                    $queryInsert = "INSERT INTO valide_projet (project_id, valide, modifie, priorite, last_name, last_cat, last_chef_DOSI, last_ref_tech, last_sup_tech, last_fonctionnel, last_description, last_renouvellement) 
                                  VALUES(".$_POST['idProjet'].",0,0,'".$_POST['priorite']."','".mysqli_escape_string($this->mysqli,$value['name'])."','".mysqli_escape_string($this->mysqli,$value['categories'])."','".mysqli_escape_string($this->mysqli,$value['owner'])."','".mysqli_escape_string($this->mysqli,$value['refTech'])."','".mysqli_escape_string($this->mysqli,$value['supTech'])."','".mysqli_escape_string($this->mysqli,$value['fonctionnel'])."','".mysqli_escape_string($this->mysqli,$value['description'])."', '".mysqli_escape_string($this->mysqli,$value['renouvellement'])."')";
                    $resQueryInsert = mysqli_query($this->mysqli, $queryInsert);
                    if(!$resQueryInsert)
                        $resPost = "la mise à jour de l'activité à echouée.";
                }

                $projet = array('id' => $_POST['idProjet'], 'name' => $value['name']);
                $this->sendNotificationAdmin($projet, $_POST['priorite']);
            }else{
                $resPost = "Erreur de connection à la base de donnée";
            }
        }else
            return "nok";

        return "ok";
    }

    /*
     * envoyer un mail aux membres et chef de projet du projet donné lorsque l'admin fait une action
     * action = Valid, modif ou refus
     */
    public function sendNotificationAdmin($projet, $action)
    {
        $mails = $this->getMailsAdmins();
        if(!$this->getconfApi())
            return false;
        
        $httpClient = new HttpClient($this->url_api);
        $httpClient->withoutSslVerification();
        $client = new Client($this->url_api, false, $httpClient);
      
        $client->authentication('jsonrpc', $this->key_api);

        $members = $client->execute('getProjectUsers', array('project_id' => $projet['id']));

        foreach($members as $member) {
            if ($mysqli = mysqli_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_NAME)) {
                //recherche les projets ayant pour membre une personne de la dosi
                $query = "SELECT * FROM users WHERE name like '".$member."'";

                $resultat = mysqli_query($mysqli, $query);
                $row = mysqli_fetch_assoc($resultat);
                $mails[$row['email']] =$row['email'] ;
                mysqli_free_result($resultat);

            }else {
                var_dump("erreur base de donnée");
            }

        }
        if($action == "valid") {
            $message = "Votre activité \"" . $projet['name'] . "\" vient d'être validé.";
            $sujet = "[Activités DOSI] Validation de l'activité : \"".$projet['name']."\"";
        }else if($action == "modif") {
            $message = "Votre activité \"" . $projet['name'] . "\" vient d'être basculé dans le tableau \"modifié\" par l'administrateur.";
            $sujet = "[Activités DOSI] Modification de l'activité : \"".$projet['name']."\"";
        }else if($action == "refus") {
            $message = "Votre activité \"" . $projet['name'] . "\" vient d'être refusé.";
            $sujet = "[Activités DOSI] Refus de l'activité : \"".$projet['name']."\"";
        }else if($action == "Haute") {
            $message = "Votre activité \"" . $projet['name'] . "\" vient de passer en priorité Haute.";
            $sujet = "[Activités DOSI] Priorité de l'activité : \"".$projet['name']."\"";
        }else if($action == "Normal") {
            $message = "Votre activité \"" . $projet['name'] . "\" vient de passer en priorité Normal.";
            $sujet = "[Activités DOSI] Priorité de l'activité : \"".$projet['name']."\"";
        }else if($action == "Basse") {
            $message = "Votre activité \"" . $projet['name'] . "\" vient de passer en priorité Basse.";
            $sujet = "[Activités DOSI] Priorité de l'activité : \"".$projet['name']."\"";
        }else {
            return;
        }
        $headers = 'From: projets@univ-avignon.fr' . "\r\n";

        //commenter mail
        //mail ( implode(',', $mails) , $sujet , $message,$headers );

    }

    /*
     * Envoi a tous les projet modifié un mail aux membre concerné : tableau valide
     */
    public function sendAllNotificationModifValid($listes)
    {
        $mysqli = mysqli_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_NAME);
        foreach($listes as $key => $projet) {

            //Passe le projet en modifié pour ne pas envoyé plusieur fois la notifs
            //verifie si le projet exist dans la table valide_projet
            $querySelectV = "SELECT * FROM valide_projet WHERE project_id=".$key;
            $resQuerySelectV = mysqli_query($mysqli, $querySelectV);
            $projetValide = mysqli_fetch_array($resQuerySelectV);

            //le projet existe dans la table valide_projet
            if (isset($projetValide)) {
                if($projetValide['modifie'] == false){
                    $this->sendNotificationModif($key, $projet);
                    $queryUpdate = "UPDATE valide_projet set  modifie=TRUE WHERE project_id=" . $key;
                    $resQueryUpdate = mysqli_query($mysqli, $queryUpdate);
                    if (!$resQueryUpdate)
                        $resPost = "la mise à jour de l'activité à echouée.";
                }
            } //le projet existe pas dans la table valide_projet
            else {
                $queryInsert = "INSERT INTO valide_projet (project_id, valide, modifie, priorite, last_name, last_cat, last_chef_DOSI, last_ref_tech, last_sup_tech, last_fonctionnel, last_description)
                              VALUES(" . $key . ",false,true,'Normal','" . mysqli_escape_string($mysqli, $projet['name']) . "','" . mysqli_escape_string($mysqli, $projet['etat']) . "','" . mysqli_escape_string($mysqli, $projet['owner']) . "','" . mysqli_escape_string($mysqli, $projet['refTech']) . "','" . mysqli_escape_string($mysqli, $projet['supTech']) . "','" . mysqli_escape_string($mysqli, $projet['fonctionnel']) . "','" . mysqli_escape_string($mysqli, $projet['description']) . "')";
                $resQueryInsert = mysqli_query($mysqli, $queryInsert);
                if (!$resQueryInsert)
                    $resPost = "la mise à jour de l'activité à echouée.";
            }
        }
    }

    /*
     * Envoi a tous les projet modifié un mail aux membre concerné : tableau npon valide
     */
    public function sendAllNotificationEnAttente($listes)
    {
        $erreur = array();
        $mysqli = mysqli_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_NAME);

        foreach($listes as $key => $projet) {
            //Passe le projet en modifié pour ne pas envoyé plusieur fois la notifs
            //verifie si le projet exist dans la table valide_projet
            $querySelectV = "SELECT * FROM valide_projet WHERE project_id=".$key;
            $resQuerySelectV = mysqli_query($mysqli, $querySelectV);
            $projetValide = mysqli_fetch_array($resQuerySelectV);


            //le projet existe dans la table valide_projet
            if (isset($projetValide)) {
                $projet2 = array_merge($projetValide, $projet);
                if($this->projetModif($projet['name'],$projet2,$erreur, false) && $projetValide['modifie'] == false) {
                    $this->sendNotificationModif($key, $projet2);

                    $queryUpdate = "UPDATE valide_projet set  modifie=TRUE WHERE project_id=" . $key;
                    $resQueryUpdate = mysqli_query($mysqli, $queryUpdate);
                    if (!$resQueryUpdate)
                        $resPost = "la mise à jour de l'activité à echouée.";
                }
            } //le projet existe pas dans la table valide_projet
            else {
                $queryInsert = "INSERT INTO valide_projet (project_id, valide, modifie, priorite, last_name, last_cat, last_chef_DOSI, last_ref_tech, last_sup_tech, last_fonctionnel, last_description, last_renouvellement)
                              VALUES(" . $key . ",false,false,'Normal','" . mysqli_escape_string($mysqli, $projet['name']) . "','" . mysqli_escape_string($mysqli, $projet['categories']) . "','" . mysqli_escape_string($mysqli, $projet['owner']) . "','" . mysqli_escape_string($mysqli, $projet['refTech']) . "','" . mysqli_escape_string($mysqli, $projet['supTech']) . "','" . mysqli_escape_string($mysqli, $projet['fonctionnel']) . "','" . mysqli_escape_string($mysqli, $projet['description']) . "','" . mysqli_escape_string($mysqli, $projet['renouvellement']) . "')";
                $resQueryInsert = mysqli_query($mysqli, $queryInsert);
                if (!$resQueryInsert)
                    $resPost = "la mise à jour de l'activité à echouée.";
            }
        }
    }
    /*
     * envoyer un mail aux membres et chef de projet du projet donné lorsqu'il est modifié
     */
    public function sendNotificationModif($id, $projet)
    {
        $mails = $this->getMailsAdmins();
        if(!$this->getconfApi())
            return false;

        $httpClient = new HttpClient($this->url_api);
        $httpClient->withoutSslVerification();
        $client = new Client($this->url_api, false, $httpClient);
        $client->authentication('jsonrpc', $this->key_api);

        $members = $client->execute('getProjectUsers', array('project_id' => $id));

        foreach($members as $member) {
            if ($mysqli = mysqli_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_NAME)) {
                //recherche les projets ayant pour membre une personne de la dosi
                $query = "SELECT * FROM users WHERE name like '".$member."'";

                $resultat = mysqli_query($mysqli, $query);
                $row = mysqli_fetch_assoc($resultat);
                $mails[$row['email']] =$row['email'] ;
                mysqli_free_result($resultat);

            }else {
                var_dump("erreur base de donnée");
            }

        }

        //recherche les modifications
        $modifications = "";
        if(trim($projet["name"]) != trim($projet["last_name"]))
            $modifications .= "Nom : ".$projet["name"]."\nAncien : " . $projet["last_name"]."\r\n";
        if(trim($projet["owner"]) != trim($projet["last_chef_DOSI"]))
            $modifications .= "Chef de projet : ".$projet["owner"]."\nAncien : " . $projet["last_chef_DOSI"]."\r\n";
        if(trim($projet["refTech"]) != trim($projet["last_ref_tech"]))
            $modifications .= "Référent technique DOSI : ".$projet["refTech"]."\nAncien : " . $projet["last_ref_tech"]."\r\n";
        if(trim($projet["supTech"]) != trim($projet["last_sup_tech"]))
            $modifications .= "Supléant technique DOSI : ".$projet["supTech"]."\nAncien : " . $projet["last_sup_tech"]."\r\n";
        if(trim($projet["fonctionnel"]) != trim($projet["last_fonctionnel"]))
            $modifications .= "Référent fonctionnel : ".$projet["fonctionnel"]."\nAncien : " . $projet["last_fonctionnel"]."\r\n";
        if(trim($projet["categories"]) != trim($projet["last_cat"]))
            $modifications .= "Etat : ".$projet["categories"]."\nAncien : " . $projet["last_cat"]."\r\n";
        if(trim($projet["description"]) != trim($projet["last_description"]))
            $modifications .= "Description : ".$projet["description"]."\nAncien : " . $projet["last_description"]."\r\n";
        if(isset($projet["renouvellement"]) && isset($projet["last_renouvellement"])) {
            if (trim($projet["renouvellement"]) != trim($projet["last_renouvellement"]))
                $modifications .= "Renouvellement : " . $projet["renouvellement"] . "\nAncien : " . $projet["last_renouvellement"] . "\r\n";
        }

        $message = "Votre activité \"" . $projet['name'] . "\" vient d'être modifié.\r\n\r\n".$modifications;
        $sujet = "[Activités DOSI] Modification de : \"".$projet['name']."\"";

        $headers = 'From: projets@univ-avignon.fr' . "\r\n";

        //commenter mail
        //mail ( implode(',', $mails) , $sujet , $message,$headers );

    }

    function export()
    {

        //recherche les uid du personnel DOSI
        $conn_ldap = ldap_connect('ldap://ldap.univ-avignon.fr');
        $ldapSearch = ldap_search($conn_ldap, 'ou=people,dc=univ-avignon,dc=fr', "(supannaffectation=D.O.S.I*)");

        $ldapEntries = ldap_get_entries($conn_ldap, $ldapSearch);

        foreach($ldapEntries as $key => $value){
            $uids[] = $value['uid'][0];
        }

        //Pas les droits donc page d'erreur (Pas DOSI)
        /*if($droit == false){
            $this->response->html($this->helper->layout->pageLayout('dosi:indicateurs/noAccess', array('title' => t('Indicateurs DOSI'), 'message' => 'Vous n\'avez pas les droits pour acceder à cette page.')));
            return;
        }*/
        $liste = array();

        if(count($uids) == 0){
            $this->response->html($this->helper->layout->pageLayout('dosi:indicateurs/noAccess', array('title' => t('Catalogue d\'activités DOSI'), 'message' => 'ERREUR au niveau du LDAP'), 'dosi:layout'));
        }else {
            if ($mysqli = mysqli_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_NAME)) {

                //recherche les projets ayant pour owner une personne de la dosi
                $query = "SELECT p.last_modified, p.id as idProject, p.name, p.description, u.username, u.name as owner, pc.name as categories, vp.* FROM projects p left join users u on p.owner_id=u.id left join project_has_categories pc on p.id=pc.project_id left join valide_projet vp on p.id=vp.project_id WHERE is_private=0 AND (";
                foreach ($uids as $key => $value) {
                    if($value != '') {
                        if ($key == 1)
                            $query .= " u.username like '" . $value."'";
                        else
                            $query .= " OR u.username like '" . $value."'";
                    }
                }
                $query .= ")";

                $resultat = mysqli_query($mysqli, $query);
                while($row = mysqli_fetch_assoc($resultat)){
                    $tabOwner[] = $row;
                }
                mysqli_free_result($resultat);

                //recherche les projets ayant pour membre une personne de la dosi
                $query = "SELECT p.last_modified, p.id as idProject, p.name, p.description, u.username, uo.name as owner, pc.name as categories, vp.* FROM projects p left join project_has_users pu on pu.project_id = p.id left join users u on pu.user_id=u.id left join users uo on uo.id=p.owner_id left join project_has_categories pc on p.id=pc.project_id left join valide_projet vp on p.id=vp.project_id WHERE is_private=0 AND (";
                foreach ($uids as $key => $value) {
                    if($value != '') {
                        if ($key == 1)
                            $query .= " u.username like '" . $value."'";
                        else
                            $query .= " OR u.username like '" . $value."'";
                    }
                }
                $query .= ") AND ";
                foreach ($uids as $key => $value) {
                    if($value != '') {
                        if ($key == 1)
                            $query .= " uo.username not like '".$value."'";
                        else
                            $query .= " AND uo.username not like '" . $value."'";
                    }
                }

                $resultat = mysqli_query($mysqli, $query);
                while($row = mysqli_fetch_assoc($resultat)){
                    $tabMembre[] = $row;
                }
                mysqli_free_result($resultat);

                if(count($tabMembre) == 0 && count($tabOwner) == 0)
                    $tabTotal = array();
                elseif(count($tabMembre) == 0)
                    $tabTotal = $tabOwner;
                elseif(count($tabOwner) == 0)
                    $tabTotal = $tabMembre;
                else
                    $tabTotal = array_merge($tabMembre, $tabOwner);

                foreach ($tabTotal as $donnees) {
                    if ($donnees['categories'] == '' || $donnees['categories'] == null)
                        $donnees['categories'] = '-';

                    //on comptabilise seulement les projets valide

                    if($donnees['valide'] != null && $donnees['valide'] == "1") {
                        if (!array_key_exists($donnees['idProject'], $liste)) {
                            $desc = '';
                            $corresp = '';

                            if ($donnees['last_cat'] == '' || $donnees['last_cat'] == null)
                                $donnees['last_cat'] = '-';

                            $infoDesc = $this->getInfoDesc($donnees['name'], $donnees['description'], $erreur);


                            $liste[$donnees['idProject']] = array(  "name" =>$donnees['name'],
                                "priorite" => $donnees['priorite'],
                                "owner" => $donnees['owner'],
                                "refTech" => $infoDesc['refTech'],
                                "supTech" => $infoDesc['supTech'],
                                "fonctionnel" => $infoDesc['fonctionnel'],
                                "categories" => $donnees['categories'],
                                "renouvellement" => $donnees['renouvellement'],
                                "description" => $infoDesc['description']);

                        } else {
                            if (array_key_exists($donnees['idProject'], $liste)) {
                                $concatCategories = $liste[$donnees['idProject']]['categories'] . ", " . $donnees['categories'];
                                $bufDonnees = $donnees;
                                $bufDonnees['categories'] = $concatCategories;

                                $liste[$donnees['idProject']]['categories'] = $concatCategories;

                            }
                        }

                    }
                }

            } else // Mais si elle rate…
            {
                $this->response->html($this->helper->layout->pageLayout('dosi:indicateurs/noAccess', array('title' => t('Catalogue d\'activités DOSI'), 'message' => 'Erreur de connection à la base de donnée'), 'dosi:layout'));
            }

            mysqli_close($mysqli);


        }

// Paramétrage de l'écriture du futur fichier CSV
        $chemin = '/var/www/projets-test.univ-avignon.fr/www/plugins/Dosi/Data/export-projets-dosi.csv';
        $delimiteur = ';'; // Pour une tabulation, utiliser $delimiteur = "t";

// Création du fichier csv (le fichier est vide pour le moment)
// w+ : consulter http://php.net/manual/fr/function.fopen.php
        $fichier_csv = fopen($chemin, 'w+');

// Si votre fichier a vocation a être importé dans Excel,
// vous devez impérativement utiliser la ligne ci-dessous pour corriger
// les problèmes d'affichage des caractères internationaux (les accents par exemple)
        fprintf($fichier_csv, chr(0xEF).chr(0xBB).chr(0xBF));

// Boucle foreach sur chaque ligne du tableau
        foreach($liste as $ligne){
            // chaque ligne en cours de lecture est insérée dans le fichier
            // les valeurs présentes dans chaque ligne seront séparées par $delimiteur
            fputcsv($fichier_csv, array('DOSI',$ligne['name'], $ligne['description'],"","","","","","","","","","",$ligne['owner'], $ligne['refTech'], $ligne['fonctionnel'] ), $delimiteur);
        }

// fermeture du fichier csv
        fclose($fichier_csv);

    }

    function wd_remove_accents($str, $charset='utf-8')
    {
        $str = htmlentities($str, ENT_NOQUOTES, $charset);

        $str = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
        $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'
        $str = preg_replace('#&[^;]+;#', '', $str); // supprime les autres caractères

        return $str;
    }

    private function searchOwners($uids)
    {
        if ($this->mysqli == null)
            return array();
        $tabOwner = array();

        $query = "SELECT p.last_modified, p.id as idProject, p.name, p.description, p.start_date, p.end_date, p.is_active, u.username, u.name as owner, vp.* FROM projects p left join users u on p.owner_id=u.id left join valide_projet vp on p.id=vp.project_id WHERE is_private=0 AND (";
        foreach ($uids as $key => $value) {
            if($value != '') {
                if ($key == 1)
                    $query .= " u.username like '" . $value."'";
                else
                    $query .= " OR u.username like '" . $value."'";
            }
        }
        $query .= ")";

        $resultat = mysqli_query($this->mysqli, $query);
        while($row = mysqli_fetch_assoc($resultat)){
              $tabOwner[] = $row;
        }
        mysqli_free_result($resultat);
        return $tabOwner;
    }

    private function searchMembres($uids){
        if( $this->mysqli == null)
            return array();
        $tabMembre = array();
        $query = "SELECT p.last_modified, p.id as idProject, p.name, p.start_date, p.end_date, p.is_active, p.description, u.username, uo.name as owner, vp.* FROM projects p left join project_has_users pu on pu.project_id = p.id left join users u on pu.user_id=u.id left join users uo on uo.id=p.owner_id left join valide_projet vp on p.id=vp.project_id WHERE is_private=0 AND (";
        foreach ($uids as $key => $value) {
            if($value != '') {
                if ($key == 1)
                    $query .= " u.username like '" . $value."'";
                else
                    $query .= " OR u.username like '" . $value."'";
            }
        }
        $query .= ") AND ";
        foreach ($uids as $key => $value) {
            if($value != '') {
                if ($key == 1)
                    $query .= " uo.username not like '".$value."'";
                else
                    $query .= " AND uo.username not like '" . $value."'";
            }
        }

        $resultat = mysqli_query($this->mysqli, $query);
        while($row = mysqli_fetch_assoc($resultat)){
            $tabMembre[] = $row;
        }
        mysqli_free_result($resultat);
        return $tabMembre;
    }

    private function searchUidsDosi(){
        $conn_ldap = ldap_connect('ldap://ldap.univ-avignon.fr');
        $ldapSearch = ldap_search($conn_ldap, 'ou=people,dc=univ-avignon,dc=fr', "(supannaffectation=D.O.S.I*)");

        $ldapEntries = ldap_get_entries($conn_ldap, $ldapSearch);

        foreach($ldapEntries as $key => $value){
            $uids[] = $value['uid'][0];
        }
        return $uids;
    }

    /*
     * projet si :
     *  - categorie = projet peut avoir aussi en meme temps stand-by ou abandonné
     * Return true si c'est un projet
     */
    private function isProjet($donnees){
        if($donnees['type'] == "Projet")
            return true;

        return false;
    }

    /*
     * exploitation si :
     *  - categorie = exploitation : veut dire pas de categorie
     * Return true si c'est une exploitation
     */
    private function isExploitation($donnees){
        if($donnees['type'] == "Exploitation")
            return true;

        return false;
    }

    /*
     * recupere la liste admin du fichier configPlugin.txt
     */
    function getAdmins(){
        if(isset($this->admins))
            return $this->admins;

        $config = file_get_contents('plugins/Dosi/configPlugin.txt');
        $config=str_replace(' ','',$config);
        $explodeLigne = explode("\n", $config);

        foreach ($explodeLigne as $ligne){
            if(strpos($ligne, "admin:") !== false) {
                $explode = explode(":", $ligne)[1];
                $this->admins = explode(",", $explode);
            }
        }

        return $this->admins;
    }

    /*
     * admin = droit de valider ou non un projet
     * Return true si admin
     */
    private function isAdmin($user){
        $admins = $this->getAdmins();

        if(in_array($user['username'], $admins) ){
            return  true;
        }
        return false;
    }

    /*
     * uids = liste des uid dosi retourné par searchUidsDosi
     * Return true si dosi
     */
    private function isDosi($user, $uids){
        if(in_array($user['username'], $uids))
            return true;
        return false;
    }

    /*
     * Recherche les projets de la dosi
     * * Return le tableau total des membre + owner
     */
    private function searchProjets($uids){
        //recherche les projets ayant pour owner une personne de la dosi
        $tabOwner = $this->searchOwners($uids);

        //recherche les projets ayant pour membre une personne de la dosi
        $tabMembre = $this->searchMembres($uids);

        if(count($tabMembre) == 0 && count($tabOwner) == 0)
            return array();
        elseif(count($tabMembre) == 0)
            return $tabOwner;
        elseif(count($tabOwner) == 0)
            return $tabMembre;
        else
            return array_merge($tabMembre, $tabOwner);
    }

    /*
     * retourne null si il n'existe pas
     * retourne les données de la table pour ce projet si il exist
     */
    private function isExistTableValide($idProjet){
        //verifie si le projet exist dans la table valide_projet
        $querySelectV = "SELECT * FROM valide_projet WHERE project_id=".$idProjet;
        $resQuerySelectV = mysqli_query($this->mysqli, $querySelectV);
        $projetValide = mysqli_fetch_array($resQuerySelectV);
        if(!isset($projetValide))
            return null;
        return json_decode($_POST['value'],true);
    }

    /*
     * retourne les catégories
     * sans accent
     * trim
     * en minuscule
     *
     * return '-' si null ou vide
     */
    private function miseEnFormeCat($categories){
        $categories = strtolower($categories);
        $categories = $this->wd_remove_accents($categories);
        $categories = trim($categories);
        if ($categories == '' || $categories == null) {
            return '-';
        }
        return $categories;
    }

    /*
     * permet de d'inccrementer les compteurs du tableau histogramme...
     *$indexHistogramme 0,1,2,3,4,5,6 ou7 (année -3 jusqu'a année +3)
     *
     * return histogramme
     */
    private function compteurHistogrammeAccueil($histogramme, $indexHistogramme, $startDate, $endDate, $donnees, $histogrammeAnnee, &$histogrammeName){
        for($i = 0; $i < 7; $i++){
            if($donnees['start_date'] != "" and $startDate->format('Y') <= $histogrammeAnnee[$i] and $donnees['end_date'] != "" and $endDate->format('Y') >= $histogrammeAnnee[$i]){
                $histogramme[$indexHistogramme]['data'][$i]++;
                $histogrammeName[$indexHistogramme]['data'][$i] .= $donnees['name'].'<br>';
            }elseif ($donnees['start_date'] == "" and $donnees['end_date'] != "" and $endDate->format('Y') >= $histogrammeAnnee[$i] and ($startDate != $endDate)){
                $histogramme[$indexHistogramme]['data'][$i]++;
                $histogrammeName[$indexHistogramme]['data'][$i] .= $donnees['name'].'<br>';
            }elseif ($donnees['end_date'] == "" and $donnees['start_date'] != "" and $startDate->format('Y') <= $histogrammeAnnee[$i]){
                $histogramme[$indexHistogramme]['data'][$i]++;
                $histogrammeName[$indexHistogramme]['data'][$i] .= $donnees['name'].'<br>';
            }elseif ($startDate == $endDate and $donnees['end_date'] != "" and $endDate->format('Y') == $histogrammeAnnee[$i]){
                $histogramme[$indexHistogramme]['data'][$i]++;
                $histogrammeName[$indexHistogramme]['data'][$i] .= $donnees['name'].'<br>';
            }
        }
        return $histogramme;
    }

    /*
     * recupere le mail du user
     */
    function getMailUser($uid){
        if($this->getconfApi() !== false) {
            $httpClient = new HttpClient($this->url_api);
            $httpClient->withoutSslVerification();
            $client = new Client($this->url_api, false, $httpClient);
            $client->authentication('jsonrpc', $this->key_api);

            $user = $client->execute('getUserByName', array('username' => $uid));

            if(!isset($user))
                return false;
            return $user['email'];
        }
        return false;
    }

    /*
     * recupere les categories d'un projet
     */
    function getAllCategoriesProjets($idProjet){
        if ($this->mysqli = mysqli_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_NAME)) {
            //recupere les info du projet
            $querySelect = "SELECT name FROM project_has_categories WHERE project_id=" . $idProjet;
            $resQuerySelect = mysqli_query($this->mysqli, $querySelect);
            return mysqli_fetch_all($resQuerySelect);
        }
        return false;
    }

    /*
     * determine le type d'un projet en fonction de ses categories
     */
    function getTypeActivite($categories){
        $type = "Exploitation";
        foreach($categories as $categorie){
            if(strtolower($categorie[0]) === 'projet')
                return "Projet";
        }
        return $type;
    }

    /*
     * Recupere url + key de l'api
     */
    function getconfApi(){
        if(isset($this->url_api))
            return array("url_api"=>$this->url_api, "key_api"=>$this->key_api);

        $config = file_get_contents('plugins/Dosi/configPlugin.txt');
        $config=str_replace(' ','',$config);
        $explodeLigne = explode("\n", $config);

        foreach ($explodeLigne as $ligne){
            if(strpos($ligne, "url_api:") !== false)
                $this->url_api = explode("url_api:", $ligne)[1];

            if(strpos($ligne, "key_api:") !== false)
                $this->key_api = explode("key_api:", $ligne)[1];

        }

        if(!isset($this->url_api) or !isset($this->key_api))
            return false;

        return array("url_api"=>$this->url_api, "key_api"=>$this->key_api);
    }
    
    /*
     * recupere les mails de tous les admins pour envoi de mail
     */
    function getMailsAdmins(){
        $mails = array();
        $admins = $this->getAdmins();
        foreach($admins as $admin){
            $mail = $this->getMailUser($admin);
            if($mail != false)
                $mails[$mail] = $mail;
        }
        return $mails;
    }

    function getEtatProjet($donnees){
        $etat = "";

        if(!$donnees['is_active'])
            return "Terminé";

        foreach ($donnees['categories'] as $categorie){
            if(strstr(strtolower($categorie[0]), "stand") ) {
                return "Stand-by";
            }elseif(strstr(strtolower($categorie[0]), "abandonne")) {
                return "Abandonné";
            }else{
                $now = new \DateTime(date("Y-m-d"));
                $startDate = new \DateTime($donnees['start_date']);
                $endDate = new \DateTime($donnees['end_date']);

                if ($donnees['start_date'] != "" and $startDate > $now) {
                    $etat = "Futur";
                }else if ($donnees['end_date'] != "" and $endDate > $now && $donnees['start_date'] != "" and $startDate < $now) {
                    $etat = "En cours";
                }else if ($donnees['end_date'] != "" and $endDate < $now) {
                    $etat = "En retard";
                }else {
                    $etat = "En anomalie";
                }
            }
        }
        return $etat;
    }

    function getEtatExploit($donnees){
        $etat = "";

        if(!$donnees['is_active'])
            return "Terminé";

        foreach ($donnees['categories'] as $categorie){
            if(strstr(strtolower($categorie['name']), "stand") ) {
                return "Stand-by";
            }elseif(strstr(strtolower($categorie['name']), "abandonne")) {
                return "Abandonné";
            }else{
                $now = new \DateTime(date("Y-m-d"));
                $endDate = new \DateTime($donnees['end_date']);

                if ($donnees['end_date'] != "" and $endDate > $now) {
                    $etat = "En cours";
                }else if ($donnees['end_date'] != "" and $endDate < $now) {
                    $etat = "En retard";
                } else {
                    $etat = "En anomalie";
                }
            }
        }
        return $etat;
    }

    function validAllModif($donnees){
        $queryUpdate = "UPDATE valide_projet set valide=1, modifie=0, last_cat='".mysqli_escape_string($this->mysqli,$donnees["etat"])."' WHERE project_id=".$donnees['idProject'];
        $resQueryUpdate = mysqli_query($this->mysqli, $queryUpdate);
        var_dump($queryUpdate);
        if(!$resQueryUpdate)
            $resPost = "la mise à jour de l'activité à echouée.";
    }

}