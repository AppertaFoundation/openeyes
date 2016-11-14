<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>
<div class="row field-row">
    <div class="large-<?php echo $layoutColumns['label']; ?> column">
        <label for="<?php echo "{$class}_{$field}"; ?>">Diagnosis:</label>
    </div>
    <div class="large-<?php echo $layoutColumns['field']; ?> column end">

        <!-- Here we show the selected diagnosis -->
        <div id="enteredDiagnosisText" class="panel diagnosis<?php if (!$label) { ?> hide<?php } ?>">
            <?php echo $label ?>
            <?php
            $clear_diagnosis = '';
            if ($allowClear) {
                $clear_diagnosis = Chtml::link('(Remove)', "#", array("id" => "clear-diagnosis-widget"));
                echo $clear_diagnosis;
            } ?>
        </div>

        <div class="field-row">
            <?php echo CHtml::dropDownList("{$class}[$field]", '', $options, array('empty' => 'Select a commonly used diagnosis')) ?>
        </div>
        <div class="field-row">
            <?php
            $this->render('disorderAutoComplete', array(
                'class' => $class,
                'name' => $field,
                'code' => $code,
                'clear_diagnosis' => $clear_diagnosis,
                'placeholder' => 'or type the first few characters of a diagnosis',
            ));
            ?>
            <input type="hidden" name="<?php echo $class ?>[<?php echo $field ?>]" id="savedDiagnosis" value="<?php echo $value ?>"/>
        </div>
    </div>
</div>

<script type="text/javascript">
    $('#<?php echo $class?>_<?php echo $field?>').change(function () {
        $('#enteredDiagnosisText').html($('option:selected', this).text()
            <?php if ($allowClear) { ?>
            + ' <?php echo $clear_diagnosis ?>'
            <?php } ?>
        );
        $('#enteredDiagnosisText').show();
        $('#savedDiagnosis').val($(this).val());
    });
</script>
