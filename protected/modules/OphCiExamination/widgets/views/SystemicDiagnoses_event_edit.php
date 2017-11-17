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

<script type="text/javascript" src="<?=$this->getJsPublishedPath('SystemicDiagnoses.js')?>"></script>
<script type="text/javascript" src="<?=$this->getJsPublishedPath('OpenEyes.UI.DiagnosesSearch.js', true)?>"></script>

<?php
    $model_name = CHtml::modelName($element);
?>

<div class="element-fields" id="<?=CHtml::modelName($element);?>_element">

    <input type="hidden" name="<?= $model_name ?>[present]" value="1" />
    <table id="<?= $model_name ?>_diagnoses_table">
        <thead>
        <tr>
            <th>Diagnosis</th>
            <th>Side</th>
            <th>Date</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($element->diagnoses as $row_count => $diagnosis) {
            $this->render(
                'SystemicDiagnosesEntry_event_edit',
                array(
                    'diagnosis' => $diagnosis,
                    'form' => $form,
                    'model_name' => CHtml::modelName($element),
                    'row_count' => $row_count,
                    'field_prefix' => $model_name . "[entries][$row_count]",
                    'removable' => true
                )
            );
        }
        ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3"></td>
                <td><button class="button small primary add-entry">Add</button></td>
            </tr>
        </tfoot>
    </table>


    <script type="text/template" class="entry-template hidden">
        <?php
        $empty_entry = new \OEModule\OphCiExamination\models\SystemicDiagnoses_Diagnosis();
        $this->render(
            'SystemicDiagnosesEntry_event_edit',
            array(
                //'diagnosis' => $empty_entry,
                'form' => $form,
                'model_name' => $model_name,
                'field_prefix' => $model_name . '[entries][{{row_count}}]',
                'row_count' => '{{row_count}}',
                'removable' => true,

                'values' => array(
                    'id' => '',
                    'disorder_id' => '{{disorder_id}}',
                    'disorder_display' => '{{disorder_display}}',
                    'side_id' => '{{side_id}}',
                    'side_display' => '{{side_display}}',
                    'date' => '{{date}}',
                    'date_display' => '{{date_display}}',
                    'row_count' => '{{row_count}}',
                )
            )
        );
        ?>
    </script>

    <script type="text/javascript">
        $(document).ready(function() {
            new OpenEyes.OphCiExamination.SystemicDiagnosesController({
                element: $('#<?=$model_name?>_element')
            });
        });
    </script>
</div>
