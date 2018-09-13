<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
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
                <th>Study date</th>
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
            'url': '<?php echo Yii::app()->createUrl('//DicomLogViewer/search'); ?>',
            'type': 'POST',
            'data': $('#auditList-filter').serialize() + "&YII_CSRF_TOKEN=" + YII_CSRF_TOKEN,
            'beforeSend':function (){
                $('.spinner').show();
            },
            'success': function (data) {
                let files = JSON.parse(data);

                $('#previous_site_id').val($('#site_id').val());
                $('#previous_firm_id').val($('#firm_id').val());
                $('#previous_user').val($('#user').val());
                $('#previous_action').val($('#action').val());
                $('#previous_target_type').val($('#target_type').val());
                $('#previous_event_type_id').val($('#event_type_id').val());
                $('#previous_date_from').val($('#date_from').val());
                $('#previous_date_to').val($('#date_to').val());

                $('#dicom-file-list tbody').html();
                for(let i = 0; i < files.data.length; i++){
                    let file = files.data[i];
                    let tr = Mustache.render($('#tr-template').text(), file);
                    $('#dicom-file-list tbody').append(tr);
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
        <td>{{filename}}</td>
        <td>{{import_datetime}}</td>
        <td>{{study_date}}</td>
        <td>{{station_id}}</td>
        <td>{{location}}</td>
        <td>{{type}}</td>
        <td>{{hos_num}}</td>
        <td>{{status}}</td>
        <td>{{study_id}}</td>
        <td>{{comment}}</td>
        <td class="more"><a class="button small">More</a></td>
    </tr>
</script>

<?php if (false): ?>
    <div class="box content">
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
            <div id="searchResults"></div>
            <div id="search-loading-msg" class="large-12 column hidden">
                <div class="alert-box">
                    <img src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif'); ?>"
                         class="spinner"/> <strong>Searching, please wait...</strong>
                </div>
            </div>
        </form>
    </div>

    <script type="text/javascript">
        $(function () {

            function searchDicomLog(){
                $.ajax({
                    'url': '<?php echo Yii::app()->createUrl('//DicomLogViewer/search'); ?>',
                    'type': 'POST',
                    'data': $('#auditList-filter').serialize() + "&YII_CSRF_TOKEN=" + YII_CSRF_TOKEN,
                    'beforeSend':{
                        loadingMsg.show();
                    }
                    'success': function (data) {
                        $('#previous_site_id').val($('#site_id').val());
                        $('#previous_firm_id').val($('#firm_id').val());
                        $('#previous_user').val($('#user').val());
                        $('#previous_action').val($('#action').val());
                        $('#previous_target_type').val($('#target_type').val());
                        $('#previous_event_type_id').val($('#event_type_id').val());
                        $('#previous_date_from').val($('#date_from').val());
                        $('#previous_date_to').val($('#date_to').val());

                        var s = data.split('<!-------------------------->');

                        $('#searchResults').html(s[0]);
                        $('.pagination').html(s[1]).show();

                        enableButtons();
                    },
                    'complete': function () {
                        loadingMsg.hide();
                    }
                });
            }

            $('#dicom-log-search').on('click', searchDicomLog);

            /***********/

            var loadingMsg = $('#search-loading-msg');

            handleButton($('#auditList-filter button[type="submit"]'), function (e) {
                loadingMsg.show();
                $('#searchResults').empty();

                $('#page').val(1);

                $.ajax({
                    'url': '<?php echo Yii::app()->createUrl('//DicomLogViewer/search'); ?>',
                    'type': 'POST',
                    'data': $('#auditList-filter').serialize() + "&YII_CSRF_TOKEN=" + YII_CSRF_TOKEN,
                    'success': function (data) {
                        $('#previous_site_id').val($('#site_id').val());
                        $('#previous_firm_id').val($('#firm_id').val());
                        $('#previous_user').val($('#user').val());
                        $('#previous_action').val($('#action').val());
                        $('#previous_target_type').val($('#target_type').val());
                        $('#previous_event_type_id').val($('#event_type_id').val());
                        $('#previous_date_from').val($('#date_from').val());
                        $('#previous_date_to').val($('#date_to').val());

                        var s = data.split('<!-------------------------->');

                        $('#searchResults').html(s[0]);
                        $('.pagination').html(s[1]).show();

                        enableButtons();
                    },
                    'complete': function () {
                        loadingMsg.hide();
                    }
                });

                e.preventDefault();
            });
        });

        $(document).ready(function () {
            $('#auditList-filter button[type="submit"]').click();

            $('#auto_update_toggle').click(function () {
                if ($(this).text().match(/update on/)) {
                    $(this).text('Auto update off');
                    auditLog.run = false;
                } else {
                    $(this).text('Auto update on');
                    auditLog.run = true;
                    auditLog.refresh();
                }
                return false;
            });
        });

        $('#date_from').bind('change', function () {
            $('#date_to').datepicker('option', 'minDate', $('#date_from').datepicker('getDate'));
        });

        $('#date_to').bind('change', function () {
            $('#date_from').datepicker('option', 'maxDate', $('#date_to').datepicker('getDate'));
        });
    </script>
<?php endif; ?>