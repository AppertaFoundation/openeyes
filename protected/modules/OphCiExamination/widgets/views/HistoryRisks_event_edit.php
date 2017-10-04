<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use OEModule\OphCiExamination\models\HistoryRisksEntry;

?>

<?php
$model_name = CHtml::modelName($element);
$risks_options = $element->getRiskOptions();
$missing_req_risks = $element->getMissingRequiredRisks();
$required_risk_ids = array_map(function($r) { return $r->id; }, $element->getRequiredRisks());
?>
<script type="text/javascript" src="<?= $this->getJsPublishedPath('HistoryRisks.js') ?>"></script>
<div class="element-fields" id="<?= $model_name ?>_element">

    <div class="field-row row<?= (count($element->entries)+count($missing_req_risks)) ? ' hidden' : ''?> <?=$model_name?>_no_risks_wrapper">
        <div class="large-3 column">
            <label for="<?=$model_name?>_no_risks">Confirm patient has no risks:</label>
        </div>
        <div class="large-1 column end">
            <?php echo CHtml::checkBox($model_name .'[no_risks]', $element->no_risks_date ? true : false, array('class' => $model_name .'_no_risks')); ?>
        </div>
    </div>

    <input type="hidden" name="<?= $model_name ?>[present]" value="1" />

    <table class="<?= $model_name ?>_entry_table">
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
        $row_count = 0;
        foreach ($missing_req_risks as $entry) {
            $this->render(
                'HistoryRisksEntry_event_edit',
                array(
                    'entry' => $entry,
                    'form' => $form,
                    'model_name' => $model_name,
                    'field_prefix' => $model_name . '[entries][' . $row_count . ']',
                    'row_count' => $row_count,
                    'removable' => false,
                    'posted_not_checked' => $element->widget->postedNotChecked($row_count)
                )
            );
            $row_count++;
        } ?>
        <?php
        foreach ($element->entries as $entry) {
            $this->render(
                'HistoryRisksEntry_event_edit',
                array(
                    'entry' => $entry,
                    'form' => $form,
                    'model_name' => $model_name,
                    'field_prefix' => $model_name . '[entries][' . $row_count . ']',
                    'row_count' => $row_count,
                    'removable' => !in_array($entry->risk_id, $required_risk_ids),
                    'risks' => $risks_options,
                    'posted_not_checked' => $element->widget->postedNotChecked($row_count)
                )
            );
            $row_count++;
        }
        ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3"></td>
                <td><button class="button small primary <?= $model_name ?>_add_entry">Add</button></td>
            </tr>
        </tfoot>
    </table>
    <script type="text/template" class="<?= CHtml::modelName($element).'_entry_template' ?> hidden">
        <?php
        $empty_entry = new \OEModule\OphCiExamination\models\HistoryRisksEntry();
        $this->render(
            'HistoryRisksEntry_event_edit',
            array(
                'entry' => $empty_entry,
                'form' => $form,
                'model_name' => $model_name,
                'field_prefix' => $model_name . '[entries][{{row_count}}]',
                'row_count' => '{{row_count}}',
                'removable' => true,
                'risks' => $risks_options,
                'posted_not_checked' => false,
                'values' => array(
                    'id' => '',
                    'risk_id' => '{{risk_id}}',
                    'risk_display' => '{{risk_display}}',
                    'other' => '{{other}}',
                    'comments' => '{{comments}}',
                    'has_risk' => -9
                )
            )
        );
        ?>
    </script>
</div>


<script type="text/javascript">
    $(document).ready(function() {
        new OpenEyes.OphCiExamination.HistoryRisksController({
          element: $('#<?=$model_name?>_element')
        });
    });
</script>
