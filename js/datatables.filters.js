/**
 * Filter each DataTable columns. The plugin add a simple text input at 
 * the top of each columns.
 *
 * TODO : Add the option to choose the type of input (text, select, 
 * range...)
 *
 *  @summary Add a filter input on each DataTable columns
 *  @name DataTables Filters v0.1
 *  @author [Angelo Boursin] (https://github.com/aboursin/datatables.filters)
 *
 *  @example
 *		$(document).ready(function() {
 *		
 *			// Initialize DataTable and active filter plugin
 *			$('#example').DataTable().filtersOn();
 *
 *    } );
 */

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

$.fn.dataTable.Api.register( 'filtersOn()', function () {
	
	var dataTable = this;
	if(dataTable.context[0] == undefined) {
    	return dataTable;
	}
	var id = $(dataTable.context[0].nTable).attr('id');
	var state = dataTable.state.loaded();

	// Restore filter (only if stateSave)
    if (state) {
    	console.log("stateSave:true > Restoring filters...");
    	dataTable.columns().eq(0).each( function (index) {
        var colSearch = state.columns[index].search;
        if (colSearch.search) {
        	// Restore input value
        	$('#' + id + ' .filter input').get(index).value = colSearch.search;
        }
      });
    }

 	// Filter input event : filter matching column
	$('#' + id +' .filter input, #' + id +' .filter select').each( function (index) {
	    if(!$(this).hasClass("date")) {
            $(this).on('keyup change', function () {
                var valueSansAccent = accentRemove(this.value);
                dataTable.column(index).search(valueSansAccent).draw();
            });
        }
	});

    /********DATE RANGE datatable******************/
    $("#datepicker_from").datepicker({
		dateFormat: 'yy-mm-dd',
        onClose: function(date) {
            minDateFilter = new Date(date).getTime();
            dataTable.draw();
		}

    });


    $("#datepicker_to").datepicker({
		dateFormat: 'yy-mm-dd',
        onClose: function(date) {
            maxDateFilter = new Date(date).getTime();
            dataTable.draw();
        }
    });


    // Date range filter
    minDateFilter = "";
    maxDateFilter = "";

    $.fn.dataTableExt.afnFiltering.push(
        function(oSettings, aData, iDataIndex) {
            if (typeof aData._dateDebut == 'undefined') {
            	indexDateDebut = $("#datepicker_from").parent().index();
                aData._dateDebut = new Date(aData[indexDateDebut]).getTime();
            }

            if (typeof aData._dateFin == 'undefined') {
                indexDateFin = $("#datepicker_from").parent().index();
                aData._dateFin = new Date(aData[indexDateFin]).getTime();
            }


            if (minDateFilter && !isNaN(minDateFilter)) {
                if(isNaN(aData._dateDebut)){
                    return false;
                }
                if (aData._dateDebut < minDateFilter) {
                    return false;
                }
            }

            if (maxDateFilter && !isNaN(maxDateFilter)) {
                if(isNaN(aData._dateFin)){
                    return false;
                }
                if (aData._dateFin > maxDateFilter) {
                    return false;
                }
            }

            return true;
        }
    );

	return dataTable;
	
});

$.fn.dataTable.Api.register( 'filtersClear()', function () {
	
	var dataTable = this;
	var id = $(dataTable.context[0].nTable).attr('id');
	var state = dataTable.state.loaded();
	
	// Clean filters (only if stateSave)
	if (state) {
		console.log("stateSave:true > Clearing filters...");
		$('#' + id +' .filter input, #' + id +' .filter select').each( function (index) {
			// Clear input value
			this.value = '';
			// Clear column filter
			dataTable.column(index).search('');
		});
	
		// Re-draw datatable
  		dataTable.draw();
	}
	
});
