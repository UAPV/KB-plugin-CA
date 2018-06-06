
$(document).ready(function() {

    //tooltip
    $(document).tooltip({
        content: function() {
            return $(this).attr('title');
        }
    });

} );