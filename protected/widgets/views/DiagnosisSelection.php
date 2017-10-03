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
                $clear_diagnosis = Chtml::link('(Remove)', "#", array("class" => "clear-diagnosis-widget"));
                echo $clear_diagnosis;
            } ?>
        </div>

        <div class="field-row">
            <?php echo CHtml::dropDownList("{$class}[$field]", '', $options, array('empty' => 'Select a commonly used diagnosis')) ?>
        </div>
        <div class="field-row">
            <?php
            $this->controller->renderPartial('//disorder/disorderAutoComplete', array(
                'class' => $class,
                'name' => $field,
                'code' => $code,
                'clear_diagnosis' => $clear_diagnosis,
                'value' => $value,
                'placeholder' => 'or type the first few characters of a diagnosis',
                'form' => $form,
                'callback' => $callback
            ));
            ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    $('#<?php echo $class?>_<?php echo $field?>').change(function () {
      var selected = $('option:selected', this);
      select(undefined, {item: {id: selected.val(), value: selected.text()}});
    });
</script>
