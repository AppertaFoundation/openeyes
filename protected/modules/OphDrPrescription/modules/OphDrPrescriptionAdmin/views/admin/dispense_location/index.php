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

<div class="cols-5" id="generic-admin-list">
    <form id="admin_<?= get_class(OphDrPrescription_DispenseLocation::model()); ?>">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken ?>"/>
        <input type="hidden" name="model" value="<?= OphDrPrescription_DispenseLocation::class ?>"/>
        <table class="standard" id="et_sort" data-uri="/OphDrPrescription/admin/DispenseLocation/sortLocations">
            <thead>
            <tr>
                <th><input type="checkbox" name="selectall" id="selectall"/></th>
                <th>Name</th>
                <th>Dispense Location</th>
                <th>Display Order</th>
                <th>Active for Current Institution</th>
            </tr>
            </thead>
            <colgroup>
                <col class="cols-1">
                <col class="cols-1">
                <col class="cols-5">
                <col class="cols-1">
                <col class="cols-1">
            </colgroup>
            <tbody class="sortable">
            <?php foreach ($dispense_locations as $dispense_location) {
                $this->renderPartial(
                    '/admin/dispense_location/_dispense_location_entry',
                    [
                        'model' => $dispense_location,
                        'data_id' => $dispense_location->id,
                        'data_uri' => 'OphDrPrescription/admin/DispenseLocation/edit/' . $dispense_location->id,
                        'name' => $dispense_location->name,
                        'display_order' => $dispense_location->display_order,
                        'is_active' => $dispense_location->hasMapping(
                            ReferenceData::LEVEL_INSTITUTION,
                            Yii::app()->session['selected_institution_id']
                        )
                    ]
                );
            } ?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="9">
                    <?php echo \CHtml::button(
                        'Add',
                        [
                            'data-uri' => '/OphDrPrescription/admin/DispenseLocation/create',
                            'class' => 'button large',
                            'id' => 'et_add'
                        ]
                    );
                    echo CHtml::submitButton(
                        'Add selected to current Institution',
                        [
                            'name' => 'admin-map-add',
                            'id' => 'et_admin-map-add',
                            'class' => 'generic-admin-save button large',
                            'formaction' => '/OphDrPrescription/admin/DispenseLocation/addMapping',
                            'formmethod' => 'POST',
                        ]
                    );
                    echo CHtml::submitButton(
                        'Remove selected from current Institution',
                        [
                            'name' => 'admin-map-remove',
                            'id' => 'et_admin-map-remove',
                            'class' => 'generic-admin-save button large',
                            'formaction' => '/OphDrPrescription/admin/DispenseLocation/removeMapping',
                            'formmethod' => 'POST',
                        ]
                    );
                    ?>
                </td>
            </tr>
            </tfoot>
        </table>
</div>