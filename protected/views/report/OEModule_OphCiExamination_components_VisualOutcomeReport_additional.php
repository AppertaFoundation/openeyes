<script>
    OpenEyes.Dash.postUpdate['<?=$report->graphId();?>'] = function(){
        var months = $('#visual-acuity-months').val();
        var type = $('input[name="type"]:checked').val();
        var type_text = type.charAt(0).toUpperCase() + type.slice(1);
        OpenEyes.Dash.reports['<?=$report->graphId();?>'].yAxis[0].setTitle({ text: "Visual acuity " + months + " month" + (months > 1 ? 's' : '') + " after surgery (LogMAR)" });
        OpenEyes.Dash.reports['<?=$report->graphId();?>'].setTitle({ text: "Visual Acuity (" + type_text + ")" });
    }
</script>