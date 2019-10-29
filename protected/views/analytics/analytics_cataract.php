<div id="js-hs-chart-analytics-clinical-main">
    <div id="pcr-risk-grid" class="analytics-cataract"></div>
    <div id="cataract-complication-grid" class="analytics-cataract"></div>
    <div id="visual-acuity-grid" class="analytics-cataract"></div>
    <div id="refractive-outcome-grid" class="analytics-cataract"></div>
    <div id="nod-audit-grid" class="analytics-cataract"></div>
    <div id="catprom5-pre-grid" class="analytics-cataract"></div>
    <div id="catprom5-post-grid" class="analytics-cataract"></div>
    <div id="catprom5-grid" class="analytics-cataract"></div>
</div>
<!-- TODO
<script type="text/javascript">
    $('.clickable').click(function () {
        var link = $(this).attr('id');
        window.location.href = '/OphTrOperationnote/default/view/' + link;
    });
    $('#js-back-to-chart').click(function () {
        $('.analytics-event-list-row').hide();
        $('.analytics-event-list').hide();
        $(this).hide();
        $('.analytics-charts').show();
        if ($('#cataract-complication-grid').html()){
            $('#cataract-complication-grid').html("");
            $('#cataract-complication-grid').show();
            OpenEyes.Dash.init('#cataract-complication-grid');
            OpenEyes.Dash.addBespokeReport('/report/ajaxReport?report=CataractComplications&template=analytics', null,10);
        }else if ($('#visual-acuity-grid').html()){
            $('#visual-acuity-grid').html("");
            $('#visual-acuity-grid').show();
            OpenEyes.Dash.init('#visual-acuity-grid');
            OpenEyes.Dash.addBespokeReport('/report/ajaxReport?report=\\OEModule\\OphCiExamination\\components\\VisualOutcome&template=analytics', null, 10);
        }else if ($('#refractive-outcome-grid').html()){
            $('#refractive-outcome-grid').html("");
            $('#refractive-outcome-grid').show();
            OpenEyes.Dash.init('#refractive-outcome-grid');
            OpenEyes.Dash.addBespokeReport('/report/ajaxReport?report=\\OEModule\\OphCiExamination\\components\\RefractiveOutcome&template=analytics&procedures[]=all', null, 10);
        }else if ($('#nod-audit-grid').html()){
            $('#nod-audit-grid').html("");
            $('#nod-audit-grid').show();
            OpenEyes.Dash.init('#nod-audit-grid');
            OpenEyes.Dash.addBespokeReport('/report/ajaxReport?report=NodAudit&template=analytics', null, 10);
        }else if ($('#catprom5-pre-grid').html()){
            $('#catprom5-pre-grid').html("");
            $('#catprom5-pre-grid').show();
            OpenEyes.Dash.init('#catprom5-pre-grid');
            OpenEyes.Dash.addBespokeReport('/report/ajaxReport?report=\\OEModule\\OphOuCaprom5\\components\\Catprom5&template=analytics&catprom5=pre', null, 10);
        }else if ($('#catprom5-post-grid').html()){
            $('#catprom5-post-grid').html("");
            $('#catprom5-post-grid').show();
            OpenEyes.Dash.init('#catprom5-post-grid');
            OpenEyes.Dash.addBespokeReport('/report/ajaxReport?report=\\OEModule\\OphOuCaprom5\\components\\Catprom5&template=analytics&catprom5=post', null, 10);
        }else if ($('#catprom5-grid').html()){
            $('#catprom5-grid').html("");
            $('#catprom5-grid').show();
            OpenEyes.Dash.init('#catprom5-grid');
            OpenEyes.Dash.addBespokeReport('/report/ajaxReport?report=\\OEModule\\OphOuCaprom5\\components\\Catprom5&template=analytics&catprom5=diff', null, 10);
        }
        viewAllDates();
        if ($('#analytics_allsurgeons').val() == 'on'){
            viewAllSurgeons();
        }
    });
</script>
<script src="<?= Yii::app()->assetManager->createUrl('js/analytics/analytics_csv_cataract.js')?>"></script>
-->
