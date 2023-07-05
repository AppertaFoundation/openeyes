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
<div class="diagnosis-selection">
    <div id="<?php echo $class?>_<?php echo $field?>_enteredDiagnosisText"<?php if (!$label) {
        ?> style="display: none;" <?php
             }?>>
        <h4>
            <?php echo $label?>
        </h4>
    </div>
    <div class="dropdown-row">
        <?=\CHtml::dropDownList("{$class}[$field]", '', $options, array('empty' => 'Select a commonly used diagnosis'))?>
    </div>
    <div class="autocomplete-row">
        <?php
            $this->widget('application.widgets.AutoCompleteSearch',
                [
                    'field_name' => "{$class}[$field]",
                    'htmlOptions' =>
                        [
                            'placeholder' => 'or type the first few characters of a diagnosis',
                        ],
                    'layoutColumns' => ['field' => '12']
                ]);
            ?>
    </div>
</div>
<input type="hidden" name="<?php echo $class?>[<?php echo $field?>]"
    id="<?php echo $class?>_<?php echo $field?>_savedDiagnosis" value="<?php echo $value?>" />
<script type="text/javascript">
    $('#<?php echo $class?>_<?php echo $field?>').change(function() {
        $('#<?php echo $class?>_<?php echo $field?>_enteredDiagnosisText h4').html($('option:selected', this).text());
        $('#<?php echo $class?>_<?php echo $field?>_savedDiagnosis').val($(this).val());
        $('#<?php echo $class?>_<?php echo $field?>_enteredDiagnosisText').show();
    });

    $(document).ready(function() {
        let element = '<?= "{$class}_$field" ?>';

        OpenEyes.UI.AutoCompleteSearch.init({
            input: $(`[id="${'<?= "{$class}[$field]" ?>'}"]`),
            url: '/disorder/autocomplete',
            params: {
                'code': function () {return "<?= $code ?>"},
            },
            maxHeight: '200px',
            onSelect: function () {
                let response = OpenEyes.UI.AutoCompleteSearch.getResponse();
                let input = OpenEyes.UI.AutoCompleteSearch.getInput();

                $(`[id="${'<?= "{$class}_$field" ?>'}"]`).val('');
                $(`[id="${element}_enteredDiagnosisText"] h4`).html(response.value);
                $(`[id="${element}_enteredDiagnosisText"]`).show();
                $(`input[id="${element}_savedDiagnosis"]`).val(response.id);
                $(`[id="${element}"]`).focus();
            }
        });
    });
</script>
