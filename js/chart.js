

$(document).ready(function() {
    function  accentRemove (string) {
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

    /******function strstr********/
    function strstr(search, chaine){
        search = "/"+search+"/";
        var myMatch = chaine.search(search);
        if(myMatch != -1)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /********************* cammembert projet en cours / ecploit ********************/
    totalEnCoursExploit = parseInt(JSON.parse($('#cptNbExploit').attr('value')))+parseInt(JSON.parse($('#cptNbProjetEnCours').attr('value')))
    chart = Highcharts.chart('projetExploit', {
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
            text: 'Total : '+totalEnCoursExploit
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
                }
            }
        },
        series: [{
            name: 'Nombre',
            colorByPoint: true,
            data: [
                {
                    name: 'Projet en cours',
                    y: JSON.parse($('#cptNbProjetEnCours').attr('value'))
                },
                {
                    name: 'Exploitation',
                    y: JSON.parse($('#cptNbExploit').attr('value'))
                }]
        }]
    });

    /*********** diagramme générale année *************/
    Highcharts.chart('histogrammeGeneral', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Vision globale des activités passées et futures'
        },
        xAxis: {
            categories: JSON.parse($('#histogrammeAnnee').attr('value'))
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Total fruit consumption'
            },
            stackLabels: {
                enabled: true,
                style: {
                    fontWeight: 'bold',
                    color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                }
            }
        },
        legend: {
            align: 'right',
            x: -30,
            verticalAlign: 'top',
            y: 25,
            floating: true,
            backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
            borderColor: '#CCC',
            borderWidth: 1,
            shadow: false
        },
        tooltip: {
            headerFormat: '<b>{point.x}</b><br/>',
            pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
        },
        plotOptions: {
            column: {
                stacking: 'normal',
                dataLabels: {
                    enabled: true,
                    color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
                },
                events: {
                    click: function (event) {
                        console.log(event);
                        if($(event.target).css('opacity') == 1){
                            $(event.target).parent().children().each(function(index, value){
                                $(value).css('opacity', '1');
                            })
                            var date = new Date(event.point.x);
                            $(event.target).css('opacity', '0.2');
                            $('#filtreRen ').val(date.getFullYear()+'-'+(date.getMonth()+1)+'-'+date.getDate());
                        }else{
                            $(event.target).css('opacity', '1');
                            $('#filtreRen ').val('');
                        }

                        $('#filtreRen').trigger('change');
                    }
                }
            }
        },
        series: JSON.parse($('#histogramme').attr('value'))
    });


    $("table").on( 'init.dt', function ( e, settings ) {
        chart.reflow();
        /********* Action click filtre *************/
        $('#projetsRetard').on('click', function(){
            tableValid.column(1).search('').draw();
            $('#filtreCat ').val('');
            $('#filtreType ').val('');
            if($(this).parent().hasClass("select")) {
                $(this).parent().removeClass("select");
                $(this).parent().css("border", "4px solid #f6f6f6");
                $('#filtreCat ').val('');
                $('#filtreType ').val('');
            }else{
                $(".select").each(function(index){
                    $(this).removeClass("select");
                    $(this).css("border", "4px solid #f6f6f6");
                });
                $(this).parent().addClass("select");
                $(this).parent().css("border", "4px solid black");
                $('#filtreCat ').val('En retard');
                $('#filtreType ').val('Projet');
            }
            $('#filtreCat').trigger('change');
            $('#filtreType').trigger('change');
        });

        $('#projetAnomalie').on('click', function(){
            tableValid.column(1).search('').draw();
            $('#filtreCat ').val('');
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
                $('#filtreCat ').val('En anomalie');
            }
            $('#filtreCat').trigger('change');
        });

        $('#projetsStandByPerim').on('click', function(){

            $('#filtreCat ').val('');
            $('#filtreCat').trigger('change');
            tableValid.column(1).search('').draw();

            if($(this).parent().hasClass("select")) {
                $(this).parent().removeClass("select");
                $(this).parent().css("border", "4px solid #f6f6f6");

                tableValid.column(1).search('').draw();
            }else{
                $(".select").each(function(index){
                    $(this).removeClass("select");
                    $(this).css("border", "4px solid #f6f6f6");
                });
                $(this).parent().addClass("select");
                $(this).parent().css("border", "4px solid black");

                listeStandByPerim = JSON.parse($('#listeStandByPerim').attr('value'));

                RegExp.escape = function(s) {
                    return accentRemove(s).replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
                };
                regexStr = "";
                $.each(listeStandByPerim, function(index, value){
                    if(listeStandByPerim.length == 1 )
                        regexStr += "("+RegExp.escape(value)+")";
                    else if(index==0)
                        regexStr += "("+RegExp.escape(value)+"|";
                    else if(index == listeStandByPerim.length-1)
                        regexStr += RegExp.escape(value)+')';
                    else
                        regexStr += RegExp.escape(value)+'|';
                });
                console.log(listeStandByPerim);
                console.log(regexStr);
                tableValid.column(1).search(regexStr, true, false).draw();
            }
        });

        $('#renPerim').on('click', function(){

            $('#filtreCat ').val('');
            $('#filtreCat').trigger('change');
            tableValid.column(1).search('').draw();
            if($(this).parent().hasClass("select")) {
                $(this).parent().removeClass("select");
                $(this).parent().css("border", "4px solid #f6f6f6");

                tableValid.column(1).search('').draw();
            }else{
                $(".select").each(function(index){
                    $(this).removeClass("select");
                    $(this).css("border", "4px solid #f6f6f6");
                });
                $(this).parent().addClass("select");
                $(this).parent().css("border", "4px solid black");

                listeRenPerim = JSON.parse($('#listeRenPerim').attr('value'));

                RegExp.escape = function(s) {
                    return accentRemove(s).replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
                };
                regexStr = "";
                $.each(listeRenPerim, function(index, value){

                    if(listeRenPerim.length==1)
                        regexStr += RegExp.escape(value);
                    else if(index==0)
                        regexStr += "("+RegExp.escape(value)+"|";
                    else if(index == listeRenPerim.length-1)
                        regexStr += RegExp.escape(value)+')';
                    else
                        regexStr += RegExp.escape(value)+'|';
                });
                tableValid.column(1).search(regexStr, true, false).draw();
            }
        });

    } );



});
