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
<?php

?>
<div class="admin box">

    <h2>Clinical Disorders</h2>
    <form action="#" method="get">

        <?=\CHtml::dropDownList('search[patient_type]', $search['patient_type'], $patient_types, [
            'empty' => '- Select -',
            'data-test' => 'patient-type-dropdown'
        ])?>
        <button data-test="patient-type-filter-submit" type="submit">Search</button>
    </form>

    <form id="admin_sections" data-test="clinical-disorder-result">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken ?>"/>
        <table class="standard">
            <thead>
            <tr>
                <th>Name</th>
                <th>ICD 10 Code</th>
                <th>Section</th>
                <th>Disorder</th>
                <th>SNOMED Code</th>
                <th>Active</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($disorders as $i => $disorder) {
                ?>
                <tr class="clickable" data-id="<?=$disorder->id?>"
                    data-uri="OphCoCvi/admin/editClinicalDisorder/<?=$disorder->id?>?patient_type=<?=$search['patient_type'];?>">
                    <td><?= \CHtml::encode($disorder->name) ?></td>
                    <td><?= \CHtml::encode($disorder->code) ?></td>
                    <td><?= \CHtml::encode($disorder->section->name) ?></td>
                    <td><?= \CHtml::encode($disorder->disorder->term ?? '')?></td>
                    <td><?= \CHtml::encode($disorder->disorder_id) ?></td>
                    <td><?= \OEHtml::icon($disorder->active ? 'tick' : 'remove', ['class' => 'small']) ?></td>
                </tr>
            <?php } ?>
            </tbody>
            <tfoot class="pagination-container">
            <tr>
                <td colspan="6">
                    <?php
                    $url = "/OphCoCvi/admin/addClinicalDisorder?patient_type={$search['patient_type']}";
                    echo EventAction::button('Add', 'add', array(), array('class' => 'small','data-type' => 'ClinicalDisorder', 'data-uri' => $url))->toHtml() ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </form>
</div>