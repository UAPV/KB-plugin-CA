

$(document).ready(function() {
    if($('#pourcCategories').width() != $('#categoriesProjet').width()) {
        $('#pourcCategories').css("max-width", $('#pourcCategories').width());
        $('#categoriesProjet').css("max-width", $('#categoriesProjet').width());
        $('#categoriesProjetHorsDosi').css("max-width", $('#categoriesProjetHorsDosi').width());
    }else{
        pourcentage = 20 * $('#pourcCategories').width() / 100;
        pourcentage = 20 * $('#pourcCategories').width() / 100;
        $('#pourcCategories').css("max-width", pourcentage);
        $('#categoriesProjet').css("max-width", $('#categoriesProjet').width() - pourcentage - 40);
        $('#categoriesProjetHorsDosi').css("max-width", $('#categoriesProjetHorsDosi').width());

    }

    /************** menu fixe *****************/
    /*menu fixe*/
    /*$(function(){
        $(window).scroll(function () {
            //Au scroll dans la fenetre on déclenche la fonction
            if ($(this).scrollTop() > 150) {
                //si on a défilé de plus de 150px du haut vers le bas
                $('.navbar').addClass("navbarFixed");
            } else if($(this).scrollTop() < 100) {
                $('.navbar').removeClass("navbarFixed");
            }
        });
    });

    /* bouton scroll haut */
    /*$('#cRetour').click(function() {
        $('html,body').animate({scrollTop: 0}, 'slow');
    });

    $(window).scroll(function(){
        if($(window).scrollTop()<500){
            $('#cRetour').addClass("cInvisible");
            $('#cRetour').removeClass("cVisible");
        }else{
            $('#cRetour').addClass("cVisible");
            $('#cRetour').removeClass("cInvisible");
        }
    });
    /************** DATATABLE *****************/

    $.fn.dataTableExt.ofnSearch['string'] = function ( data ) {
        return ! data ?
            '' :
            typeof data === 'string' ?
                data
                    .replace( /\n/g, ' ' )
                    .replace( /á/g, 'a' )
                    .replace( /é/g, 'e' )
                    .replace( /í/g, 'i' )
                    .replace( /ó/g, 'o' )
                    .replace( /ú/g, 'u' )
                    .replace( /ê/g, 'e' )
                    .replace( /î/g, 'i' )
                    .replace( /ô/g, 'o' )
                    .replace( /è/g, 'e' )
                    .replace( /ï/g, 'i' )
                    .replace( /ü/g, 'u' )
                    .replace( /ç/g, 'c' ) :
                data;
    };

    compteurChart = JSON.parse($('#compteurChart').attr('value'));
    categoriesProjet = [];
    categoriesProjetKey = [];
    for (i = 0; i < compteurChart.length; i++) {
        categoriesProjet[i] = compteurChart[i][0];
        if(compteurChart[i][0].trim() == "Sans Catégories") {
            categoriesProjetKey[i] = "-";
        }
        else if (compteurChart[i][0].trim() == "Stand-by"){
            categoriesProjetKey[i] = "stand";
        }else
        {
            categoriesProjetKey[i] = compteurChart[i][0];
        }
    }

    if($('#droits').attr('value') == "false") {
        tableColumns = [
            {"sWidth": "15%", "aTargets": [0]},
            {"sWidth": "15%", "aTargets": [0]},
            {"sWidth": "15%", "aTargets": [0]},
            {"sWidth": "15%", "aTargets": [0]},
            {"sWidth": "15%", "aTargets": [0]},
            {"sWidth": "10%", "aTargets": [0]},
            {"sWidth": "10%", "aTargets": [0]},
            {"sWidth": "5%", "aTargets": [0]}
        ];

        columnDefs = [{
            "targets": 7,
            "createdCell": function (td, cellData, rowData, row, col) {
                if (cellData.search("Haute") != -1) {
                    $(td).css('background-color', 'rgba(255, 82, 70,0.6)')
                }else if (cellData.search("Normal") != -1) {
                    $(td).css('background-color', 'rgba(255, 171, 56, 0.6)')
                }
            }},
            {"targets": [7], "searchcategories": "select", "searchKey" : ['HauteTag', 'NormalTag', 'BasseTag'], "searchValue" : ["Haute", "Normal", "Basse"]},
            {"targets": [4], "searchcategories": "select", "searchKey" : categoriesProjetKey, "searchValue" : categoriesProjet}
        ];


    }else{
        tableColumns = [
            {"sWidth": "15%", "aTargets": [0]},
            {"sWidth": "15%", "aTargets": [0]},
            {"sWidth": "15%", "aTargets": [0]},
            {"sWidth": "15%", "aTargets": [0]},
            {"sWidth": "5%", "aTargets": [0]},
            {"sWidth": "15%", "aTargets": [0]},
            {"sWidth": "10%", "aTargets": [0]},
            {"sWidth": "5%", "aTargets": [0]},
            {"sWidth": "5%", "aTargets": [0]}
        ];

        columnDefs = [{
            "targets": 7,
            "createdCell": function (td, cellData, rowData, row, col) {
                if (cellData.search("selected=\"\">Haute") != -1) {
                    $(td).css('background-color', 'rgba(255, 82, 70,0.6)')
                }else if (cellData.search("selected=\"\">Normal") != -1) {
                    $(td).css('background-color', 'rgba(255, 171, 56, 0.6)')
                }
            }},
            {"targets": [8], "searchable": false},
            {"targets": [7], "searchcategories": "select", "searchKey" : ['HauteTag', 'NormalTag', 'BasseTag'], "searchValue" : ["Haute", "Normal", "Basse"]},
            {"targets": [4], "searchcategories": "select", "searchKey" : categoriesProjetKey, "searchValue" : categoriesProjet}
        ];
    }



    tableValid = $('#tableau table').DataTable({
        dom: 'Bfrtip',
        "bSortCellsTop": true,
        buttons: [
            'print',
            'pdfHtml5',
            'columnsToggle'
        ],
        "language": {
            "url": "plugins/Dosi/js/French.json"
        },
        "paging": false,
        "bAutoWidth": true,
        fixedHeader: {
            header: true
        },
        "columnDefs": columnDefs,
        fnInitComplete: function() {
            searchInit(this,$("#tableValid_filter input").val());
        }
    }).filtersOn();



     searchInit = function ($datatable, $input) {

        if ($input.length === 0) {
            return false;
        }
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            var val = accentRemove($input.toLowerCase());
            for (var i = 0; i < data.length; i++) {
                var string = accentRemove($('<div/>').text(data[i]).text().toLowerCase());

                if (string.indexOf(val) !== -1) {
                    return true;
                }
            }

            return false;
        });
    }

     accentRemove = function (string) {
        return string
            .replace(/έ/g, 'ε')
            .replace(/[ύϋΰ]/g, 'υ')
            .replace(/ό/g, 'ο')
            .replace(/ώ/g, 'ω')
            .replace(/ά/g, 'α')
            .replace(/[ίϊΐ]/g, 'ι')
            .replace(/ή/g, 'η')
            .replace(/\n/g, ' ')
            .replace(/á/g, 'a')
            .replace(/é/g, 'e')
            .replace(/í/g, 'i')
            .replace(/ó/g, 'o')
            .replace(/ú/g, 'u')
            .replace(/ê/g, 'e')
            .replace(/î/g, 'i')
            .replace(/ô/g, 'o')
            .replace(/è/g, 'e')
            .replace(/ï/g, 'i')
            .replace(/ü/g, 'u')
            .replace(/ã/g, 'a')
            .replace(/õ/g, 'o')
            .replace(/ç/g, 'c')
            .replace(/ì/g, 'i');
    };

    $("#tableValid_filter input").keyup(function(){
        searchInit(tableValid, $(this).val());
    });

    tableModif = $('#tableauModif table').DataTable({
        "language": {
            "url": "plugins/Dosi/js/French.json"
        },
        "paging": false,
        "bAutoWidth": true,
        fixedHeader: {
            header: true
        },
        "columnDefs": columnDefs
    }).filtersOn();

    tableNonValid = $('#tableNonValide table').DataTable({
        "language": {
            "url": "plugins/Dosi/js/French.json"
        },
        "paging": false,
        "bAutoWidth": true,
        fixedHeader: {
            header: true
        },
        "columnDefs": columnDefs
    }).filtersOn();

    /***************** AJAX *************************************/
    $('.valid').on('click', function() {
        if($(location).attr('href').search("-test") == -1){
            url = "https://projets.univ-avignon.fr/?plugin=Dosi&controller=IndicateursController&action=index";
        }else{
            url = "https://projets-test.univ-avignon.fr/?plugin=Dosi&controller=IndicateursController&action=index";
        }
        priorite = $(this).parent().parent().find("select option:selected").val();
        $.ajax({
            method: "POST",
            url: url,
            data: {idProjet: $(this).parent().parent().attr('id'), valide: true, modifie: false, priorite: priorite, value: $(this).parent().find("input[name=value]").val() , ancre: $(this).parent().find("input[name=ancre]").val() }
        }).done(function (data) {
            location.reload();

        }).fail(function (data) {

        }).complete(function (data) {

        });
    });

    $('.nonValid').on('click', function() {
        if($(location).attr('href').search("-test") == -1){
            url = "https://projets.univ-avignon.fr/?plugin=Dosi&controller=IndicateursController&action=index";
        }else{
            url = "https://projets-test.univ-avignon.fr/?plugin=Dosi&controller=IndicateursController&action=index";
        }
        priorite = $(this).parent().parent().find("select option:selected").val();
        $.ajax({
            method: "POST",
            url: url,
            data: {idProjet: $(this).parent().parent().attr('id'), valide: false, modifie: false, priorite: priorite, value: $(this).parent().find("input[name=value]").val() , ancre: $(this).parent().find("input[name=ancre]").val() }
        }).done(function (data) {
            location.reload();

        }).fail(function (data) {

        }).complete(function (data) {

        });
    });

    $('.modif').on('click', function() {
        if($(location).attr('href').search("-test") == -1){
            url = "https://projets.univ-avignon.fr/?plugin=Dosi&controller=IndicateursController&action=index";
        }else{
            url = "https://projets-test.univ-avignon.fr/?plugin=Dosi&controller=IndicateursController&action=index";
        }
        priorite = $(this).parent().parent().find("select option:selected").val();
        $.ajax({
            method: "POST",
            url: url,
            data: {idProjet: $(this).parent().parent().attr('id'), valide: true, modifie: true, priorite: priorite, value: $(this).parent().find("input[name=value]").val() , ancre: $(this).parent().find("input[name=ancre]").val() }
        }).done(function (data) {
            location.reload();

        }).fail(function (data) {

        }).complete(function (data) {

        });
    });

    $('.priorite').on('click', function() {
        if($(location).attr('href').search("-test") == -1){
            url = "https://projets.univ-avignon.fr/?plugin=Dosi&controller=IndicateursController&action=modifPriorite";
        }else{
            url = "https://projets-test.univ-avignon.fr/?plugin=Dosi&controller=IndicateursController&action=modifPriorite";
        }
        priorite = $(this).parent().parent().find("select option:selected").val();
        $.ajax({
            method: "POST",
            url: url,
            data: {idProjet: $(this).parent().parent().attr('id'), priorite: priorite, value: $(this).parent().find("input[name=value]").val() , ancre: $(this).parent().find("input[name=ancre]").val() }
        }).done(function (data) {
            location.reload();

        }).fail(function (data) {

        }).complete(function (data) {

        });
    });

    /************* Diagramme type projet catalogue DOSI *****************/
    chart = Highcharts.chart('categoriesProjet', {
        chart: {
            type: 'column'
        },
        position: {
            spacingLeft: 0,
            marginRight: 0
        },
        style: {
            width: '100%'
        },
        title: {
            text: 'Nombre de projets par categories'
        },
        xAxis: {
            type: 'category',
            labels: {
                rotation: -45,
                style: {
                    fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Nombre de projets'
            }
        },
        legend: {
            enabled: false
        },
        tooltip: {
            pointFormatter: function () {
                var mytotal = 0;
                for (i = 0; i < chart.series.length; i++) {
                    if (chart.series[i].visible) {
                        for (j = 0; j < chart.series[i].yData.length; j++) {
                            mytotal += parseInt(chart.series[i].yData[j]);
                        }
                    }
                }
                var pcnt = (this.y / mytotal) * 100;
                return 'Projets : <b>'+this.y+'</b><br>Représente <b>'+Highcharts.numberFormat(pcnt) + '%</b>';
            },
            //pointFormat: 'Projets : <b>{point.y:.1f}</b>'
        },
        series: [{
            name: 'Projet',
            data: JSON.parse($('#compteurChart').attr('value')) ,
            dataLabels: {
                enabled: true,
                rotation: -90,
                color: '#FFFFFF',
                align: 'right',
                //format: '{point.y:.1f}', // one decimal
                y: 10, // 10 pixels down from the top
                style: {
                    fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif'
                },
                formatter: function () {
                    var mychart = $('#categoriesProjet').highcharts();
                    var mytotal = 0;
                    for (i = 0; i < mychart.series.length; i++) {
                        if (mychart.series[i].visible) {
                            for (j = 0; j < mychart.series[i].yData.length; j++) {
                                mytotal += parseInt(mychart.series[i].yData[j]);
                            }
                        }
                    }
                    var pcnt = (this.y / mytotal) * 100;
                    return Highcharts.numberFormat(pcnt) + '%';
                }

            }
        }]
    });

    $('.dt-buttons').addClass('col-sm-6');
    $('.dataTables_filter').addClass('col-sm-6');

    //chart.setSize( $('#categoriesProjet').width(), $('#categoriesProjet').height());

    /******************** Gestion des filtre avec highchart ************************/
        //ajout filtre sur le tableau en cliquant sur le shema
    var cpt = 1;
    $.each($('#categoriesProjet .highcharts-xaxis-labels').children().find('tspan'), function(index, value){
        var filtreText = $(this).text();
        if(filtreText == "Sans categories"){
            filtreText = '-';
        }

        $("#categoriesProjet rect.highcharts-point.highcharts-color-0:nth-child("+cpt+")").on('click', function(){
            tableValid.column( 4 ).search( accentRemove(filtreText) ).draw();
            $("#filtre").html(filtreText);
            $("#divFiltre").attr("style" ,"visibility: visible");
        });
        $("#categoriesProjet .highcharts-xaxis-labels text:nth-child("+cpt+")").on('click', function(){
            tableValid.column( 4 ).search( accentRemove(filtreText) ).draw();
            $("#filtre").html(filtreText);
            $("#divFiltre").attr("style" ,"visibility: visible");
        });

        cpt += 1;
    });

    $("#tsProjets").on("click", function(e) {
        e.preventDefault();
        $("#divFiltre").attr("style" ,"visibility: hidden");
        $("#filtre").html("");
        tableValid.column( 4 ).search( "" ).draw();
        return false;
    });


    /************* Diagramme type projet hors catalogue DOSI *****************/
    Highcharts.chart('categoriesProjetHorsDosi', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Nombre de projets par categories'
        },
        xAxis: {
            type: 'category',
            labels: {
                rotation: -45,
                style: {
                    fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Nombre de projets'
            }
        },
        legend: {
            enabled: false
        },
        tooltip: {
            pointFormat: 'Projets : <b>{point.y:.1f}</b>'
        },
        series: [{
            name: 'Projet',
            data: JSON.parse($('#compteurChartHorsDosi').attr('value')) ,
            dataLabels: {
                enabled: true,
                rotation: -90,
                color: '#FFFFFF',
                align: 'right',
                format: '{point.y:.1f}', // one decimal
                y: 10, // 10 pixels down from the top
                style: {
                    fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        }]
    });

    /******************** Gestion des filtre avec highchart ************************/
        //ajout filtre sur le tableau en cliquant sur le shema
    var cpt = 1;
    $.each($('#categoriesProjetHorsDosi .highcharts-xaxis-labels').children().find('tspan'), function(index, value){
        var filtreText = $(this).text();
        if(filtreText == "Sans categories"){
            filtreText = '-';
        }

        $("#categoriesProjetHorsDosi rect.highcharts-point.highcharts-color-0:nth-child("+cpt+")").on('click', function(){
            tableNonValid.column( 4 ).search( filtreText ).draw();
            tableModif.column( 4 ).search( filtreText ).draw();
            $(".filtreHorsDosi").html(filtreText);
            $(".divFiltreHorsDosi").attr("style" ,"visibility: visible");
        });
        $("#categoriesProjetHorsDosi .highcharts-xaxis-labels text:nth-child("+cpt+")").on('click', function(){
            tableNonValid.column( 4 ).search( filtreText ).draw();
            tableModif.column( 4 ).search( filtreText ).draw();
            $(".filtreHorsDosi").html(filtreText);
            $(".divFiltreHorsDosi").attr("style" ,"visibility: visible");
        });

        cpt += 1;
    });

    $(".tsProjetsHorsDosi").on("click", function(e) {
        e.preventDefault();
        $(".divFiltreHorsDosi").attr("style" ,"visibility: hidden");
        $(".filtreHorsDosi").html("");
        tableNonValid.column( 4 ).search( "" ).draw();
        tableModif.column( 4 ).search( "" ).draw();
        return false;
    });

    /*************** SCROLL PAGE *****************/
    ScrollToTop=function() {
        var s = $(window).scrollTop();
        if (s > 250) {
            $('.scrollup').fadeIn();
        } else {
            $('.scrollup').fadeOut();
        }

        $('.scrollup').click(function () {
            $("html, body").animate({ scrollTop: 0 }, 500);
            return false;
        });
    }

    StopAnimation=function() {
        $("html, body").bind("scroll mousedown DOMMouseScroll mousewheel keyup", function(){
            $('html, body').stop();
        });
    }


    $(window).scroll(function() {
        ScrollToTop();
        StopAnimation();
    });


    /***************** ANCRE ****************/
    if(JSON.parse($('#ancre').attr('value')) == "valid"){
        $('html,body').animate({scrollTop: $("#tableValid").offset().top}, 'slow');
    }else if(JSON.parse($('#ancre').attr('value')) == "modif"){
        $('html,body').animate({scrollTop: $("#tableauModif").offset().top}, 'slow');
    }if(JSON.parse($('#ancre').attr('value')) == "hors"){
        $('html,body').animate({scrollTop: $("#tableNonValide").offset().top}, 'slow');
    }

    /********************* Higthchart ********************/
    chart2 = Highcharts.chart('pourcCategories', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        position: {
            spacingLeft: 0,
            marginRight: 0
        },
        style: {
            width: '100%'
        },
        title: {
            text: 'Etats des projets <br><span style="font-size: 10px">(pris en compte : Récurrents, Dates, Projets en attente)</span>'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                }
            }
        },
        series: [{
            name: 'Pourcentage',
            colorByPoint: true,
            data: [{
                name: 'Récurrents',
                y: JSON.parse($('#pourcRec').attr('value'))
            }, {
                name: 'En cours',
                y: JSON.parse($('#pourcEnCours').attr('value')),
                sliced: true,
                selected: true
            }, {
                name: 'En attente',
                y: JSON.parse($('#pourcAttente').attr('value'))
            }, {
                name: 'Stand-by',
                y: JSON.parse($('#pourcStandBy').attr('value'))
            }]
        }]
    });

    //chart2.setSize( $('#pourcCategories').width(), $('#pourcCategories').height());

    $('[data-toggle="tooltip"]').tooltip();
});
