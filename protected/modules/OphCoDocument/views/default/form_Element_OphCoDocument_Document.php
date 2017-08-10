<?php
    function generateFileField($element, $index)
    {
            if($element->{$index."_id"} > 0){
                if(strrchr($element->{$index}->name, '.') == '.pdf'){
                    ?>
                    <div id="ophco-image-container-'+sideID+'" class="ophco-image-container">
                        <object width="100%" height="500px" data="/file/view/<?php echo $element->{$index}->id ?>/image<?php echo strrchr ($element->{$index}->name, '.') ?>" type="application/pdf">
                            <embed src="/file/view/<?php echo $element->{$index}->id ?>/image<?php echo strrchr ($element->{$index}->name, '.') ?>" type="application/pdf" />
                        </object>
                        <span title="Delete" onclick="deleteOPHCOImage(<?php echo $element->{$index}->id; ?>, '<?php echo $index; ?>' );" class="image-del-icon">X</span>
                    </div>
                    <?php
                } else {
                    ?>
                    <div id="ophco-image-container-<?php echo $element->{$index}->id;?>" class="ophco-image-container">
                        <img src="/file/view/<?php echo $element->{$index}->id;?>/image<?php echo strrchr($element->{$index}->name, '.');?>" border="0">
                        <span title="Delete" onclick="deleteOPHCOImage(<?php echo $element->{$index}->id; ?>, '<?php echo $index."_id";?>' );" class="image-del-icon">X</span>
                    </div>
                    <?php
                }
            }else {
                ?>
                <div class="upload-box">
                    <label for="Document_<?php echo $index; ?>_id" id="upload_box" class="upload-label"
                           ondrop="drop(event)" ondragover="allowDrop(event)">
                        <svg class="box__icon" xmlns="http://www.w3.org/2000/svg" width="50" height="43"
                             viewBox="0 0 50 43">
                            <path
                                d="M48.4 26.5c-.9 0-1.7.7-1.7 1.7v11.6h-43.3v-11.6c0-.9-.7-1.7-1.7-1.7s-1.7.7-1.7 1.7v13.2c0 .9.7 1.7 1.7 1.7h46.7c.9 0 1.7-.7 1.7-1.7v-13.2c0-1-.7-1.7-1.7-1.7zm-24.5 6.1c.3.3.8.5 1.2.5.4 0 .9-.2 1.2-.5l10-11.6c.7-.7.7-1.7 0-2.4s-1.7-.7-2.4 0l-7.1 8.3v-25.3c0-.9-.7-1.7-1.7-1.7s-1.7.7-1.7 1.7v25.3l-7.1-8.3c-.7-.7-1.7-.7-2.4 0s-.7 1.7 0 2.4l10 11.6z"/>
                        </svg>
                        <br> Click to select file or DROP here</label>
                    <input autocomplete="off" type="file" name="Document[<?php echo $index; ?>_id]"
                           id="Document_<?php echo $index; ?>_id" style="display:none;">
                </div>
                <?php
            }
    }
?>

<div class="element-fields">
    <?php echo $form->dropDownList($element, 'event_sub_type',
        CHtml::listData(OphCoDocument_Sub_Types::model()->findAll(), 'id', 'name'),
        array(),
        array(),
        array(
            'label' => 4,
            'field' => 2,
        )); ?>
    <div class="row field-row">
        <div class="large-8 column">
            <label class="inline highlight">
                <input type="radio" name="upload_mode" value="single" <?php if($element->single_document_id >0){echo "checked";}?>>Single file
            </label>
            <label class="inline highlight">
                <input type="radio" name="upload_mode" value="double" <?php if($element->left_document_id > 0 || $element->right_document_id >0){echo "checked";}?>>Right/Left sides
            </label>
        </div>
    </div>
    <div class="row field-row" id="single_document_uploader">
        <div class="large-8 column" id="single_document_id_row">
            <?php generateFileField($element, 'single_document'); ?>
        </div>
    </div>
    <div id="double_document_uploader">
        <div class="row field-row">
            <div class="large-6 column">
                <b>RIGHT</b>
            </div>
            <div class="large-6 column">
                <b>LEFT</b>
            </div>
        </div>
        <div class="row field-row" id="double_document_uploader">
            <div class="large-6 column" id="right_document_id_row">
                <?php generateFileField($element, 'right_document'); ?>
            </div>
            <div class="large-6 column" id="left_document_id_row">
                <?php generateFileField($element, 'left_document'); ?>
            </div>
        </div>
    </div>
    <div id="showUploadStatus" style="height:10px;color:#ffffff;font-size:8px;text-align:right;width:0px;background-color:#22b24c"></div>
</div>

