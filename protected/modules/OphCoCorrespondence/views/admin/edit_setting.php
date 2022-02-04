<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<?php echo $this->renderPartial('//admin/_form_errors', array('errors' => $errors)) ?>

<?php
$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
    'id' => 'settingsformaaaaa',
    'enableAjaxValidation' => false,
    'layoutColumns' => array(
        'label' => 2,
        'field' => 5,
    ),
)) ?>

<div class="cols-4">

    <div class="row divider">
        <h2>Edit setting</h2>
    </div>

    <table class="standard" id="to_location_sites_grid">
        <thead>
        <colgroup>
            <col class="cols-3">
            <col class="cols-1">
        </colgroup>
        </thead>

        <tbody>
        <tr>
            <td><?= $metadata->name ?></td>
            <td>
                <?php
                if (isset($institution_id)) {
                    echo CHtml::hiddenField($metadata->key.'_institution_id', $institution_id);
                }
                $this->renderPartial(
                    '//admin/_admin_setting_' . strtolower(str_replace(' ', '_', $metadata->field_type->name)),
                    array('metadata' => $metadata, 'allowed_classes' => null, 'institution_id' => $institution_id)
                )
                ?>
            </td>
        </tr>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="2">
                <?=\CHtml::submitButton(
                    'Save',
                    [
                        'class' => 'button large',
                        'id' => 'et_save',
                    ]
                ); ?>
                <?=\CHtml::button(
                    'Cancel',
                    [
                        'data-uri' => $cancel_uri,
                        'class' => 'button large',
                        'type' => 'button',
                        'name' => 'cancel',
                        'id' => 'et_cancel'
                    ]
                ); ?>
            </td>
        </tr>
        </tfoot>
    </table>
</div>

<?php $this->endWidget() ?>