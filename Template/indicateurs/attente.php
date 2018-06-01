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
<?php echo "<input id='cptNbProjets' type='hidden' value='".json_encode($cptNbAttente, true)."'/>";?>
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
                        <li class="nav-item "><a class="nav-link" href="https://intracri.univ-avignon.fr/index.php/Catalogue_d%27activit%C3%A9s" target="_blank" >
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
                        <li><a href="/indicateurs/dosi/modif">
                                Modifiés <?php //echo $cptNbActivitesModif; ?>
                            </a>
                        </li>
                        <li class="active"><a href="/indicateurs/dosi/attente">
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
<div class="row" style="text-align: center" id="ancreValid"><h2>En attente</h2></div>
      <div id="indicateurs" class="row" style="font-size: large;text-align: center; vertical-align: middle">
      <br>
      <div id="autres" style="display: inline-block; text-align: center;padding-right:50px">
            <div class="left">
                  <div class="labelIndic">
                        Nombre
                        <br>
                        <span class="chiffre"><?php echo $cptNbAttente; ?></span>
                  </div>
            </div>
            <div style="clear: both;"></div>
      </div>
</div>

<div class="row">
    <div id="tableNonValide" style="margin:20px;">
            <?php if(count($listeNonValide) == 0): ?>
                  Il n'y a pas de projet hors DOSI.
            <?php else: ?>
                  <table class="table table-striped table-bordered dataTable no-footer indicateurs">
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
                                    foreach($categoriesProjet as $value){
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

                        foreach ($listeNonValide as $key => $value) {
                              echo "<tr id='".$key."'>";
                              /*foreach ($value as $value2) {
                                    echo "<td>" . $value2."</td>";
                              }*/
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
                                echo $value["categories"];
                            echo "</td>";
                            echo "<td>" . $value["description"]."</br></td>";
                            echo "<td>" . ((isset($value["start_date"])) ? $value["start_date"] : '')."</br></td>";
                            echo "<td>" . ((isset($value["end_date"])) ? $value["end_date"] : '' )."</br></td>";
                            echo "<td>" . ((isset($value["renouvellement"])) ? $value["renouvellement"] : '' )."</br></td>";
                              if($droitValide) {
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
                                    echo '</br><a class="priorite btn-xs btn-success"  data-toggle="tooltip" data-placement="top" title="Valider la priorité de ce projet."><i class="fa fa-check" style="color: white;"></i></a><input type="hidden" name="ancre" value="hors" /><input type="hidden" name="value" value="'.htmlentities(json_encode($value, true)).'" />';
                                    echo "</td>";echo '<td><a class="btn btn-success valid" data-toggle="tooltip" data-placement="top" title="Basculer ce projet dans le catalogue de projet DOSI."><i class="fa fa-check" style="color: white;"></i></a><input type="hidden" name="ancre" value="hors" /><input type="hidden" name="value" value="' . htmlentities(json_encode($value, true)) . '" /></td>';
                              }else{
                                  echo "<td>";
                                  if($value['priorite'] == "")
                                      echo "<div class='tagPriorite' style='visibility: hidden;height: 0px;'>".$value['priorite']."Tag</div>Normal";
                                  else
                                      echo "<div class='tagPriorite' style='visibility: hidden;height: 0px;'>".$value['priorite']."Tag</div>".$value['priorite'];
                                  echo "</td>";
                              }
                              echo "</tr>";
                        }
                        ?>
                        </tbody>
                  </table>
            <?php endif; ?>
      </div>
</div>