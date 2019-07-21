<?php $coreAPI = new CoreAPI();
      $operation_API = new OphTrOperationnote_API();
      $cataract = isset($event_list);
      ?>
<div class="analytics-patient-list" style="display: all;" >
    <div class="flex-layout">
        <h3 id="js-list-title">List of Events</h3>
        <button id="js-back-to-chart" class="selected js-plot-display-label" >Back to chart</button>
    </div>
    <h3 id="js-list-title">Examination events</h3>
    <table>
        <colgroup>
            <col style="width: 100px;">
            <col style="width: 100px">
            <col style="width: 200px;">
            <col style="width: 100px;">
            <col style="width: 50px;">
           <?= $cataract?
               ' <col style="width: 350px;">
                 <col style="width: 50px;">
                 <col style="width: 400px;">
                 <col style="width: 100px;">':
                '<col style="width: 450px;">
                 <col style="width: 450px;">';?>
        </colgroup>
        <thead>
        <tr>
        <!-- <?=$cataract? '<th style="vertical-align: center;">Date</th>': '';?> // can be turned off -->
            <th class="drill_down_patient_list text-left" style="vertical-align: center;">Event ID</th><!-- ID -->
            <th class="text-left" style="vertical-align: center;">Event Type</th><!-- Event ID (confusing, I know...) -->
            <th class="text-left" style="vertical-align: center;">Eye (left or right)</th><!-- eye_ID -->
            <th class="text-left" style="vertical-align: center;">Left Instrument</th><!-- Left_Instrument -->
            <th class="text-left" style="vertical-align: center;">Right Instrument</th><!-- Right_Instrument -->
            <th class="text-left" style="vertical-align: center;">Left_Dilated</th><!-- Left_Dilated -->
            <th class="text-left" style="vertical-align: center;">Right_Dilated</th><!-- Right_Dilated -->
            <th class="text-left" style="vertical-align: center;">Left_Comments</th><!-- Left_Comments -->
            <th class="text-left" style="vertical-align: center;">Right_Comments</th><!-- Right_Comments -->
            <th class="text-left" style="vertical-align: center;">Left_Reading</th><!-- Left_Reading -->
            <th class="text-left" style="vertical-align: center;">Right_Reading</th><!-- Left_Reading -->
        </tr>
        </thead>
        <tbody>
            <tr id='find-me' class="clickable" data-link="/OphCiExamination/default/view/3655754">
                <td class="drill_down_patient_list js-csv-data js-csv-hos_num" style="vertical-align: center;">1661782</td>
                <td style="vertical-align: center;">1958-12-08</td>
                <td style="vertical-align: center;">g</td>
                <td style="vertical-align: center;">f</td>
                <td style="vertical-align: center;">e</td>
                <td style="vertical-align: center;">d</td>
                <td style="vertical-align: center;">c</td>
                <td style="vertical-align: center;">b</td>
                <td style="vertical-align: center;">a</td>

            </tr>
        </tbody>
    </table>
    
    <h3 id="js-list-title">Phasing Events</h3>
    <table>
        <colgroup>
            <col style="width: 150px;"><!--1-->
            <col style="width: 150px;"><!--2-->
            <col style="width: 200px;"><!--3-->
            <col style="width: 150px;"><!--4-->
            <col style="width: 150px;"><!--5-->    
            <col style="width: 150px;"><!--6-->    
            <col style="width: 150px;"><!--7-->    
            <col style="width: 150px;"><!--8-->    
            <col style="width: 150px;"><!--9-->    
            <col style="width: 150px;"><!--10-->    
            <col style="width: 150px;"><!--11-->
        </colgroup>
        <thead>
        <tr>
        <th class="drill_down_patient_list text-left" style="vertical-align: center;">Event ID</th><!-- ID -->
            <th class="text-left" style="vertical-align: center;">Event Type</th><!-- Event ID (confusing, I know...) -->
            <th class="text-left" style="vertical-align: center;">Eye (left or right)</th><!-- eye_ID -->
            <th class="text-left" style="vertical-align: center;">Left Instrument</th><!-- Left_Instrument -->
            <th class="text-left" style="vertical-align: center;">Right Instrument</th><!-- Right_Instrument -->
            <th class="text-left" style="vertical-align: center;">Left_Dilated</th><!-- Left_Dilated -->
            <th class="text-left" style="vertical-align: center;">Right_Dilated</th><!-- Right_Dilated -->
            <th class="text-left" style="vertical-align: center;">Left_Comments</th><!-- Left_Comments -->
            <th class="text-left" style="vertical-align: center;">Right_Comments</th><!-- Right_Comments -->
            <th class="text-left" style="vertical-align: center;">Left_Reading</th><!-- Left_Reading -->
            <th class="text-left" style="vertical-align: center;">Right_Reading</th><!-- Left_Reading -->
        </tr>
        </thead>
        <tbody>
            <tr id='find-me' class="clickable" data-link="/OphCiPhasing/default/view/3436242">
                <td style="vertical-align: center;">1</td>
                <td style="vertical-align: center;">2</td>
                <td style="vertical-align: center;">3</td>
                <td style="vertical-align: center;">4</td>
                <td style="vertical-align: center;">5</td>
                <td style="vertical-align: center;">6</td>
                <td style="vertical-align: center;">7</td>
                <td style="vertical-align: center;">8</td>
                <td style="vertical-align: center;">9</td>
                <td style="vertical-align: center;">10</td>
                <td style="vertical-align: center;">11</td>
            </tr>
        </tbody>
    </table>
</div>
<!-- <script src="<?= Yii::app()->assetManager->createUrl('js/analytics/analytics_csv.js')?>"></script> -->
<script type="text/javascript">
    $('.clickable').click(function () {
        var link = $(this).data('link');
        window.location.href = link;
    });
    // to Paitent info
    $('.js-plot-display-label').click(function () {
        $('.analytics-charts').show();
        $('.analytics-patient-list').hide();
        $('.analytics-patient-list-row').hide();
    });
    //back to chart
    $('#js-back-to-chart').click(function () {
        $('.analytics-event-list-row').hide();
        $('.analytics-event-list').hide();
        $(this).hide();
        $('#oescape-layout').show();
    });
</script>