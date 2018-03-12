<?= $this->asset->js('plugins/Dosi/js/highcharts.js') ?>
<?= $this->asset->js('plugins/Dosi/js/dataTables.button.min.js') ?>
<?= $this->asset->js('plugins/Dosi/js/dataTables.fixedHeader.min.js') ?>
<?= $this->asset->js('plugins/Dosi/js/button.print.min.js') ?>
<?= $this->asset->js('plugins/Dosi/js/button.bootstrap.min.js') ?>
<?= $this->asset->js('plugins/Dosi/js/pdfMake.min.js') ?>
<?= $this->asset->js('plugins/Dosi/js/vfs_fonts.min.js') ?>
<?= $this->asset->js('plugins/Dosi/js/button.html5.min.js') ?>
<?= $this->asset->js('plugins/Dosi/js/indicateurs.js') ?>
<?= $this->asset->js('plugins/Dosi/js/datatables.filters.js') ?>
<?= $this->asset->js('plugins/Dosi/js/button.colvis.min.js') ?>
<?= $this->asset->css('plugins/Dosi/Css/indicateurs.css') ?>
<?= $this->asset->css('plugins/Dosi/Css/fixedHeader.dataTable.min.css') ?>
<?= $this->asset->css('plugins/Dosi/Css/highchart.css') ?>
<?= $this->asset->css('plugins/Dosi/Css/datatables.filters.css') ?>

<?php echo "<input id='compteurChart' type='hidden' value='".json_encode($pie, true)."'/>";?>
<?php echo "<input id='compteurChartHorsDosi' type='hidden' value='".json_encode($pieHorsDosi, true)."'/>";?>
<?php echo "<input id='ancre' type='hidden' value='".json_encode($ancre, true)."'/>";?>
<?php echo "<input id='droits' type='hidden' value='".json_encode($droitValide, true)."'/>";?>
<?php echo "<input id='pourcRec' type='hidden' value='".json_encode($pourcRec, true)."'/>";?>
<?php echo "<input id='pourcEnCours' type='hidden' value='".json_encode($pourcEnCours, true)."'/>";?>
<?php echo "<input id='pourcAttente' type='hidden' value='".json_encode($pourcAttente, true)."'/>";?>
<?php echo "<input id='pourcStandBy' type='hidden' value='".json_encode($pourcStandBy, true)."'/>";?>
<?php echo "<input id='cptNbProjets' type='hidden' value='".json_encode($cptNbProjets, true)."'/>";?>
<a href="#" title="Haut de page" class="scrollup"><i class="fa fa-arrow-up" style="color: white"></i></a>




      <!--<div id="erreur" class="row col-lg-6 col-lg-offset-3">
      <?php /*if(count($erreur) == 0): ?>

      <?php else: ?>
            <table class="table table-striped table-bordered dataTable no-footer indicateurs">
                  <thead>
                  <tr>
                        <td>Projets</td>
                        <td>Erreur</td>
                  </tr>
                  </thead>
                  <tbody>
                  <?php
                  foreach($erreur as $key => $value){
                        echo "<tr><td>$key</td><td style='color:red;'>";
                        foreach ($value as $key2 => $value2){
                              echo $value2."</br>";
                        }
                        echo "</td></tr>";
                  }
                  ?>
                  </tbody>
            </table>
      <?php endif; */ ?>
</div>-->

<!-- Projet valide -->
<div class="row" style="text-align: center" id="ancreValid"><h2>Catalogue de projets DOSI</h2></div>
      <div id="indicateurs" class="row" style="font-size: large;text-align: center; vertical-align: middle">
      <br>
      <div id="autres" style="display: inline-block; text-align: center;padding-right:50px">
            <div class="left">
                  <div class="labelIndic">
                        Nombre de projets
                        <br>
                        <span class="chiffre"><?php echo $cptNbProjets; ?></span>
                  </div>
            </div>
            <div style="clear: both;"></div>
      </div>
      <br>
      <a href="#ancreHorsDosi">Voir les projets DOSI en attente : hors catalogue de projets DOSI</a>
      <?php
      if(count($listeModif) > 0)
            echo "<br><span style='color: red'>".count($listeModif)." projet(s) modifié </span><a href='#ancreModif'>Voir</a>";
      ?>
</div>
<br>
<div  class="row">
    <div style="padding: 20px;" class="col-lg-3">
        <div id="pourcCategories"></div>
    </div>
    <div style="padding: 20px;" class="col-lg-9">
        <div id="categoriesProjet"></div>
    </div>
</div>
<div class="row" style="text-align: center !important;"><div id="divFiltre" style="visibility: hidden">Filtre : <span id="filtre"></span>   <a href="" id="tsProjets">Voir tous les projets</a> </div></div>

<div class="row">

      <div id="tableau" style="margin:20px;">
            <?php if(count($liste) == 0): ?>
                  Il n'y a pas de projet dans le catalogue de projets DOSI.
            <?php else: ?>
                  <table class="table table-striped table-bordered dataTable no-footer indicateurs" id="tableValid" width="100%">
                        <thead>
                        <tr>
                              <th >Nom</th>
                              <th>Chef de projet </th>
                              <th>Référent technique DOSI</th>
                              <th>Référent fonctionnel</th>
                              <th>Categories</th>
                              <th>Renouvellement</th>
                              <th>Description</th>
                              <th>Priorité</th>
                              <?php if($droitValide)
                                          echo "<th>Actions</th>";
                              ?>
                        </tr>
                        <tr class="filter"><td><input class="form-control input-sm" type="text"></td><td><input class="form-control input-sm" type="text"></td><td><input class="form-control input-sm" type="text"></td><td><input class="form-control input-sm" type="text"></td><td><select class="form-control input-sm"><option value=""></option><option value="Récurrent">Récurrent</option><option value="Terminé">Terminé</option><option value="Abandonné">Abandonné</option><option value="stand">Stand-by</option><option value="-">Sans Categories</option><option value="2016">2016</option><option value="2017">2017</option><option value="2018">2018</option><option value="2019">2019</option><option value="a attribuer">a attribuer</option></select></td><td><input class="form-control input-sm" type="text"></td><td><input class="form-control input-sm" type="text"></td><td><select class="form-control input-sm"><option value=""></option><option value="HauteTag">Haute</option><option value="NormalTag">Normal</option><option value="BasseTag">Basse</option></select></td><td></td></tr>

                        </thead>
                        <tbody>
                        <?php
                        foreach ($liste as $key => $value) {
                            echo "<tr id='".$key."'>";
                            // foreach ($value as $value2) {
                            echo "<td>" . $value["name"]." </td>";
                            echo "<td>" . $value["owner"]." </td>";
                            echo "<td><b>Référent : </b>" . $value["refTech"]."</br><b>Suppléant :</b> " . $value["supTech"]."</td>";
                            echo "<td>" . $value["fonctionnel"]."</td>";
                            echo "<td>" . $value["categories"]."</td>";
                            echo "<td>" . $value["renouvellement"]."</td>";
                            echo "<td>" . $value["description"]."</br></td>";
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


<!-- Projet non valide -->
<br>
<div class="row" style="text-align: center" id="ancreHorsDosi"><h2>Catalogue de projets hors DOSI</h2></div>
<div class="row" >
    <div class="col-sm-2 col-sm-push-4"><a href="#ancreNonValid" class="btn btn-primary">Voir les projets en attente</a></div>
    <div class="col-sm-2 col-sm-push-5"><a href="#ancreModif" class="btn btn-primary">Voir les projets modifiés</a></div>
</div>
<div id="indicateurs" class="row" style="font-size: large;text-align: center; vertical-align: middle">
      <br>
      <div id="autres" style="display: inline-block; text-align: center;padding-right:50px">
            <div class="left">
                  <div class="labelIndic">
                        Nombre de projets <br>
                        <span class="chiffre"><?php echo $cptNbProjetsHorsDosi; ?></span>
                  </div>
            </div>
            <div style="clear: both;"></div>
      </div>
</div>
<div  class="row">
      <div id="categoriesProjetHorsDosi" style="padding: 20px;"></div>
</div>


<!-- Projet modifié -->
<?php if(count($listeModif) > 0) :?>
    <div class="row" style="text-align: center" id="ancreModif"><h2> Projets modifiés </h2><div class="divFiltreHorsDosi" style="visibility: hidden">Filtre : <span class="filtreHorsDosi"></span>   <a href="" class="tsProjetsHorsDosi">Voir tous les projets</a> </div></div>
    <div class="row">
        <div id="tableauModif" style="margin:20px;">
            <?php if(count($listeModif) == 0): ?>
                Aucun projets n'a été modifié dans le catalogue de projets DOSI.
            <?php else: ?>
                <table class="table table-striped table-bordered dataTable no-footer indicateurs" >
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Chef de projet </th>
                            <th>Référent technique DOSI</th>
                            <th>Référent fonctionnel</th>
                            <th>Categories</th>
                            <th>Renouvellement</th>
                            <th>Description</th>
                            <th>Priorité</th>
                            <?php if($droitValide)
                                echo "<th>Actions</th>";
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($listeModif as $key => $value) {
                        echo "<tr id='".$key."'>";
                       // foreach ($value as $value2) {
                            echo "<td>" . $value["name"];
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

                            echo "<td>" . $value["categories"];
                            if(trim($value["categories"]) != trim($value["last_cat"]))
                                echo " </br></br><b>Ancien : </b>" . $value["last_cat"]."</td>";
                            else
                                echo "</td>";

                            echo "<td>" . $value["renouvellement"];
                            if(trim($value["renouvellement"]) != trim($value["last_renouvellement"]))
                                echo " </br></br><b>Ancien : </b>" . $value["last_renouvellement"]."</td>";
                            else
                                echo "</td>";

                            echo "<td>" . $value["description"];
                            if(trim($value["description"]) != trim($value["last_description"]))
                                echo " </br></br><b>Ancien : </b>" . $value["last_description"]."</td>";
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

<div class="row" style="text-align: center" id="ancreNonValid"><h2>Projets en attente</h2></div>
<div class="row" style="text-align: center !important;"><div class="divFiltreHorsDosi" style="visibility: hidden;">Filtre : <span class="filtreHorsDosi"></span>   <a href="" class="tsProjetsHorsDosi">Voir tous les projets</a> </div></div></div>

<div class="row">
    <div id="tableNonValide" style="margin:20px;">
            <?php if(count($listeNonValide) == 0): ?>
                  Il n'y a pas de projet hors DOSI.
            <?php else: ?>
                  <table class="table table-striped table-bordered dataTable no-footer indicateurs">
                        <thead>
                        <tr>
                              <th>Nom</th>
                              <th>Chef de projet </th>
                              <th>Référent technique DOSI</th>
                              <th>Référent fonctionnel</th>
                              <th>Categories</th>
                              <th>Renouvellement</th>
                              <th>Description</th>
                              <th>Priorité</th>
                              <?php if($droitValide)
                                    echo "<th>Actions</th>";
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
                            echo "<td>" . $value["name"]." </td>";
                            echo "<td>" . $value["owner"]." </td>";
                            echo "<td><b>Référent : </b>" . $value["refTech"]."</br><b>Suppléant :</b> " . $value["supTech"]."</td>";
                            echo "<td>" . $value["fonctionnel"]."</td>";
                            echo "<td>" . $value["categories"]."</td>";
                            echo "<td>" . $value["renouvellement"]."</td>";
                            echo "<td>" . $value["description"]."</br></td>";

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