<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>
<?php
Yii::app()->clientScript->registerScriptFile('../../node_modules/cropper/dist/cropper.min.js');
Yii::app()->clientScript->registerCssFile('../../node_modules/cropper/dist/cropper.min.css', null, 10);
?>
<div class="box admin">
    <?php if (!$img) { ?>
        <div class="alert-box alert column error">Cannot load signature image</div>
    <?php } else { ?>
        <div id="signature-crop">
            <img id="canvas_img" src="<?=$img?>" style="display: none;">
            <canvas id="canvas" class="signature_import_log_canvas"></canvas>
            <div
                id="signature_import_log_form_hos_num"
                class="row field-row"
                <?php if( $log->event_id > 0 && isset($log_parameters['e_id'] )) { ?>style="display:none;"<?php } ?>
            >
                <div class="large-2 column">
                    <label for="signature_import_log_form_hos_num">Hospital Number:</label>
                </div>
                <div class="large-5 column">
                    <?php
                    $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                        'name' => 'autocomplete_hos_num',
                        'id' => 'autocomplete_hos_num',
                        'value' => '',
                        'sourceUrl' => array('dicomLogViewer/signatureImportLogAutocomplete'),
                        'options' => array(
                            'minLength' => '7',
                            'select' => "js:function(event, ui) {
                                            $('#autocomplete_hos_num').val(ui.item.hos_num);
                                            $('#unique_id').val(ui.item.unique_id);
                                            $('#e_id').val(ui.item.element_id);
                                            $('#event_id').val(ui.item.event_id);
                                            $('#cvi_hos_num').html(ui.item.hos_num);
                                            $('#cvi_patient_name').html(ui.item.patient_name);
                                            $('#cvi_date').html(ui.item.value);
                                            $('#cvi_date_box').show();
                                            $('#btnCrop').show();
                                            return false;
                                        }",
                            'response' => 'js:function(event, ui){
                                if(ui.content.length === 0){
                                    $("#cvi_date_box").hide();
                                    $("#btnCrop").hide();
                                    $("#no_result").show();
                                } else {
                                    $("#no_result").hide();
                                }
                            }'
                        ),
                        'htmlOptions' => array('placeholder' => 'hospital number here'),
                    ));
                    ?>
                    <div id="no_result" class="alert-box alert column end error hide">There is no suitable CVI event or the CVI has been issued for this hospital number</div>
                    <div id="cvi_date_box" class="hide">
                        Patient Name: <span id="cvi_patient_name"></span><br>
                        Hospital Number: <span id="cvi_hos_num"></span><br>
                        CVI date: <span id="cvi_date"></span><br>
                    </div>
                </div>
            </div>
            <div class="large-12">
                <button id="btnCrop" data-test="crop_button" class="small primary event-action hide" value="">Save</button>
                <a href="<?php echo Yii::app()->createUrl('/DicomLogViewer/signatureList?type='.$type.'&page='.$page) ?>"><button class="small primary event-action" value="">Cancel</button></a>
                <button id="btnManualIgnore" class="small primary event-action" value="">Manual Ignore</button>
            </div>
            <?php
            if ($log->event) {
                $unique_code_mapping = \UniqueCodeMapping::model()->findByAttributes(['event_id' => $log->event->id]);
                $unique_code = $unique_code_mapping->unique_codes ? $unique_code_mapping->unique_codes->code : null;
            }
            ?>
            <input
                type="hidden"
                id="unique_id"
                name="signature_import_log_form[unique_id]"
                value="<?= isset($unique_code) && $unique_code ? $unique_code . '"':''; ?>"
            >

            <input type="hidden" id="e_t_id" name="signature_import_log_form[e_t_id]" value="<?= $element_type_id ?>">
            <input type="hidden" id="log_id" name="signature_import_log_form[log_id]" value="<?= $log->id ?>">
            <input
                type="hidden"
                id="e_id"
                name="signature_import_log_form[e_id]"
                value="<?php if( $log->event_id > 0 && isset($log_parameters['e_id']) ) { echo $log_parameters['e_id']; } ?>"
            >
            <input
                type="hidden"
                id="event_id"
                name="signature_import_log_form[event_id]"
                value="<?php if( $log->event_id > 0 ) { echo $log->event_id; } ?>"
            >
            <input type="hidden" id="log_id" name="signature_import_log_form[log_id]" value="<?=$log->id?>">
        </div>
    <?php } ?>
</div>

<?php if ($img) { ?>
<script type="text/javascript">
    var canvas  = $("#canvas"),
        context = canvas.get(0).getContext("2d"),
        $result = $('#result');

    var img = new Image();
    img.onload = function() {
        context.canvas.height = img.height;
        context.canvas.width  = img.width;
        context.drawImage(img, 0, 0);
        var cropper = canvas.cropper({
            aspectRatio: 16 / 9,
            viewMode: 3,
            dragMode: 'move'
        });

        changeStatus = function(log_id, status_id)
        {
            $.ajax({
                'type': 'POST',
                'url': baseUrl + '/DicomLogViewer/statusChange',
                'data': {'id': log_id, 'status_id': status_id, 'event_id': $('#event_id').val(), YII_CSRF_TOKEN: YII_CSRF_TOKEN},
                'success': function(data) {
                    new OpenEyes.UI.Dialog.Alert({
                        title: "<span data-test='answer_string'>Operation was successfull</span>",
                        onClose: function() { enableButtons();
                            window.location.href = baseUrl+'/DicomLogViewer/signatureList?type=<?=$type?>&page=<?=$page?>';
                            enableButtons();
                        }
                    }).open();

                }
            });
        }

        $('#btnCrop').on('click',function() {
            if($('#unique_id').val()==='' || $('#log_id').val()==='' || $('#e_t_id').val()==='' ||  $('#e_id').val()==='' ){
                new OpenEyes.UI.Dialog.Alert({
                    title: "<span data-test='answer_string'>Please identify the Event.</span>",
                }).open();
                return false;
            }

            var croppedImageDataURL = canvas.cropper('getCroppedCanvas').toDataURL("image/png");
            imgData = croppedImageDataURL.replace('data:image/png;base64,','');
            var unique_identifier = $('#unique_id').val();
            var original_log_id = $('#log_id').val();
            var signatureUrl = baseUrl+"/Api/sign/add";
            var xhr = new XMLHttpRequest();
            xhr.open('POST', signatureUrl, true);
            xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
            var sendObj = JSON.stringify({"unique_identifier":unique_identifier,"image":imgData,"original_log_id":original_log_id, "extra_info": '{"e_t_id":'+$('#e_t_id').val()+',"e_id":'+$('#e_id').val()+'}', 'YII_CSRF_TOKEN': YII_CSRF_TOKEN});
            xhr.send(sendObj);
            xhr.onreadystatechange = function(e) {
                if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                    var log_id = $('#log_id').val();
                    var status_id = 5;

                    changeStatus(log_id, status_id);
                }
            };
        });
    };
    img.src = $('#canvas_img').attr('src');

    $('#btnManualIgnore').click(function() {
        var status_id = 6;
        var log_id = $('#log_id').val();
        $.ajax({
            'type': 'POST',
            'url': baseUrl + '/dicomLogViewer/statusChange',
            'data': {'id': log_id, 'status_id': status_id, 'event_id': $('#event_id').val(), YII_CSRF_TOKEN: YII_CSRF_TOKEN},
            'success': function(data) {
                new OpenEyes.UI.Dialog({
                    title: "Manual ignore was successfull",
                    onClose: function() { enableButtons(); },
                    buttons: {
                        "Close" : {
                            text: "OK",
                            click: function(){
                                window.location.href = baseUrl+'/DicomLogViewer/signatureList?type=<?=$type?>&page=<?=$page?>';
                                enableButtons();
                            }
                        },
                    }
                }).open();
            }
        });
    });
</script>
<?php } ?>