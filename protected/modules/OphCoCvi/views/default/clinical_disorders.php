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
    $search_params =\Yii::app()->request->getQuery('search')['event_type_version'];
    $event_type_version = \CHtml::encode($search_params['event_type_version'] ?? 0);
    $patient_type = \CHtml::encode($search_params['patient_type'] ?? 0);
?>
<div class="admin box">

    <h2>Clinical Disorders</h2>

    <?php $this->widget('GenericSearch', array('search' => $search)); ?>

    <form id="admin_sections">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken ?>"/>
        <table class="grid">
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
                <tr class="clickable" data-id="<?php echo CHtml::encode($disorder->id) ?>"
                    data-uri="OphCoCvi/admin/editClinicalDisorder/<?php echo \CHtml::encode($disorder->id) ?>?event_type_version=<?=$event_type_version;?>&patient_type=<?=$patient_type;?>">
                    <td><?php echo CHtml::encode($disorder->name) ?></td>
                    <td><?php echo CHtml::encode($disorder->code) ?></td>
                    <td><?php echo CHtml::encode($disorder->section->name) ?></td>
                    <td><?php echo (!empty($disorder->disorder->term)) ? CHtml::encode($disorder->disorder->term) : '' ?></td>
                    <td><?php echo CHtml::encode($disorder->disorder_id) ?></td>
                    <td><?php echo ($disorder->active) ? 'Active' : 'Inactive' ?></td>
                </tr>
            <?php } ?>
            </tbody>
            <tfoot class="pagination-container">
            <tr>
                <td colspan="6">
                    <?php
                    $url = "/OphCoCvi/admin/addClinicalDisorder?event_type_version={$event_type_version}&patient_type={$patient_type}";
                    echo EventAction::button('Add', 'add', array(), array('class' => 'small','data-type' => 'ClinicalDisorder', 'data-uri' => $url))->toHtml() ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </form>
</div>