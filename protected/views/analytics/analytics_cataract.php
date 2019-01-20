    <div id="pcr-risk-grid" class="analytics-cataract"></div>
    <div id="cataract-complication-grid" class="analytics-cataract"></div>
    <div id="visual-acuity-grid" class="analytics-cataract"></div>
    <div id="refractive-outcome-grid" class="analytics-cataract"></div>
<div class="analytics-event-list analytics-patient-list" style="display:none;margin-right: 500px; ">
    <div class="flex-layout">
        <h3 id="js-list-title">Event List</h3>
        <button id="js-back-to-chart" class="selected">Back to chart</button>
    </div>
    <table>
        <colgroup>
            <col style="width: 100px;">
        </colgroup>
        <thead>
        <tr>
            <th>Event No</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($event_list as $event) { ?>
            <tr class="analytics-event-list-row clickable" id="<?=$event['event_id']?>" style="display: none;">
                <td><?= $event['event_id']; ?></td>
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
        }
        $('.mdl-cell').css('height','600px');
    });
</script>
