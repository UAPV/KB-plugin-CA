
$(document).ready(function() {
    //ajout du thead pour les table ne l'ayant pas pour que dataTable fonctionne
    $("table tbody tr th").parent().parent().parent().each(function(index){
        if($(this).attr('id') != 'board') {
            $(this).find("tbody tr:first-child a").removeAttr('href')
            theadStr = "<thead><tr>" + $(this).find("tbody tr:first-child").html() + "</tr></thead>";
            $(this).prepend(theadStr);
            //supprime les th or thead
            $(this).find("tbody tr th").parent().remove();
        }
    });


    //ProjectListController
    url = window.location.href;
    if(url.search("ProjectListController") == -1) {
        //ajout du datatable sur tous les table
        $('table:not(".indicateurs"):not("#board")').DataTable({
            "language": {
                "url": "plugins/Dosi/js/French.json"
            },
            responsive: true,
            autoWidth: true,
            "order": [[1, "asc"]],
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
        });
    }else{
        $('table').DataTable({
            "language": {
                "url": "plugins/Dosi/js/French.json"
            },
            responsive: true,
            autoWidth: true,
            "order": [[1, "asc"]],
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            "iDisplayLength": -1
        });
    }
    //enleve la pagination par defaut de kanboard car datatble le gere
    $('div.pagination').hide();

} );




function validateDisable(theform) {
    if (theform.disabletest.value == null || theform.disabletest.value == "") {
        alert("You must select the Test(s) to disable!");
        theform.disabletest.focus();
        return false;
    }

    if (theform.cause.value == null || theform.cause.value == "") {
        alert("You must fill in the Cause field!");
        theform.cause.focus();
        return false;
    }

    if (!(ispositive(theform.duration.value))) {
        alert("Duration must be a positive integer.");
        return false;
    }

    // fall through...
    disableButtons(theform,true);
    theform.submit();
}

function disableButtons(theform,action) {
    if (document.all || document.getElementById) {
        for (i = 0; i < theform.length; i++) {
            var tempobj = theform.elements[i];
            if (tempobj.type.toLowerCase() == "button")
                tempobj.disabled = action;
        }
    }
}

function ispositive(inputVal) {
    inputStr = inputVal.toString();
    for (var i = 0; i < inputStr.length; i++) {
        var oneChar = inputStr.charAt(i)
        if (oneChar < "0" || oneChar > "9")
            return false;
    }
    if (inputVal < 1)
        return false;
    return true;
}