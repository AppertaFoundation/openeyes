<div class="row divider"></div>

<table class="standard cols-full" id="mirth-file-list">
    <colgroup>
        <col class="cols-1" span="4">
    </colgroup>
    <thead>
    <tr>
        <th>Received Date</th>
        <th>Status</th>
        <th>Connector Name</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach($routes as $route) { ?>
        <tr data-message-id="<?= $route['MESSAGE_ID'] ?>" 
            data-channel-id="<?= $_GET['channel_id'] ?>"
            data-route-id="<?= $route['ID'] ?>" >
            <td><?= $route['RECEIVED_DATE'] ?></td>
            <td><?= $route['STATUS'] ?></td>
            <td><?= $route['CONNECTOR_NAME'] ?></td>
            <td class="more"><a class="button small">More</a></td>
        </tr>
    <?php } ?>
    </tbody>
    <tfoot>
        <form id="cancelMessageForm" action="/Mirth/admin/list" method="post">
            <input type="hidden" id="channelId" name="channelId" value="<?= $_GET['channel_id'] ?>">
            <input type="hidden" id="filter" name="filter" value="<?= $_GET['filter'] ?>">
            <input type="hidden" id="hos_num" name="hos_num" value="<?= $_GET['hos_num'] ?>">
            <input type="hidden" id="dateFrom" name="dateFrom" value="<?= $_GET['dateFrom'] ?>">            
            <input type="hidden" id="dateTo" name="dateTo" value="<?= $_GET['dateTo'] ?>">
        </form>
        <tr>
            <td colspan="8">
                <?= \CHtml::submitButton(
                    'Cancel',
                    [
                        'class' => 'button large',
                        'name' => 'cancel',
                        'id' => 'et_cancel_route'
                    ]
                ); ?>
            </td>
        </tr>
    </tfoot>
</table>

<i class="spinner" title="Loading..." style="display: none;"></i>

<div class="row divider">

<script type="text/javascript">

    $(document).ready(function(){

        $('#et_cancel_route').on('click', function(e){
            e.preventDefault();
            $("<input />").attr("type", "hidden")
                .attr("name", "YII_CSRF_TOKEN")
                .attr("value", YII_CSRF_TOKEN)
                .appendTo("#cancelMessageForm");
            document.getElementById('cancelMessageForm').submit();
        });

        $('#mirth-file-list').on('click', '.more', function(){
            let tr = $(this).closest('tr');
            let data = {
                messageId: tr.data('message-id'),
                channelId: tr.data('channel-id'),
                routeId: tr.data('route-id'),
                YII_CSRF_TOKEN: YII_CSRF_TOKEN
            };

            if($('.ui-dialog').length <= 0) {
                $.ajax({
                    'url': '<?php echo Yii::app()->createUrl('Mirth/admin/getContentTypesForMessage'); ?>',
                    'type': 'POST',
                    'data': data,
                    'beforeSend':function (){
                        $('.spinner').show();
                    },
                    'success': function (result) {
                        let content = Mustache.render($('#dialog-template').text(), result);
                        
                        $(content).dialog({
                            width: "70%",
                            maxWidth: "700px",
                            maxHeight: "70%",
                            resizable: false,
                            close: function(event, ui)
                            {
                                $(this).dialog("close");
                                $(this).remove();
                                enableButtons();
                            },
                        });

                        $("#messageRadios").empty();

                        for (let i = 0; i < result['contentTypes'].length; i++) {
                            let messages = Mustache.render($('#mirth-radios').text(), result['contentTypes'][i]);
                            $("#messageRadios").append(messages);
                        }
                        $('.ui-dialog').css('overflow','auto');
                        $('.ui-dialog').css('height','70%');
                        $('.ui-dialog').position({
                            my: "center",
                            at: "center",
                            of: window
                        });

                        disableButtons();
                    },
                    'complete': function () {
                        $('.spinner').hide();
                    }
                });
            }
            
        });
        
        $('div#dialog-template').on('dialogclose', function(event) {
            alert('closed');
        });
    });

    function showMessage() {
            let tr = $('.dialogbox');
            let data = {
                messageId: tr.data('message-id'),
                channelId: tr.data('channel-id'),
                routeId: tr.data('route-id'),
                YII_CSRF_TOKEN: YII_CSRF_TOKEN,
                messageContentType: $('input[type=radio][name=message]:checked').attr('id')
            };
            
            $.ajax({
                'url': '<?php echo Yii::app()->createUrl('Mirth/admin/getMessageContent'); ?>',
                'type': 'POST',
                'data': data,
                'beforeSend':function (){
                    $('.spinner').show();
                },
                'success': function (result) {
                    let content = Mustache.render($('#mirth-message').text(), result);
                    $("#messageText").html(content);
                    enableButtons();
                },
                'complete': function () {
                    $('.spinner').hide();
                }
            });
            
        };
</script>

<script type="text/template" id="dialog-template" style="display: none;">

        <div style="display:none; width:500px;" class="dialogbox" title="More Info"
            data-message-id="{{messageId}}" 
            data-channel-id="{{channelId}}"
            data-route-id="{{routeId}}" >
            <p><b>History</b> - Channel ID: {{channelId}}, Message ID: {{messageId}}, Route ID: {{routeId}} <br>
            <div id="messageRadios" >
            </div><br>
            <div id="messageText" >
            </div>
        </div>
</script>

<script type="text/template" id="mirth-radios" style="display: none;">
    <input type="radio" id="{{ID}}" class="messageRadio" name="message" value="{{ID}}" onclick="showMessage();">
    <label for="html">{{NAME}}</label><br>
</script>

<script type="text/template" id="mirth-message" style="display: none;">
    <p><PRE>{{messageContent}}</PRE></p>
</script>