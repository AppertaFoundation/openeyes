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

?>
<script type="text/javascript" src="<?= $this->getJsPublishedPath('PastSurgery.js') ?>"></script>
<?php
$model_name = CHtml::modelName($element);
?>

<div class="element-fields">
  <input type="hidden" name="<?= $model_name ?>[present]" value="1" />
    <table id="<?= $model_name ?>_operation_table" class="<?= $model_name ?>_Operation">
        <thead>
        <tr>
            <th>Operation</th>
            <th>Side</th>
            <th>Date</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $row_count = 0;
        foreach ($operations as $i => $op) {
            if (!array_key_exists('object', $op)) {
                $this->render(
                    'PastSurgery_OperationEntry_event_edit',
                    array(
                        'values' => array(
                            'op' => $op,
                            'operation' => $op['operation'],
                            'form' => $form,
                            'model_name' => CHtml::modelName($element),
                            'side' => $op['side'],
                            'date' => $op['date'],

                        ),
                        'removable' => false,
                        'row_count' => ($row_count),
                        'field_prefix' => $model_name . '[operation][' . ($row_count) . ']',
                        'model_name' => CHtml::modelName($element),
                    )
                );
                $row_count++;
            }
        }
        foreach ($element->operations as $i => $op) {
            $this->render(
                'PastSurgery_OperationEntry_event_edit',
                array(
                    'op' => $op,
                    'form' => $form,
                    'row_count' => ($row_count),
                    'field_prefix' => $model_name . '[operation][' . ($row_count) . ']',
                    'model_name' => CHtml::modelName($element),
                    'removable' => true,
                )
            );
            $row_count++;
        }
        ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3"></td>
                <td class="text-right"><button class="button small primary" id="<?= $model_name ?>_add_entry">Add</button></td>
            </tr>
        </tfoot>
    </table>
</div>
<script type="text/template" id="<?= CHtml::modelName($element).'_operation_template' ?>" class="hidden">
    <?php
    $empty_operation = new \OEModule\OphCiExamination\models\PastSurgery_Operation();
    $this->render(
        'PastSurgery_OperationEntry_event_edit',
        array(
            'op' => $empty_operation,
            'form' => $form,
            'model_name' => CHtml::modelName($element),
            'row_count' => '{{row_count}}',
            'field_prefix' => $model_name . '[operation][{{row_count}}]',
            'removable' => true,
            'values' => array(
                'id' => '',
                'previous_operation_id' => '',
                'operation' => '{{operation}}',
                'side_id' => '{{side_id}}',
                'side_display' => '{{side_display}}',
                'date' => '{{date}}',
                'date_display' => '{{date_display}}'
            )
        )
    );
    ?>
</script>
