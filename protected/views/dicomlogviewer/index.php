<?php
/**
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

        <div class="row divider">

            <form method="post" action="/audit/search" id="auditList-filter" class="clearfix">

                <input type="hidden" id="previous_site_id" value="<?php echo @$_POST['site_id'] ?>"/>
                <input type="hidden" id="previous_firm_id" value="<?php echo @$_POST['firm_id'] ?>"/>
                <input type="hidden" id="previous_user" value="<?php echo @$_POST['user'] ?>"/>
                <input type="hidden" id="previous_action" value="<?php echo @$_POST['action'] ?>"/>
                <input type="hidden" id="previous_target_type" value="<?php echo @$_POST['target_type'] ?>"/>
                <input type="hidden" id="previous_event_type_id" value="<?php echo @$_POST['event_type_id'] ?>"/>
                <input type="hidden" id="previous_date_from" value="<?php echo @$_POST['date_from'] ?>"/>
                <input type="hidden" id="previous_date_to" value="<?php echo @$_POST['date_to'] ?>"/>
                <input type="hidden" id="previous_hos_num" value="<?php echo @$_POST['hos_num'] ?>"/>

                <?php echo $this->renderPartial('//dicomlogviewer/_filters'); ?>

                <div class="row divider js-no-result" style="display:none">
                    <div class="alert-box issue"><b>No results found</b></div>
                </div>
            </form>
        </div>

        <table class="standard cols-full" id="dicom-file-list">
            <colgroup>
                <col class="cols-1" span="11">
            </colgroup>
            <thead>
            <tr>
                <th>File Name</th>
                <th>Import Date</th>
                <th>Study Date</th>
                <th>Station ID</th>
                <th>Location</th>
                <th>Type</th>
                <th>Patient Number</th>
                <th>Status</th>
                <th>Study Instance ID</th>
                <th>Comment</th>
                <th><i>More</i></th>
            </tr>
            </thead>
            <tbody>

            </tbody>
        </table>

        <i class="spinner" title="Loading..." style="display: none;"></i>


<script type="text/javascript">
    function searchDicomLog(){
        $.ajax({
            'url': '<?php echo Yii::app()->createUrl('DicomLogViewer/search'); ?>',
            'type': 'POST',
            'data': $('#auditList-filter').serialize() + "&YII_CSRF_TOKEN=" + YII_CSRF_TOKEN,
            'beforeSend':function (){
                $('.spinner').show();
            },
            'success': function (data) {
                let files = data;

                $('#previous_site_id').val($('#site_id').val());
                $('#previous_firm_id').val($('#firm_id').val());
                $('#previous_user').val($('#user').val());
                $('#previous_action').val($('#action').val());
                $('#previous_target_type').val($('#target_type').val());
                $('#previous_event_type_id').val($('#event_type_id').val());
                $('#previous_date_from').val($('#date_from').val());
                $('#previous_date_to').val($('#date_to').val());

                $('#dicom-file-list tbody').html();

                if (files.data) {
                    for (let i = 0; i < files.data.length; i++) {
                        let file = files.data[i];
                        let last_slash = file.filename.lastIndexOf("/")+1;
                        file.file_ext = file.filename.slice(-4);
                        file.filename = file.filename.slice(0, last_slash)+" "+file.filename.slice(last_slash,-4);

                        let tr = Mustache.render($('#tr-template').text(), file);
                        $('#dicom-file-list tbody').append(tr);
                    }
                }

                enableButtons();
            },
            'complete': function () {
                $('.spinner').hide();
            }
        });
    }

    $(document).ready(function(){
        $('#dicom-log-search').on('click', searchDicomLog);

        //$('#auditListData').clone().dialog({ close: function( event, ui ) { $( this ).dialog( "destroy" ); } });


        $('#dicom-file-list').on('click', '.more', function(){
            let $tr = $(this).closest('tr');
            let data = {
                file_basename: $tr.data('filename')
            };
            let content = Mustache.render($('#dialog-template').text(), data);
            $(content).dialog({
                width: "40%",
                maxWidth: "700px"
            });
        });
    });
</script>

<script type="text/template" id="dialog-template" style="display: none;">

        <div style="display:none; width:500px;" class="dialogbox" title="More Info">
            <p><b>{{file_basename}}</b></p><!-- basename($log['filename']) -->
            <button onclick="reprocessFile('{{filename}}', this)" style="float:right;margin-bottom: 20px;">Reprocess file</button> <!-- $log['filename'] -->
            <p><b>History</b> <br>
            <table class="standard audit-logs">
                <thead>
                <tr>
                    <th>Status</th>
                    <th>Time Stamp</th>
                    <th>Process Name</th>
                    <th>Process Server ID</th>
                </tr>
                </thead>
                <tbody id="fileWatcherHistoryData">
                </tbody>
            </table>
        </div>
</script>

<script type="text/template" id="tr-template" style="display: none;">
    <tr data-id="{{id}}" filename="{{filename}}" processor_id="{{processor_id}}" status="{{status}}">
        <td>{{filename}}<br>{{file_ext}}</td>
        <td>{{import_datetime}}</td>
        <td>{{study_datetime}}</td>
        <td>{{station_id}}</td>
        <td>{{study_location}}</td>
        <td>{{report_type}}</td>
        <td><a href="/patient/search?term={{patient_number}}">{{patient_number}}</a></td>
        <td>{{status}}</td>
        <td>{{study_instance_id}}</td>
        <td>{{comment}}</td>
        <td class="more"><a class="button small">More</a></td>
    </tr>
</script>
