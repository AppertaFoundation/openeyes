<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<div id="edit_oph_info" style="display: none;">

    <fieldset class="data-group">
        <legend><strong>Edit CVI Status</strong></legend>
        <?php
        $form = $this->beginWidget('FormLayout', array(
            'id' => 'edit-oph_info',
            'htmlOptions' => array('class' => 'form add-data'),
            'layoutColumns' => array(
                'label' => 3,
                'field' => 9
            ),
        )) ?>

        <div class="data-group">
            <div class="<?php echo $form->columns('label'); ?>">
                <label for="PatientOphInfo_cvi_status_id">Status:</label>
            </div>
            <div class="<?php echo $form->columns('field'); ?>">
                <?=\CHtml::activeDropDownList($info, 'cvi_status_id',
                    CHtml::listData(
                        PatientOphInfoCviStatus::model()->active()->findAll(array('order' => 'display_order')),
                        'id',
                        'name')) ?>
                <?php echo $form->error($info, 'cvi_status_date'); ?>
            </div>
        </div>

        <?php
        $this->renderPartial('_fuzzy_date', array('form' => $form, 'date' => $info->cvi_status_date)) ?>

        <input type="hidden" name="patient_id" value="<?php echo $this->patient->id ?>"/>

        <div id="oph_info_errors" class="alert-box alert hide"></div>
        <div class="buttons">
            <img src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif') ?>"
                 class="edit_oph_info_loader" style="display: none;"/>
            <button type="submit" class="secondary small btn_save_oph_info">
                Save
            </button>
            <button class="warning small btn_cancel_previous_operation btn_cancel_oph_info">
                Cancel
            </button>
        </div>

        <?php $this->endWidget(); ?>
    </fieldset>
</div>
<script type="text/javascript">
    $('#btn-edit_oph_info').click(function() {
        $('#edit_oph_info').slideToggle('fast');
        $('#btn-edit_oph_info').attr('disabled',true);
        $('#btn-edit_oph_info').addClass('disabled');
    });
    $('button.btn_cancel_oph_info').click(function(e) {
        $('#edit_oph_info').slideToggle('fast');
        $('#btn-edit_oph_info').attr('disabled',false);
        $('#btn-edit_oph_info').removeClass('disabled');
        $('#oph_info_errors').html('').hide();
        OpenEyes.Form.reset($(e.target).closest('form'));
        return false;
    });
    handleButton($('button.btn_save_oph_info'), function () {
        $('#oph_info_errors').html('').hide();
        $('img.edit_oph_info_loader').show();
        $.post(
            <?= json_encode($this->createUrl('patient/editOphInfo')) ?>,
            $('#edit-oph_info').serialize(),
            function (result) {
                if (result == true) {
                    location.href = <?= json_encode($this->createUrl('patient/view', array('id' => $this->patient->id))) ?>;
                } else {
                    enableButtons();
                    $('img.edit_oph_info_loader').hide();
                    for (var i in result) {
                        for (var j in result[i]) {
                            $('#oph_info_errors').append('<div>' + result[i][j] + '</div>');
                        }
                    }
                    $('#oph_info_errors').show();
                }
            },
            'json'
        );
    });
</script>
