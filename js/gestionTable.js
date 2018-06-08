

$(document).ready(function() {

    /************** DATATABLE *****************/
    var indexColPriorite = $( "thead tr:first-child" ).children().index($('#prioriteHead'));

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
            "targets": indexColPriorite,
            "createdCell": function (td, cellData, rowData, row, col) {
                if (cellData.search("Haute") != -1) {
                    $(td).css('background-color', 'rgba(255, 82, 70,0.6)')
                }else if (cellData.search("Normal") != -1) {
                    $(td).css('background-color', 'rgba(255, 171, 56, 0.6)')
                }
            }}
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
            "targets": indexColPriorite,
            "createdCell": function (td, cellData, rowData, row, col) {
                if (cellData.search("selected=\"\">Haute") != -1) {
                    $(td).css('background-color', 'rgba(255, 82, 70,0.6)')
                }else if (cellData.search("selected=\"\">Normal") != -1) {
                    $(td).css('background-color', 'rgba(255, 171, 56, 0.6)')
                }
            }}
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

    }).filtersOn();

    tableModif = $('#tableauModif table').DataTable({
        dom: 'Bfrtip',
        "bSortCellsTop": true,
        buttons: [
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
        "columnDefs": columnDefs
    }).filtersOn();

    tableNonValid = $('#tableNonValide table').DataTable({
        dom: 'Bfrtip',
        "bSortCellsTop": true,
        buttons: [
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
        "columnDefs": columnDefs
    }).filtersOn();

    /***************** AJAX *************************************/
    $('.valid').on('click', function() {
        url = $(location).attr('href');

        priorite = $(this).parent().parent().find("select option:selected").val();
        $.ajax({
            method: "POST",
            url: url,
            data: {idProjet: $(this).parent().parent().attr('id'), valide: true, modifie: false, priorite: priorite, value: $(this).parent().find("input[name=value]").val() , ancre: $(this).parent().find("input[name=ancre]").val() }
        }).done(function (data) {
            //location.reload();

        }).fail(function (data) {
        }).complete(function (data) {
        });
    });

    $('.nonValid').on('click', function() {
        url = $(location).attr('href');
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
        url = $(location).attr('href');
        priorite = $(this).parent().parent().find("select option:selected").val();
        $.ajax({
            method: "POST",
            url: url,
            data: {idProjet: $(this).parent().parent().attr('id'), valide: true, modifie: true, priorite: priorite, value: $(this).parent().find("input[name=value]").val() , ancre: $(this).parent().find("input[name=ancre]").val() }
        }).done(function (data) {
            //location.reload();

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



    $('.dt-buttons').addClass('col-sm-6');
    $('.dataTables_filter').addClass('col-sm-6');



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


    $('[data-toggle="tooltip"]').tooltip();

});
