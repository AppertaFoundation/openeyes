<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

?>

<div class="element-fields">
    <?php
    Yii::app()->clientScript->registerScriptFile($this->getJsPublishedPath('HistoryRisks.js'), CClientScript::POS_HEAD);
    $model_name = CHtml::modelName($element);
    $risks_options = $element->getRiskOptions();
    ?>
    <input type="hidden" name="<?= $model_name ?>[present]" value="1" />

    <table id="<?= $model_name ?>_entry_table">
        <thead>
        <tr>
            <th>Risk</th>
            <th>Checked Status</th>
            <th>Comments</th>
            <th>Action(s)</th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($element->entries as $i => $entry) {
            $this->render(
                'HistoryRisksEntry_event_edit',
                array(
                    'entry' => $entry,
                    'form' => $form,
                    'field_prefix' => $model_name . '[entries][' . $i . ']',
                    'editable' => true,
                    'risks' => $risks_options
                )
            );
        }
        ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3"></td>
                <td><button class="button small primary" id="<?= $model_name ?>_add_entry">Add</button></td>
            </tr>
        </tfoot>
    </table>
</div>

<script type="text/template" id="<?= CHtml::modelName($element).'_entry_template' ?>" class="hidden">
    <?php
    $empty_entry = new \OEModule\OphCiExamination\models\HistoryRisksEntry();
    $this->render(
        'HistoryRisksEntry_event_edit',
        array(
            'entry' => $empty_entry,
            'form' => $form,
            'field_prefix' => $model_name . '[entries][{{row_count}}]',
            'editable' => true,
            'risks' => $risks_options,
            'values' => array(
                'id' => '',
                'risk_id' => '{{risk_id}}',
                'risk_display' => '{{risk_display}}',
                'other' => '{{other}}',
                'comments' => '{{comments}}',
                'has_risk' => null
            )
        )
    );
    ?>
</script>
<script type="text/javascript">
    $(document).ready(function() {
        new OpenEyes.OphCiExamination.HistoryRisksController();
    });
</script>
