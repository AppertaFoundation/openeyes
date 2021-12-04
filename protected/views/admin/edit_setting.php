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

/**
 * @var $metadata SettingMetadata
 * @var $allowed_classes string[]
 */
?>

<div>
    <div class="row divider">
        <h2>Edit setting: <?= $metadata->name ?></h2>
    </div>

    <?php
    $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
        'id' => 'settingsform',
        'enableAjaxValidation' => false,
        'focus' => '#username',
        'layoutColumns' => array(
            'label' => 2,
            'field' => 5,
        )))?>
    <?php
    if (isset($institution_id)) {
        echo CHtml::hiddenField($metadata->key.'_institution_id', $institution_id);
    }
    $this->renderPartial(
        '_admin_setting_' . strtolower(str_replace(' ', '_', $metadata->field_type->name)),
        [
            'metadata' => $metadata,
            'allowed_classes' => $allowed_classes,
            'institution_id' => $institution_id,
        ]
    );
    ?>
    <hr class="divider">
    <div class="row">
        <?= CHtml::submitButton('Save ' . $metadata->name, [
                'class' => 'green hint',
                'name' => 'save',
                'id' => 'et_save'
            ])
?>

        <?= CHtml::submitButton('Cancel', [
                'class' => 'blue hint',
                'name' => 'cancel',
                'id' => 'et_cancel'
            ])
?>
    </div>

    <?php $this->endWidget() ?>
</div>
