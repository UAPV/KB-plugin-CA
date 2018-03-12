<?= $this->asset->js('plugins/Dosi/js/jquery-ui-datepicker.min.js') ?>
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
<?= $this->asset->js('plugins/Dosi/js/chart-projets.js') ?>
<?= $this->asset->js('plugins/Dosi/Css/jquery-ui-datepicker.min.css') ?>
<?= $this->asset->css('plugins/Dosi/Css/indicateurs.css') ?>
<?= $this->asset->css('plugins/Dosi/Css/fixedHeader.dataTable.min.css') ?>
<?= $this->asset->css('plugins/Dosi/Css/highchart.css') ?>
<?= $this->asset->css('plugins/Dosi/Css/datatables.filters.css') ?>
<?= $this->asset->css('plugins/Dosi/Css/base.css') ?>

<?php echo "<input id='droits' type='hidden' value='".json_encode($droitValide, true)."'/>";?>
<?php echo "<input id='cptNbProjets' type='hidden' value='".json_encode($cptNbProjets-$cptAbandonne-$cptTermine, true)."'/>";?>
<?php echo "<input id='pourcAb' type='hidden' value='".json_encode($cptAbandonne, true)."'/>";?>
<?php echo "<input id='pourcEnCours' type='hidden' value='".json_encode($cptEnCours, true)."'/>";?>
<?php echo "<input id='pourcStandBy' type='hidden' value='".json_encode($cptStandBy, true)."'/>";?>
<?php echo "<input id='pourcTerm' type='hidden' value='".json_encode($cptTermine, true)."'/>";?>
<?php echo "<input id='pourcRetard' type='hidden' value='".json_encode($cptRetard, true)."'/>";?>
<?php echo "<input id='pourcFutur' type='hidden' value='".json_encode($cptFutur, true)."'/>";?>
<?php echo "<input id='pourcSansCat' type='hidden' value='".json_encode($cptSansCat, true)."'/>";?>
<a href="#" title="Haut de page" class="scrollup"><i class="fa fa-arrow-up" style="color: white"></i></a>


<header>
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
                        <li class="active"><a href="/indicateurs/dosi/projets">
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

</header>


<!-- Projet valide -->
<div class="row" style="text-align: center" id="ancreValid"><h2>Projets</h2></div>
      <div id="indicateurs" class="row" style="font-size: large;text-align: center; vertical-align: middle">
          <div style="padding: 20px;" class="col-lg-6">
              <div id="pourcCategories"></div>
          </div>
          <div class="col-lg-6" style="padding-top: 100px;">
              <div class="row">
                  <div class="col-lg-6" >
                      <div id="autres " style="display: inline-block; text-align: center;padding-right:50px">
                          <div class="left" style="width: 110px;height: 110px;border: 4px solid #f6f6f6;font-size: 14px;padding: 5px;padding-top:25px">
                              <div class="labelIndic" id="projetsTerm">
                                  Terminé
                                  <br>
                                  <span class="chiffre"><?php echo $cptTermine; ?></span>
                              </div>
                          </div>
                          <div style="clear: both;"></div>
                      </div>
                  </div>
                  <div class="col-lg-6" >
                      <div id="autres " style="display: inline-block; text-align: center;padding-right:50px">
                          <div class="left" style="width: 110px;height: 110px;border: 4px solid #f6f6f6;font-size: 14px;padding: 5px;padding-top:25px">
                              <div class="labelIndic" id="projetsAband">
                                  Abandonné
                                  <br>
                                  <span class="chiffre"><?php echo $cptAbandonne; ?></span>
                              </div>
                          </div>
                          <div style="clear: both;"></div>
                      </div>
                  </div>
              </div>

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
                              <th >Nom</th>
                              <th>Chef de projet</th>
                              <th>Référent technique DOSI</th>
                              <th>Référent fonctionnel</th>
                              <th>Catégories</th>
                              <th>Description</th>
                              <th>Date de début</th>
                              <th>Date de fin</th>
                              <th id="prioriteHead">Priorité</th>
                              <?php if($droitValide)
                                          echo "<th>Actions</th>";
                              ?>
                        </tr>
                        <tr class="filter">
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
                            echo "<td>";
                            echo $this->url->link(t($value["name"]), 'BoardViewController', 'show', array('project_id' => $key), false, '', '', true);
                            echo " </td>";
                            echo "<td>" . $value["owner"]." </td>";
                            echo "<td><b>Référent : </b>" . $value["refTech"]."</br><b>Suppléant :</b> " . $value["supTech"]."</td>";
                            echo "<td>" . $value["fonctionnel"]."</td>";
                            echo "<td>" . $value["categories"]."</td>";
                            echo "<td>" . $value["description"]."</br></td>";
                            echo "<td>" . $value["start_date"]."</br></td>";
                            echo "<td>" . $value["end_date"]."</br></td>";
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
