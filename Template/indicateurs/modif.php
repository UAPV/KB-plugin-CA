<?= $this->asset->js('plugins/Dosi/js/highcharts.js') ?>
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
<?= $this->asset->css('plugins/Dosi/Css/fixedHeader.dataTable.min.css') ?>
<?= $this->asset->css('plugins/Dosi/Css/highchart.css') ?>
<?= $this->asset->css('plugins/Dosi/Css/datatables.filters.css') ?>
<?= $this->asset->css('plugins/Dosi/Css/base.css') ?>

<?php echo "<input id='droits' type='hidden' value='".json_encode($droitValide, true)."'/>";?>
<?php echo "<input id='cptNbProjets' type='hidden' value='".json_encode($cptNbModif, true)."'/>";?>
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
                        <li class="nav-item "><a class="nav-link" href="https://intracri.univ-avignon.fr/index.php/Catalogue_d%27activit%C3%A9s" target="_blank"  title="Lien vers le wiki de la DOSI. (Outil interne DOSI)">
                                <i class="fa fa-question-circle fa-lg" style="color: white;"></i>
                            </a>
                        </li>
                        <li ><a href="/indicateurs/dosi">
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
                        <li class="active"><a href="/indicateurs/dosi/modif">
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


<!-- Projet valide -->
<div class="row" style="text-align: center" id="ancreValid"><h2>Modifications</h2></div>
      <div id="indicateurs" class="row" style="font-size: large;text-align: center; vertical-align: middle">
      <br>
      <div id="autres" style="display: inline-block; text-align: center;padding-right:50px">
            <div class="left">
                  <div class="labelIndic">
                        Nombre
                        <br>
                        <span class="chiffre"><?php echo $cptNbModif; ?></span>
                  </div>
            </div>
            <div style="clear: both;"></div>
      </div>

</div>


<!-- Projet modifié -->
<?php if(count($listeModif) > 0) :?>
    <div class="row">
        <div id="tableauModif" style="margin:20px;">
            <?php if(count($listeModif) == 0): ?>
                Aucun projets n'a été modifié dans le catalogue de projets DOSI.
            <?php else: ?>
                <table class="table table-striped table-bordered dataTable no-footer indicateurs" >
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
                                <select class="form-control input-sm">
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
                                            echo "<option value='-'>".$value."</option>";
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
                    foreach ($listeModif as $key => $value) {
                        echo "<tr id='".$key."'>";
                       // foreach ($value as $value2) {
                            echo "<td>".$value['type']."</td>";
                            echo "<td>";
                            echo $this->url->link(t($value["name"]), 'BoardViewController', 'show', array('project_id' => $key), false, '', '', true);
                            echo " </td>";
                            if(trim($value["name"]) != trim($value["last_name"]))
                                echo " </br></br><b>Ancien : </b>" . $value["last_name"]."</td>";
                            else
                                echo "</td>";

                            echo "<td>" . $value["owner"];
                            if(trim($value["owner"]) != trim($value["last_chef_DOSI"]))
                                echo " </br></br><b>Ancien : </b>" . $value["last_chef_DOSI"]."</td>";
                            else
                                echo "</td>";

                            echo "<td><b>Référent : </b>" . $value["refTech"]."</br><b>Suppléant :</b> " . $value["supTech"];
                            if(trim($value["refTech"]) != trim($value["last_ref_tech"]) && trim($value["supTech"]) == trim($value["last_sup_tech"]))
                                echo " </br></br><b>Ancien référent : </b>" . $value["last_ref_tech"]."</td>";
                            elseif(trim($value["refTech"]) != trim($value["last_ref_tech"]))
                                echo "</br><b>Ancien référent :</b> " . $value["last_ref_tech"]."</br><b>Ancien suppléant :</b> " . $value["last_sup_tech"]."</td>";
                            elseif(trim($value["supTech"]) != trim($value["last_sup_tech"]))
                                echo "</br><b>Ancien suppléant :</b> " . $value["last_sup_tech"]."</td>";
                            else
                                echo "</td>";

                            echo "<td>" . $value["fonctionnel"];
                            if(trim($value["fonctionnel"]) != trim($value["last_fonctionnel"]))
                                echo " </br></br><b>Ancien : </b>" . $value["last_fonctionnel"]."</td>";
                            else
                                echo "</td>";

                            echo "<td>" . $value["etat"];
                            if(trim($value["etat"]) != trim($value["last_cat"]))
                                echo " </br></br><b>Ancien : </b>" . $value["last_cat"]."</td>";
                            else
                                echo "</td>";

                            echo "<td>" . $value["description"];
                            if(trim($value["description"]) != trim($value["last_description"]))
                                echo " </br></br><b>Ancien : </b>" . $value["last_description"]."</td>";
                            else
                                echo "</td>";

                            echo "<td>" . ((isset($value["start_date"])) ? $value["start_date"] : '')."</br></td>";
                            echo "<td>" . ((isset($value["end_date"])) ? $value["end_date"] : '' )."</br></td>";

                            echo "<td>" . ((isset($value["renouvellement"])) ? $value["renouvellement"] : '');

                            if(isset($value["renouvellement"]) && trim($value["renouvellement"]) != trim($value["last_renouvellement"]))
                                echo " </br></br><b>Ancien : </b>" . $value["last_renouvellement"]."</td>";
                            else
                                echo "</td>";




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
                                echo '</br><a class="priorite btn-xs btn-success"  data-toggle="tooltip" data-placement="top" title="Valider la priorité de ce projet."><i class="fa fa-check" style="color: white;"></i></a><input type="hidden" name="ancre" value="modif" /><input type="hidden" name="value" value="'.htmlentities(json_encode($value, true)).'" />';
                                echo "</td>";
                            echo '<td><a class="btn btn-success valid" data-toggle="tooltip" data-placement="top" title="Basculer ce projet dans le catalogue de projet DOSI."><i class="fa fa-check" style="color: white;"></i></a><input type="hidden" name="ancre" value="modif" /><input type="hidden" name="value" value="'.htmlentities(json_encode($value, true)).'" />';
                            echo '</br><a class="btn btn-danger nonValid" style="margin-top: 5px;" data-toggle="tooltip" data-placement="top" title="Basculer ce projet dans le catalogue de projet hors DOSI."><i class="fa fa-close" style="color: white;"></i></a><input type="hidden" name="ancre" value="modif" /><input type="hidden" name="value" value="'.htmlentities(json_encode($value, true)).'" /></td>';
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
<?php endif;?>
