$(document).ready(function() {
    function filtrePieChart(val){
        //suprime les fitres a coté
        $(".select").each(function(index){
            $(this).removeClass("select");
            $(this).css("border", "4px solid #f6f6f6");
        });

        if(val == 'Stand-by'){
            val = 'stand';
        }
        if(($("#filtreCat option:selected").val() != "" | $("#filtreCat option:selected").val() != undefined) & $("#filtreCat option:selected").val() == val & $('.highcharts-point-select').length !=0){
            val = "";
        }

        $('#filtreCat ').val(val);
        $('#filtreCat').trigger('change');
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
            text: 'Etats des projets : '+JSON.parse($('#cptNbProjets').attr('value'))
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.y}</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>({point.y}): {point.percentage:.1f} %',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                },
                events: {
                    click: function (event) {
                        var noeud = event.target;
                        if($(noeud).attr('x') == undefined & $(noeud).attr('d') == undefined) {
                            noeud = $(event.target).prev();
                            noeud = noeud[0];
                        }
                        if(typeof noeud.point === "undefined"){
                            filtrePieChart(noeud.innerHTML);
                        }else{
                            filtrePieChart(noeud.point.name);
                        }
                    }
                }
            },
            series: {
                animation: false
            }
        },
        series: [{
            name: 'Nombre',
            colorByPoint: true,
            data: [
                {
                    name: 'En cours',
                    y: JSON.parse($('#pourcEnCours').attr('value'))
                },
                {
                    name: 'Stand-by',
                    y: JSON.parse($('#pourcStandBy').attr('value'))
                },
                {
                    name: 'Futur',
                    y: JSON.parse($('#pourcFutur').attr('value'))
                },
                {
                    name: 'En anomalie',
                    y: JSON.parse($('#pourcSansCat').attr('value'))
                },
                {
                    name: 'En retard',
                    y: JSON.parse($('#pourcRetard').attr('value'))
                }]
        }]
    });

    $("table").on( 'init.dt', function ( e, settings ) {
        chart2.reflow();
        /********* Action click filtre *************/
        $('#projetsTerm').on('click', function(){
            $('path.highcharts-point-select').each(function(index){
                $(this).click();
            });
            if($(this).parent().hasClass("select")) {
                $(this).parent().removeClass("select");
                $(this).parent().css("border", "4px solid #f6f6f6");
                $('#filtreCat ').val('');
            }else{
                $(".select").each(function(index){
                    $(this).removeClass("select");
                    $(this).css("border", "4px solid #f6f6f6");
                });
                $(this).parent().addClass("select");
                $(this).parent().css("border", "4px solid black");
                $('#filtreCat ').val('termine');
            }
            $('#filtreCat').trigger('change');
        })

        $('#projetsAband').on('click', function(){
            $('path.highcharts-point-select').each(function(index){
                $(this).trigger("click");
            });
            if($(this).parent().hasClass("select")) {
                $(this).parent().removeClass("select");
                $(this).parent().css("border", "4px solid #f6f6f6");
                $('#filtreCat ').val('');
            }else{
                $(".select").each(function(index){
                    $(this).removeClass("select");
                    $(this).css("border", "4px solid #f6f6f6");
                });
                $(this).parent().addClass("select");
                $(this).parent().css("border", "4px solid black");
                $('#filtreCat ').val('abandonne');
            }
            $('#filtreCat').trigger('change');
        })
    } );




});