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
        <tbody id='DrillDownContent'>
        </tbody>
    </table>
    
</div>
<!--TODO <script src="<?= Yii::app()->assetManager->createUrl('js/analytics/analytics_csv.js')?>"></script> -->

<script type="text/javascript">
    DisplayDrillThroughData(3492971);
    
    // DisplayDrillThroughData(3686611);
    

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

    function DisplayDrillThroughData(){
        /// pull list of ids here

        // loop for each ID
        var iop_plotly_data = <?= CJavaScript::encode(OphCiExamination_Episode_IOPHistory::getDrillthroughIOPDataForEvent(3492971)); ?>
        
        var data_row = "<tr  class='clickable' data-link='/OphCiExamination/default/view/3655754'> <!-- Generated Data -->";
        data_row += "<td  style='vertical-align: center;'>"+iop_plotly_data["event_id"]+"</td> <!-- Event ID -->";
        data_row += "<td style='vertical-align: center;'>"+iop_plotly_data["event_name"]+"</td> <!-- Event type -->";
        data_row += "<td style='vertical-align: center;'>"+iop_plotly_data["eye"]+"</td><!-- Eye -->";
        data_row += " <td style='vertical-align: center;'>"+iop_plotly_data["instrument_name"]+"</td><!-- Instrument -->";
        data_row += "<td style='vertical-align: center;'>"+iop_plotly_data["dilated"]+"</td><!-- Dilated -->";
        data_row += "<td style='vertical-align: center;'>"+iop_plotly_data["reading_values"]+"</td><!-- Value -->";
        data_row += "<td style='vertical-align: center;'>"+iop_plotly_data["comments"]+"</td><!-- Comments -->";
        data_row += "</tr>";

        document.getElementById("DrillDownContent").innerHTML += data_row;
        //endloop
    }
</script>