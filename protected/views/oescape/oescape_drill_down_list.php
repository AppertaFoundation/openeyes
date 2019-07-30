<?php $coreAPI = new CoreAPI();
      $operation_API = new OphTrOperationnote_API();
      ?>
<div class="analytics-patient-list" style="display: none;" >
    <div class="flex-layout">
        <h3 id="js-list-title">List of Events</h3>
        <button id="js-back-to-chart" class="selected js-plot-display-label" >Back to chart</button>
    </div>
    <table>
        <colgroup>
            <col style="width: 100px;"><!-- Event ID -->
            <col style="width: 100px"><!-- Event type -->
            <col style="width: 100px;"><!-- Eye-->
            <col style="width: 100px;"><!-- Instrument -->
            <col style="width: 100px;"><!-- Dilated -->
            <col style="width: 100px;"><!-- Value -->
            <col style="width: 200px;"><!-- Comments -->
        </colgroup>
        <thead>
            <tr>
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

<script type="text/javascript">
    var iop_plotly_data;

    // to drill through data from chart
    $('.js-plot-display-label').click(function () {
        $('.analytics-patient-list').show();
        $('#js-back-to-chart').show();      
        $('#oescape-layout').hide();
    });
    //back to chart from drill through data
    $('#js-back-to-chart').click(function () {
        $('.analytics-patient-list').hide();
        $('#js-back-to-chart').hide();
        $('#oescape-layout').show();
    });

    //call the initialization for the data here so that it gets called
    InitDrillThroughData();
    
    function DisplayDrillThroughData(id){
        //display relevant content
        $('.event_'+id).show();

        // generate links (used for drill through data to event details) based upon the datalink they have
        $('.clickable').click(function () {
        var link = $('.event_'+id).data('link');
        window.location.href = link;
        });
    }
    function InitDrillThroughData(){
        /// pull list of ids here
        document.getElementById("DrillDownContent").innerHTML = '';
        // loop for each ID
        iop_plotly_data = <?= CJavaScript::encode(OphCiExamination_Episode_IOPHistory::getDrillthroughIOPDataForEvent($this->patient)); ?>

        for (var i = 0; i < iop_plotly_data.length; i++){
            var data_row = "<tr  class='clickable event_rows event_" + iop_plotly_data[i]["event_id"]+"' data-link='/OphCi"+iop_plotly_data[i]["event_name"]+"/default/view/"+iop_plotly_data[i]["event_id"]+"'> <!-- Generated Data -->";
            data_row += "<td  style='vertical-align: center;'>"+iop_plotly_data[i]["event_id"]+"</td> <!-- Event ID -->";
            data_row += "<td style='vertical-align: center;'>"+iop_plotly_data[i]["event_name"]+"</td> <!-- Event type -->";
            data_row += "<td style='vertical-align: center;'>"+iop_plotly_data[i]["eye"]+"</td><!-- Eye -->";
            data_row += " <td style='vertical-align: center;'>"+iop_plotly_data[i]["instrument_name"]+"</td><!-- Instrument -->";
            data_row += "<td style='vertical-align: center;'>"+iop_plotly_data[i]["dilated"]+"</td><!-- Dilated -->";
            data_row += "<td style='vertical-align: center;'>"+iop_plotly_data[i]["reading_value"]+"</td><!-- Value -->";
            data_row += "<td style='vertical-align: center;'>"+iop_plotly_data[i]["comments"]+"</td><!-- Comments -->";
            data_row += "</tr>";

            document.getElementById("DrillDownContent").innerHTML += data_row;
        }
        //endloop
    }
</script>