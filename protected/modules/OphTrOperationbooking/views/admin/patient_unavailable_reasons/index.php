<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<div class="cols-5">
    <form id="admin_patientunavailablereasons" method="POST">
        <input type="hidden" name="model" value="OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason"/>
        <input type="hidden" name="redirect-url" value="/OphTrOperationbooking/admin/viewPatientUnavailableReasons"/>
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken ?>"/>
        <table class="standard">
            <thead>
            <tr>
                <th><input type="checkbox" id="selectall"/></th>
                <th>Name</th>
                <th>Active for Current Institution</th>
            </tr>
            </thead>
            <tbody class="sortable" data-sort-uri="/OphTrOperationbooking/admin/sortpatientunavailablereasons">
            <?php
            $criteria = new CDbCriteria();
            $criteria->order = 'display_order asc';
            $reasons = OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason::model()->findAll($criteria);
            $institution_id = Institution::model()->getCurrent()->id;
            foreach ($reasons as $i => $patientunavailablereason) {?>
                <tr class="clickable" data-attr-id="<?php echo $patientunavailablereason->id?>" data-uri="OphTrOperationbooking/admin/editpatientunavailablereason/<?php echo $patientunavailablereason->id?>">
                    <td><input type="checkbox" name="select[]" value="<?php echo $patientunavailablereason->id?>" class="patientunavailablereasons-enabled" id="select[<?= $patientunavailablereason->id ?>"/></td>
                    <td>
                        <?php echo $patientunavailablereason->name?>
                    </td>
                    <td>
                        <?php if ($patientunavailablereason->hasMapping(ReferenceData::LEVEL_INSTITUTION, $institution_id)) { ?>
                        <i class="oe-i tick small"></i>
                        <?php } else { ?>
                        <i class="oe-i remove small"></i>
                        <?php } ?>
                    </td>
                </tr>
            <?php }?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="3">
                    <?= \CHtml::submitButton(
                        'Add',
                        [
                            'class' => 'button large',
                            'name' => 'add',
                            'data-uri' => '/OphTrOperationbooking/admin/AddPatientUnavailableReason',
                            'id' => 'et_add'
                        ]
                    ) ?>
                    <?= \CHtml::submitButton(
                        'Enable selected for current institution',
                        [
                            'class' => 'button large',
                            'name' => 'add-mapping',
                            'formaction' => '/OphTrOperationbooking/admin/AddInstitutionMapping',
                            'id' => 'et_add_mapping'
                        ]
                    ) ?>
                    <?= \CHtml::submitButton(
                        'Disable selected for current institution',
                        [
                            'class' => 'button large',
                            'name' => 'delete-mapping',
                            'formaction' => '/OphTrOperationbooking/admin/DeleteInstitutionMapping',
                            'id' => 'et_delete_mapping'
                        ]
                    ) ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </form>
</div>
