<div class="row divider">

    <form method="post" action="/audit/search" id="auditList-filter" class="clearfix">

        <input type="hidden" id="previous_hos_num" value="<?php echo @$_POST['hos_num'] ?>"/>

        <?php echo $this->renderPartial('/admin/_filters', array("channels" => $channels)); ?>

        <div class="row divider js-no-result" style="display:none">
            <div class="alert-box issue"><b>No results found</b></div>
        </div>

        <div class="row divider js-no-date" style="display:none">
            <div class="alert-box issue"><b>Please select To and From Dates</b></div>
        </div>

        <div class="row divider js-no-channel" style="display:none">
            <div class="alert-box issue"><b>Please select a Channel</b></div>
        </div>
    </form>
</div>

<table class="standard cols-full" id="mirth-file-list">
    <colgroup>
        <col class="cols-1" span="5">
    </colgroup>
    <thead>
    <tr>
        <th>Id</th>
        <th>Message type</th>
        <th>Received Date</th>
        <th>Status</th>
        <th>Error text</th>
    </tr>
    </thead>
    <tbody>

    </tbody>
</table>
<div class="pagination"></div>

<i class="spinner" title="Loading..." style="display: none;"></i>

<script type="text/javascript">
    function searchMirthLog(){
        if($('#channel').val() === "") {
            $('.js-no-channel').show();
            $('.js-no-date').hide();
            $('.js-no-result').hide();
            $('#mirth-file-list tbody').empty();
        } else if($('#dateFrom').val() === "" || $('#dateTo').val() === "") {
            $('.js-no-channel').hide();
            $('.js-no-date').show();
            $('.js-no-result').hide();
            $('#mirth-file-list tbody').empty();
        } else {
            $('.js-no-channel').hide();
            $('.js-no-date').hide();
            $.ajax({
                'url': '<?php echo Yii::app()->createUrl('Mirth/admin/search'); ?>',
                'type': 'POST',
                'data': $('#auditList-filter').serialize() + "&YII_CSRF_TOKEN=" + YII_CSRF_TOKEN,
                'beforeSend':function (){
                    $('.spinner').show();
                },
                'success': function (data) {
                    let files = data;

                    $('#previous_hos_num').val($('#hos_num').val());

                    $('#mirth-file-list tbody').empty();

                    if (files.data !== null) {
                        for (let i = 0; i < files.data.length; i++) {
                            let file = files.data[i];

                            if(file['TYPE'] === false) {
                                file['TYPE'] = "";
                            }

                            if(file['ERROR_STRING'] !== "Success") {
                                file['style'] = ' style\=\"color\:red\"';
                            } else {
                                file['style'] = '';
                            }

                            file["CHANNEL_ID"] = $('#channel').val();
                            file["FILTER"] = $('#filter').is(':checked');
                            file["HOS_NUM"] = $('#hos_num').val();
                            file["FROM_DATE"] = $('#dateFrom').val();
                            file["TO_DATE"] = $('#dateTo').val();

                            let tr = Mustache.render($('#tr-mirth-template').text(), file);
                            $('#mirth-file-list tbody').append(tr);
                        }
                        $('.js-no-result').hide();
                    } else {
                        $('.js-no-result').show();
                    }

                    enableButtons();
                },
                'complete': function () {
                    $('.spinner').hide();
                }
            }); 
        }
    }

    $(document).ready(function(){
        $('#mirth-log-search').on('click', searchMirthLog);

        <?php echo $script; ?>

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

<script type="text/template" id="tr-mirth-template" style="display: none;">
    <tr class="clickable" data-id="{{ID}}"
        data-uri="Mirth/admin/listRoutes?message_id={{ID}}&channel_id={{CHANNEL_ID}}&filter={{FILTER}}&hos_num={{HOS_NUM}}&dateFrom={{FROM_DATE}}&&dateTo={{TO_DATE}}">
        <td {{{style}}}>{{ID}}</td>
        <td {{{style}}}>{{TYPE}}</td>
        <td {{{style}}}>{{RECEIVED_DATE}}</td>
        <td {{{style}}}>{{ERROR_STRING}}</td>
        <td {{{style}}}>{{ERROR_TEXT}}</td>
    </tr>
</script>