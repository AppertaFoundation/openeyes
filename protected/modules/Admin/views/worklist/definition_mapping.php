<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
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

<?php Yii::app()->clientScript->registerPackage('tagsinput'); ?>

<div class="admin box cols-6">
    <h2><?= $mapping->isNewRecord ? 'Create' : 'Edit'?> Worklist Definition Mapping</h2>

    <?php echo $this->renderPartial('//admin/_form_errors', array('errors' => $errors))?>
    <?php
    $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
        'id' => 'mapping-form',
        'enableAjaxValidation' => false,
        'focus' => '#Worklist_name',
        'layoutColumns' => array(
            'label' => 2,
            'field' => 5,
        ),
    ))?>
    <div class="row">
        <?php echo $form->checkbox($mapping, 'willdisplay', [], ['field' => 2]); ?>
    </div>
    <div class="row">
        <?php echo $form->textField($mapping, 'key', array('autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete')), null, array('field' => 2))?>
    </div>
    <div class="row">
        <div class="cols-full column large-push-2">
            <i>If no values are provided for a mapping, any value will be accepted. <br/> This is useful for adding information to each worklist entry without restricting matches.</i>
        </div>
    </div>
    <div class="row">
        <?php echo $form->textField(
            $mapping,
            'valuelist',
            array('autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete')),
            null,
            array('field' => 2)
        )?>
    </div>

    <?php echo $form->formActions(array('cancel-uri' => '/Admin/worklist/definitionMappings/' . $mapping->worklist_definition_id))?>
    <?php $this->endWidget()?>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#WorklistDefinitionMapping_valuelist').tagsInput({
            'defaultText': 'add values'
        });
    });
</script>
