$(document).ready(function(){

    $("input[id*='_complexity_']").on('click', function() {
        let $estimated = $('#Element_OphTrOperationbooking_Operation_total_duration_procs');
        if ($estimated) {
            //updateTotalDuration() declared in protected/widgets/views/ProcedureSelection.php
            if(typeof updateTotalDuration === "function"){
                updateTotalDuration('procs');
            }
        }
    });
});
