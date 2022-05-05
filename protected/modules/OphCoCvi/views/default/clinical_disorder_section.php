<?php
/**
 * (C) Copyright Apperta Foundation 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2021, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<div class="admin box">

    <h2>Clinical Disorder Section</h2>

    <form action="#" method="get">
        <?=\CHtml::dropDownList('search[patient_type]', $search['patient_type'], $patient_types, [
                'empty' => '- Select -'
        ])?>
        <button type="submit">Search</button>
    </form>

    <form id="admin_sections">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken ?>"/>
        <table class="standard">
            <colgroup>
                <col class="cols-2">
                <col class="cols-1">
            </colgroup>
            <thead>
            <tr>
                <th>Name</th>
                <th>Active</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($disorder_sections as $i => $section) { ?>
                <tr class="clickable" data-id="<?=$section->id ?>"
                    data-uri="OphCoCvi/admin/editClinicalDisorderSection/<?=$section->id?>?&patient_type=<?=$search['patient_type']?>">
                    <td><?=\CHtml::encode($section->name) ?></td>
                    <td><?= \OEHtml::icon($section->active ? 'tick' : 'remove', ['class' => 'small']) ?></td>
                </tr>
            <?php } ?>
            </tbody>
            <tfoot class="pagination-container">
            <tr>
                <td colspan="6">
                    <?php echo EventAction::button('Add', 'add', array(), array('class' => 'small','data-type' => 'ClinicalDisorderSection', 'data-uri' => '/OphCoCvi/admin/addClinicalDisorderSection'))->toHtml() ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </form>
</div>
