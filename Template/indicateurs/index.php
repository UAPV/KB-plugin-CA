<?= $this->asset->js('plugins/Dosi/js/highcharts.js') ?>
<?= $this->asset->js('plugins/Dosi/js/chart.js') ?>
<?= $this->asset->js('plugins/Dosi/js/dataTables.button.min.js') ?>
<?= $this->asset->js('plugins/Dosi/js/dataTables.fixedHeader.min.js') ?>
<?= $this->asset->js('plugins/Dosi/js/button.print.min.js') ?>
<?= $this->asset->js('plugins/Dosi/js/button.bootstrap.min.js') ?>
<?= $this->asset->js('plugins/Dosi/js/pdfMake.min.js') ?>
<?= $this->asset->js('plugins/Dosi/js/vfs_fonts.min.js') ?>
<?= $this->asset->js('plugins/Dosi/js/button.html5.min.js') ?>
<?= $this->asset->js('plugins/Dosi/js/gestionTable.js') ?>
<?= $this->asset->js('plugins/Dosi/js/datatables.filters.js') ?>
<?= $this->asset->js('plugins/Dosi/js/button.colvis.min.js') ?>
<?= $this->asset->css('plugins/Dosi/Css/indicateurs.css') ?>
<?= $this->asset->css('plugins/Dosi/Css/datatables.filters.css') ?>
<?= $this->asset->css('plugins/Dosi/Css/base.css') ?>

<?php echo "<input id='histogrammeAnnee' type='hidden' value='".json_encode($histogrammeAnnee, true)."'/>";?>
<?php echo "<input id='histogramme' type='hidden' value='".json_encode($histogramme, true)."'/>";?>
<?php echo "<input id='histogrammeName' type='hidden' value='".json_encode($histogrammeName, true)."'/>";?>
<?php echo "<input id='cptNbExploit' type='hidden' value='".json_encode($cptNbExploit, true)."'/>";?>
<?php echo "<input id='cptNbProjetEnCours' type='hidden' value='".json_encode($cptNbProjetEnCours, true)."'/>";?>
<?php echo "<input id='listeRenPerim' type='hidden' value='".json_encode($listeRenPerim, JSON_HEX_APOS)."'/>";?>
<?php echo "<input id='listeStandByPerim' type='hidden' value='".json_encode($listeStandByPerim, JSON_HEX_APOS)."'/>";?>
<?php echo "<input id='listeAnomalie' type='hidden' value='".json_encode($listeAnomalie, JSON_HEX_APOS)."'/>";?>
<a href="#" title="Haut de page" class="scrollup"><i class="fa fa-arrow-up" style="color: white"></i></a>


<div style="margin-left: -15px;padding-left: -15px;margin-right: -15px;">
    <div id="banniere" class="row">
        <div id="header" class="row">
            <div class="row" style='font: 400 4em/1 "source_sans_proregular",Arial,"Helvetica Neue",Helvetica,"Bitstream Vera Sans",sans-serif; color: #ffffff;text-align: center;padding: 0.3em;'>
                <div class="col-md-2"></div>
                <div class="col-md-8">
                    Catalogue d'activités DOSI
                </div>
            </div>
        </div>

        <div id="navbar" class="navbar navbar-inverse">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navHeaderCollapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>

                <div class="collapse navbar-collapse" id="navHeaderCollapse">
                    <ul class="nav navbar-nav">
                        <li class="nav-item "><a class="nav-link" href="https://intracri.univ-avignon.fr/index.php/Catalogue_d%27activit%C3%A9s" target="_blank" title="Lien vers le wiki de la DOSI. (Outil interne DOSI)">
                                <i class="fa fa-question-circle fa-lg" style="color: white;"></i>
                            </a>
                        </li>
                        <li class="active"><a href="/indicateurs/dosi">
                                Accueil
                            </a>
                        </li>
                        <li ><a href="/indicateurs/dosi/projets">
                                Projets
                            </a>
                        </li>
                        <li><a href="/indicateurs/dosi/exploit">
                                Exploitation
                            </a>
                        </li>
                        <li><a href="/indicateurs/dosi/modif">
                                Modifiés <?php //echo $cptNbActivitesModif; ?>
                            </a>
                        </li>
                        <li><a href="/indicateurs/dosi/attente">
                                En attente <?php //echo $cptNbActivitesAttente; ?>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>



</div>
<div id="indicateurs" class="row" style="font-size: large;text-align: center; vertical-align: middle">
    <br>
    <div class="row">
        <div class="panel panel-success" style="padding:0px;">

            <div class="panel-heading">
                <h3 class="panel-title">Activités
                    <div class="panel-title pull-right" title="Une activité peut être soit un projet, soit une exploitation"><i class="fa fa-question-circle fa-lg" style="color: #475C9C;"></i></div>
                </h3>
                </div>

            <div class="panel-body">
                <div class="row" style="display: flex;align-items: center;">
                    <div class="col-lg-3 col-md-12 col-12" >
                        <div id="projetExploit"></div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-12" style="display: inline-block; vertical-align: middle; float: none;" >
                        <div id="autres" style="display: inline-block; text-align: center;padding-right:50px">
                            <div class="left">
                                <a href="/indicateurs/dosi/modif">
                                    <div class="labelIndic">
                                        Modifiée
                                        <br>
                                        <span class="chiffre"><?php echo $cptNbActivitesModif; ?></span>
                                        <br><span title="Une activité modifié doit être à nouveau validé."><i class="fa fa-question-circle fa-md" style="color: #475C9C;"></i></span>
                                    </div>
                                </a>
                            </div>
                            <div style="clear: both;"></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-12" title ="">
                        <div id="autres" style="display: inline-block; text-align: center;padding-right:50px">
                            <div class="left" >
                                <a href="/indicateurs/dosi/attente">
                                    <div class="labelIndic">
                                        En attente
                                        <br>
                                        <span class="chiffre"><?php echo $cptNbActivitesAttente; ?></span>
                                        <br><span title="Une activité est en attente lorsqu'elle n'a jamais été validé"><i class="fa fa-question-circle fa-md" style="color: #475C9C;"></i></span>
                                    </div>
                                </a>
                            </div>
                            <div style="clear: both;"></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-12" >
                        <div id="autres" style="display: inline-block; text-align: center;padding-right:50px">
                            <div class="left">
                                <div class="labelIndic" id="projetAnomalie">
                                    En anomalie
                                    <br>
                                    <span class="chiffre"><?php echo $cptNbActivitesAnomalie; ?></span>
                                    <br><span title="Cas d'anomalie : <br />- Projet n'ayant ni date de debut ni date de fin."><i class="fa fa-question-circle fa-md" style="color: #475C9C;"></i></span>
                                </div>
                            </div>
                            <div style="clear: both;"></div>
                        </div>
                        <br><span style="font-size:12px;">Projets n'ayant ni date de fin ni date de début.</span>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6" >
            <div class="panel panel-danger" style="padding:0px;">
                <div class="panel-heading">
                    <h3 class="panel-title">Projets<div class="panel-title pull-right" title="Un projet doit etre configurer comme ceci dans Kanboard :<br>   - Une date de debut et une date de fin<br>   - categorie : projet<br>   - catégorie en plus possible : stand-by ou abandonné"><i class="fa fa-question-circle fa-lg" style="color: #475C9C;"></i></div>
                    </h3>
                    </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div id="autres" style="display: inline-block; text-align: center;padding-right:50px">
                                <div class="left" >
                                    <div class="labelIndic"  id="projetsStandByPerim">
                                        Stand-by périmé
                                        <br>
                                        <span class="chiffre"><?php echo $cptNbProjetsStandByPerim; ?></span>
                                        <br><span title="Projet en stand-by ayant une date de fin dépassé."><i class="fa fa-question-circle fa-md" style="color: #475C9C;"></i></span>
                                    </div>
                                </div>
                                <div style="clear: both;"></div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div id="autres" style="display: inline-block; text-align: center;padding-right:50px">
                                <div class="left" >
                                    <div class="labelIndic" id="projetsRetard">
                                        En retard
                                        <br>
                                        <span class="chiffre"><?php echo $cptEtatsProjets['En retard']; ?></span>
                                        <br><span title="Projet ayant une date de fin dépassé."><i class="fa fa-question-circle fa-md" style="color: #475C9C;"></i></span>
                                    </div>
                                </div>
                                <div style="clear: both;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6" >
            <div class="panel panel-danger" style="padding:0px;">
                <div class="panel-heading">
                    <h3 class="panel-title">Exploitation<div class="panel-title pull-right" title="Un exploitation doit etre configurer comme ceci dans Kanboard :<br>   - une date de fin<br>   - categorie : AUCUNE !"><i class="fa fa-question-circle fa-lg" style="color: #475C9C;"></i></div>
                    </h3>
                </div>
                <div class="panel-body">
                    <div id="autres" style="display: inline-block; text-align: center;padding-right:50px">
                        <div class="left" style="width:280px;">
                            <div class="labelIndic" id="renPerim">
                                Renouvellement périmé
                                <br>
                                <span class="chiffre"><?php echo $cptEtatsExploit['En retard']; ?></span>
                                <br><span title="Exploitation ayant une date de fin dépassé"><i class="fa fa-question-circle fa-md" style="color: #475C9C;"></i></span>
                            </div>
                        </div>
                        <div style="clear: both;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div style="padding: 20px;" class="col-lg-12">
            <div id="histogrammeGeneral"></div>
        </div>
    </div>

</div>

<div class="row">

    <div id="tableau" style="margin:20px;">
        <?php if(count($liste) == 0): ?>
            Il n'y a pas de projet dans le catalogue d'activité DOSI.
        <?php else: ?>
            <table class="table table-striped table-bordered dataTable no-footer indicateurs" id="tableValid" width="100%">
                <thead>
                <tr>
                    <th >Activité</th>
                    <th >Nom</th>
                    <th>Chef de projet / Responsable d'exploitation</th>
                    <th>Référent technique DOSI</th>
                    <th>Référent fonctionnel</th>
                    <th>Etat</th>
                    <th>Description</th>
                    <th>Date de début</th>
                    <th>Date de fin</th>
                    <th>Renouvellement</th>
                    <th id="prioriteHead">Priorité</th>
                    <?php if($droitValide)
                        echo "<th>Actions</th>";
                    ?>
                </tr>
                <tr class="filter">
                    <td>
                        <select id="filtreType" class="form-control input-sm">
                            <option value=""></option>
                            <option value="Exploitation">Exploitation</option>
                            <option value="Projet">Projet</option>
                        </select>
                    </td>
                    <td><input class="form-control input-sm" type="text"></td>
                    <td><input class="form-control input-sm" type="text"></td>
                    <td><input class="form-control input-sm" type="text"></td>
                    <td><input class="form-control input-sm" type="text"></td>
                    <td><select id="filtreCat" class="form-control input-sm">
                            <option value=""></option>
                            <?php
                            foreach($etats as $value){
                                if($value == "En anomalie")
                                    echo "<option value='En anomalie'>".$value."</option>";
                                else if($value == "Stand-by")
                                    echo "<option value='stand'>".$value."</option>";
                                else if($value == "Récurrent")
                                    echo "<option value='recurrent'>".$value."</option>";
                                else if($value == "Terminé")
                                    echo "<option value='termine'>".$value."</option>";
                                else if($value == "Abandonné")
                                    echo "<option value='abandonne'>".$value."</option>";
                                else
                                    echo "<option value='".$value."'>".$value."</option>";
                            }

                            ?>
                        </select></td>
                    <td><input class="form-control input-sm" type="text" ></td>
                    <td><span id="date-label-from" class="date-label">From: </span><input class="date_range_filter date" type="text" id="datepicker_from" /></td>
                    <td><span id="date-label-to" class="date-label">To:<input class="date_range_filter date" type="text" id="datepicker_to" /></td>
                    <td><input class="form-control input-sm" type="text"></td>
                    <td><select class="form-control input-sm">
                            <option value=""></option>
                            <option value="HauteTag">Haute</option>
                            <option value="NormalTag">Normal</option>
                            <option value="BasseTag">Basse</option></select></td>
                    <?php
                    if($droitValide)
                        echo "<td></td>";
                    ?>
                </tr>

                </thead>
                <tbody>
                <?php
                foreach ($liste as $key => $value) {

                    echo "<tr id='".$key."'>";
                    // foreach ($value as $value2) {
                    echo "<td>".$value['type']."</td>";
                    echo "<td>";
                    echo $this->url->link(t($value["name"]), 'BoardViewController', 'show', array('project_id' => $key), false, '', '', true);
                    echo " </td>";
                    echo "<td>" . $value["owner"]." </td>";
                    echo "<td><b>Référent : </b>" . $value["refTech"]."</br><b>Suppléant :</b> " . $value["supTech"]."</td>";
                    echo "<td>" . $value["fonctionnel"]."</td>";
                    echo "<td>";
                    if($value["categories"] == "-")
                        echo "En anomalie";
                    else
                        echo $value["etat"];
                    echo "</td>";
                    echo "<td>" . $value["description"]."</br></td>";
                    echo "<td>" . ((isset($value["start_date"])) ? $value["start_date"] : '')."</br></td>";
                    echo "<td>" . ((isset($value["end_date"])) ? $value["end_date"] : '' )."</br></td>";
                    echo "<td>" . ((isset($value["renouvellement"])) ? $value["renouvellement"] : '' )."</br></td>";
                    //}
                    if($droitValide){
                        echo "<td style='text-align: center'><div class='tagPriorite' style='visibility: hidden;height: 0px;'>".$value['priorite']."Tag</div><select name=\"select\" style='margin-bottom: 10px'>
                                      <option value=\"Basse\"";
                        if($value['priorite'] == "Basse")
                            echo " selected";
                        echo ">Basse</option>
                                      <option value=\"Normal\" ";
                        if($value['priorite'] == "Normal" or $value['priorite'] == "")
                            echo " selected";
                        echo ">Normal</option>
                                      <option value=\"Haute\"";
                        if($value['priorite'] == "Haute")
                            echo " selected";
                        echo ">Haute</option>
                                    </select>";
                        echo '</br><a class="priorite btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="Valider la priorité de ce projet."><i class="fa fa-check" style="color: white;"></i></a><input type="hidden" name="ancre" value="valide" /><input type="hidden" name="value" value="'.htmlentities(json_encode($value, true)).'" />';
                        echo "</td>";
                        echo '<td><a class="btn btn-danger nonValid" style="margin-top: 5px;" data-toggle="tooltip" data-placement="top" title="Basculer ce projet dans le catalogue de projet hors DOSI."><i class="fa fa-close" style="color: white;"></i></a><input type="hidden" name="ancre" value="valide" /><input type="hidden" name="value" value="'.htmlentities(json_encode($value, true)).'" />';
                        echo '<a class="btn btn-warning modif" style="margin-top: 5px;" data-toggle="tooltip" data-placement="top" title="Basculer ce projet dans les projets modifiés."><i class="fa fa-edit" style="color: white;"></i></a><input type="hidden" name="ancre" value="valide" /><input type="hidden" name="value" value="'.htmlentities(json_encode($value, true)).'" /></td>';
                    }else{
                        echo "<td><div class='tagPriorite' style='visibility: hidden;height: 0px;'>".$value['priorite']."Tag</div>" . $value['priorite']."</td>";
                    }
                    echo "</tr>";
                }

                ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>