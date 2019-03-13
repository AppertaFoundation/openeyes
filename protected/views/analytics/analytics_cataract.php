<div id="js-hs-chart-analytics-clinical-main">
    <div id="pcr-risk-grid" class="analytics-cataract"></div>
    <div id="cataract-complication-grid" class="analytics-cataract"></div>
    <div id="visual-acuity-grid" class="analytics-cataract"></div>
    <div id="refractive-outcome-grid" class="analytics-cataract"></div>
    <div id="nod-audit-grid" class="analytics-cataract"></div>
</div>
<div class="analytics-event-list analytics-patient-list" style="display:none;">
    <div class="flex-layout">
        <h3 id="js-list-title">Event List</h3>
        <button id="js-back-to-chart" class="selected">Back to chart</button>
    </div>
    <table>
        <thead>
        <tr>
            <th style="text-align: center;vertical-align: center;">Event No</th>
            <th style="text-align: center;vertical-align: center;">Patient Name</th>
            <th style="text-align: center;vertical-align: center;">Procedure</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($event_list as $event) { ?>
            <tr class="analytics-event-list-row clickable" id="<?=$event['event_id']?>" style="display: none;">
                <td style="text-align: center;vertical-align: center;"><?= $event['event_id']; ?></td>
                <td style="text-align: center;vertical-align: center;"><?= $event['patient_name']; ?></td>
                <td style="text-align: center;vertical-align: center;"><?= $event['procedures']; ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<script type="text/javascript">
    $('.clickable').click(function () {
        var link = $(this).attr('id');
        window.location.href = '/OphTrOperationnote/default/view/' + link;
    });
    $('#js-back-to-chart').click(function () {
        $('.analytics-event-list-row').hide();
        $('.analytics-event-list').hide();
        $(this).hide();
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
            OpenEyes.Dash.addBespokeReport('/report/ajaxReport?report=\\OEModule\\OphCiExamination\\components\\RefractiveOutcome&template=analytics', null, 10);
        }else if ($('#nod-audit-grid').html()){
            $('#nod-audit-grid').html("");
            $('#nod-audit-grid').show();
            OpenEyes.Dash.init('#nod-audit-grid');
            OpenEyes.Dash.addBespokeReport('/report/ajaxReport?report=NodAudit&template=analytics', null, 10);
        }
        $('.mdl-cell').css('width','1000px');
        $('.mdl-cell').css('height','600px');
        viewAllDates();
        if ($('#analytics_allsurgeons').val() == 'on'){
            viewAllSurgeons();
        }
    });
</script>
<script src="<?= Yii::app()->assetManager->createUrl('js/analytics/analytics_csv_cataract.js')?>"></script>
