<div class="oe-full-header use-full-screen">
    <div class="title allcaps">Trials Upload</div>
</div>

<div class="oe-full-content subgrid oe-trials">

    <nav class="oe-full-side-panel">
        <h3>Upload data</h3>
        <p>CSV file upload</p>
        <h3>Actions</h3>
        <ul>
            <li><a href="<?= $backuri ?? '/OETrial/trial/' ?>">Go Back to Trials</a></li>
        </ul>
    </nav>

    <main class="oe-full-main">
        <div class="errorSummary">
            <?php
            if (isset($errors) and $errors !== null) {
                echo '<div class="alert-box warning">';
                echo Helper::array_dump_html($errors);
                echo '</div>';
            }
            ?>
        </div>
        <section class="element">
            <?php
                $form = $this->beginWidget(
                    'CActiveForm',
                    array(
                        'id' => 'upload-form',
                        'action' => Yii::app()->createURL('csv/preview', array('context' => $context)),
                        'enableAjaxValidation' => false,
                        'htmlOptions' => array('enctype' => 'multipart/form-data'),
                    )
                );
            ?>
            <h3>Only CSV files are accepted (Maximum size: 2 MB)</h3>
            <hr class="divider">
            <div class="element-fields full-width">
                <div class="upload-box">
                    <label for="CSV_upload_area_id" class="upload-label"
                    ondrop="drop(event)" ondragover="allowDrop(event)">
                        <i class="oe-i download no-click large"></i>
                        <br>
                        <span class="js-upload-box-text">Click to select file, DROP here or press Ctrl + V to paste</span>
                    </label>
                    <?= $form->fileField(new Csv(), 'csvFile',
                    [
                        'autocomplete' => 'off',
                        'id' => 'CSV_upload_area_id',
                        'style' => 'display:none;'
                    ]) ?>
                </div>
            </div>
            <div class="data-group flex-c">
                <?= CHtml::submitButton(
                    'Process CSV file',
                    [
                        'class' => 'button hint green'
                    ]
                ) ?>
                <span class="tabspace"></span>
                <?=\CHtml::button(
                    'Remove uploaded file',
                    [
                        'class' => 'button hint red disabled',
                        'id' => 'CSV_remove_file'
                    ]
                ) ?>
            </div>
        </section>
    </main>
    <?php $this->endWidget(); ?>
</div>

<script>
    $('#CSV_upload_area_id').on('change', function () {

        // Disable the remove field until the file has been processed
        if (!$('#CSV_remove_file').hasClass('disabled')) {
            $('#CSV_remove_file').addClass('disabled');
        }

        // If the file has not been uploaded, no need to process it
        if( $(this).get(0).files.length !== 0 ){
            let file_type = $(this).get(0).files[0].type;
            let file_size = $(this).get(0).files[0].size;
            $.ajax({
                type: 'POST',
                url: '/Csv/fileCheck',
                data: {
                    'YII_CSRF_TOKEN': YII_CSRF_TOKEN,
                    'file_type': file_type,
                    'file_size': file_size,
                },
                xhr: function()
                {
                    var xhr = $.ajaxSettings.xhr();
                    if (xhr.upload) {
                        xhr.upload.addEventListener("progress", function (evt) {
                            if (evt.lengthComputable) {
                                let percentage = (evt.loaded / evt.total) * 100;
                                $('.js-upload-box-text').text("Uploading: " + Math.round(percentage) + "%");
                            }
                        }, false);
                    }
                    return xhr;
                },
                success: function (response) {
                    if (response !== null) {
                        new OpenEyes.UI.Dialog.Alert({
                            content: response
                        }).open();
                        $('.js-upload-box-text').text("Click to select file, DROP here or press Ctrl + V to paste");
                        $("#CSV_upload_area_id").val("");
                    } else {
                        $('.js-upload-box-text').text("Upload complete");
                        $('#CSV_remove_file').removeClass('disabled');
                    }
                },
                error: function (xhr) {
                    alert(xhr.responseText);
                }
            });
        } else {
            $('.js-upload-box-text').text("Click to select file, DROP here or press Ctrl + V to paste");
        }
    });

    $('#CSV_remove_file').on('click', function () {
        if (!$('#CSV_remove_file').hasClass('disabled')) {
            $("#CSV_upload_area_id").val("");
            $('.js-upload-box-text').text("Click to select file, DROP here or press Ctrl + V to paste");
            $('#CSV_remove_file').addClass('disabled');
        }
    });
</script>
