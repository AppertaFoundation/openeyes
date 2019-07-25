<?php $coreAPI = new CoreAPI();
      $operation_API = new OphTrOperationnote_API();
      $cataract = isset($event_list); //TODO
      ?>
<div class="analytics-patient-list" style="display: all;" >
    <div class="flex-layout">
        <h3 id="js-list-title">List of Events</h3>
        <button id="js-back-to-chart" class="selected js-plot-display-label" >Back to chart</button>
    </div>
    <h3 id="js-list-title">Examination events</h3>
    <table>
        <colgroup>
            <col style="width: 100px;"><!-- Event ID -->
            <col style="width: 100px"><!-- Event type -->
            <col style="width: 100px;"><!-- Eye-->
            <col style="width: 100px;"><!-- Instrument -->
            <col style="width: 100px;"><!-- Dilated -->
            <col style="width: 100px;"><!-- Value -->
            <col style="width: 200px;"><!-- Comments -->

           <!-- <?= $cataract?
               ' <col style="width: 350px;">
                 <col style="width: 100px;">':
                '<col style="width: 450px;">
                 <col style="width: 450px;">';?> -->
        </colgroup>
        <thead>
            <tr>
            <!-- <?=$cataract? '<th style="vertical-align: center;">Date</th>': '';?> // TODO can be turned off, this is here so I can remeber-->
                <th class="drill_down_patient_list text-left" style="vertical-align: center;">Event ID</th><!-- Event ID -->
                <th class="text-left" style="vertical-align: center;">Event Type</th><!-- Event type -->
                <th class="text-left" style="vertical-align: center;">Eye (left or right)</th><!-- Eye-->
                <th class="text-left" style="vertical-align: center;">Instrument</th><!-- Instrument -->
                <th class="text-left" style="vertical-align: center;">Dilated</th><!-- Dilated -->
                <th class="text-left" style="vertical-align: center;">Value</th><!-- Value -->
                <th class="text-left" style="vertical-align: center;">Comments</th><!-- Comments -->
            </tr>
        </thead>
        <tbody>
            <tr id='find-me' class="clickable" data-link="/OphCiExamination/default/view/3655754"> 
                <td class="drill_down_patient_list js-csv-data js-csv-hos_num" style="vertical-align: center;">1661782</td><!-- Event ID -->
                <td style="vertical-align: center;">Examination</td><!-- Event type -->
                <td style="vertical-align: center;">Inner</td><!-- Eye -->
                <td style="vertical-align: center;">Clarinet</td><!-- Instrument -->
                <td style="vertical-align: center;">5%</td><!-- Dilated -->
                <td style="vertical-align: center;">9001</td><!-- Value -->
                <td style="vertical-align: center;">DBZ ref here</td><!-- Comments -->
            </tr>
            <tr id='find-me' class="clickable" data-link="/OphCiPhasing/default/view/3436242">
                <td class="drill_down_patient_list js-csv-data js-csv-hos_num" style="vertical-align: center;">3436242</td><!-- Event ID -->
                <td style="vertical-align: center;">Phasing</td><!-- Event type -->
                <td style="vertical-align: center;">left</td><!-- Eye -->
                <td style="vertical-align: center;">Banjo</td><!-- Instrument -->
                <td style="vertical-align: center;">200%</td><!-- Dilated -->
                <td style="vertical-align: center;">5</td><!-- Value -->
                <td style="vertical-align: center;">Wow!</td><!-- Comments -->
            </tr>
        </tbody>
    </table>
    
</div>
<!--TODO <script src="<?= Yii::app()->assetManager->createUrl('js/analytics/analytics_csv.js')?>"></script> -->

<script type="text/javascript">

     var iop_plotly_data = <?= CJavaScript::encode(OphCiExamination_Episode_IOPHistory::getDrillthroughIOPDataForEvent(3492971)); ?>


    // generate links (used for drill through data to event details) based upon the datalink they have
    $('.clickable').click(function () {
        var link = $(this).data('link');
        window.location.href = link;
    });

    // to drill through data from chart
    $('.js-plot-display-label').click(function () {
        $('.analytics-charts').show();
        $('.analytics-patient-list').hide();
        $('.analytics-patient-list-row').hide();
    });
    //back to chart from drill through data
    $('#js-back-to-chart').click(function () {
        $('.analytics-event-list-row').hide();
        $('.analytics-event-list').hide();
        $(this).hide();
        $('#oescape-layout').show();
    });
</script>