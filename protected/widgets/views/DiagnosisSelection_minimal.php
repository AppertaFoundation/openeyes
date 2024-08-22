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
<script type="text/javascript" src="<?= Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.widgets.js') . '/AutoCompleteSearch.js', true, -1); ?>"></script>
<?php $class_field = "{$class}_{$field}"; ?>

<?php if (!$nowrapper) {?>
    <div class="data-group diagnosis-selection">
        <div class="cols-<?php echo $layoutColumns['label'];?> column<?php if (!$label) {
            ?> hide<?php
                         }?>">
            <label for="<?php echo $class_field;?>">Diagnosis:</label>
        </div>
        <div class="cols-<?php echo $layoutColumns['field'];?> column end">
<?php }?>
            <?php
            $list_options = array('empty' => 'Select a commonly used diagnosis');

            if ($secondary_to) {
                $list_options['options'] = array();
                foreach ($secondary_to as $id => $lst) {
                    if (count($lst)) {
                        $list_options['options'][$id] = array();
                    }
                    $data = array();
                    $second_order = 1;
                    foreach ($lst as $sid => $term) {
                        $data[] = array('id' => $sid, 'term' => $term, 'order' => $second_order++);
                    }
                    $list_options['options'][$id]['data-secondary-to'] = CJSON::encode($data);
                }
            }

            $order = 1;
            foreach ($options as $i => $opt) {
                $list_options['options'][$i]['data-order'] = $order++;
            }
            ?>
            <?php echo !empty($options) ? CHtml::dropDownList("{$class}[$field]", '', $options, $list_options) : ''?>
<?php if (!$nowrapper) {?>
        </div>
    </div>
<?php }?>

    <?php if ($secondary_to) {?>
        <?php if (!$nowrapper) {?>
            <div id="div_<?php echo "{$class_field}_secondary_to"?>" class="data-group hidden">
                <?php if (!$nowrapper) {?>
                    <div class="cols-<?php echo $layoutColumns['label'];?> column<?php if (!$label) {
                        ?> hide<?php
                                     }?>">
                        <label for="<?php echo "{$class_field}_secondary_to";?>">Associated diagnosis:</label>
                    </div>
                <?php }?>
                <div class="cols-<?php echo $layoutColumns['field'];?> column end">
        <?php }?>
                <?=\CHtml::dropDownList("{$class}[{$field}_secondary_to]", '', array(), array())?>
        <?php if (!$nowrapper) {?>
                </div>
            </div>
        <?php }?>
    <?php }?>

    <?php if (!$nowrapper) {?>
        <div class="data-group">
            <?php if (!$nowrapper) {?>
                <div class="cols-<?php echo $layoutColumns['label'];?> column<?php if (!$label) {
                    ?> hide<?php
                                 }?>">
                    <label></label>
                </div>
            <?php }?>
            <div class="cols-<?php echo $layoutColumns['field'];?> column end">
    <?php }?>
                <div class="autocomplete-row" id="div_<?php echo "{$class}_{$field}_autocomplete_row"?>">
                    <?php
                        $this->widget('application.widgets.AutoCompleteSearch',
                            [
                                'field_name' => "{$class}[$field]",
                                'htmlOptions' =>
                                    [
                                        'placeholder' => $placeholder,
                                    ],
                                'layoutColumns' => ['field' => 12]
                            ]);
                        ?>
            </div>
    <?php if (!$nowrapper) {?>
            </div>
        </div>
    <?php }?>
<script type="text/javascript">
    function updatePrimaryList(disorder, secondary_to) {
        var html = '<option value="'+disorder.id+'" data-order="'+disorder.order+'"';
        if (secondary_to) {
            html += ' data-secondary-to="'+JSON.stringify(secondary_to).replace(/\"/g, "&quot;")+'"';
        }
        html += '>'+disorder.term+'</option>';

        var none = '';
        var empty = '';
        $('#<?= $class_field ?>').children().each(function() {
            if ($(this).val() == 'NONE') {
                none = $(this)[0].outerHTML;
            }
            else if ($(this).val()) {
                html += $(this)[0].outerHTML;
            }
            else {
                empty = $(this)[0].outerHTML;
            }
        });
        // sort_selectbox keeps the first element at the top.
        $('#<?= $class_field ?>').html(empty + html);
        sort_selectbox($('#<?= $class_field ?>'));
        //prepend none
        $('#<?= $class_field ?> option').eq(1).before($(none));
    }

    <?php if ($secondary_to) {?>
    function updateSecondaryList(data, include_none) {
        var options = '<option value="">Select</option>';
        if (include_none) {
            options += '<option value="NONE">None</option>';
        }
        data.sort(function(a, b) { return a.order < b.order ? -1 : 1});
        for (var i in data) {
            if (data[i].id == 'NONE') {
                options += '<option value="' + data[i].id + '">' + data[i].term + '</option>';
            }
        }
        for (var i in data) {
            if (data[i].id != 'NONE' && $('input[type="hidden"][name="selected_diagnoses[]"][value="' + data[i].id + '"]').length == 0) {
                options += '<option value="' + data[i].id + '">' + data[i].term + '</option>';
            }
        }
        $('#<?= "{$class_field}_secondary_to"?>').html(options);
    }

    $('#<?= "{$class_field}_secondary_to"?>').change(function() {
        var primary_selected = $('#<?= $class_field ?>').children('option:selected');
        var selected = $(this).children('option:selected');
        if (selected.val()) {
            if (primary_selected.val() != 'NONE') {
                <?= $callback?>(primary_selected.val(), primary_selected.text());
            }
            if (selected.val() != 'NONE') {
                <?= $callback?>(selected.val(), selected.text());
            }
            $('#div_<?= "{$class_field}_secondary_to"?>').hide();
            if (primary_selected.val() != 'NONE') {
                primary_selected.remove();
            }
            $('#<?php echo $class?>_<?php echo $field?>').val('');
        }
    });
    <?php }?>

    <?php if ($secondary_to || $callback) {?>
        $('#<?php echo $class?>_<?php echo $field?>').change(function() {
            if ($(this).children('option:selected').val()) {
                var selected = $(this).children('option:selected');
                <?php if ($secondary_to) {?>
                    if (selected.data('secondary-to')) {
                        updateSecondaryList(selected.data('secondary-to'), selected.val() != 'NONE');
                        $('#div_<?= "{$class_field}_secondary_to"?>').show();
                    }
                    else {
                        $('#div_<?= "{$class_field}_secondary_to"?>').hide();
                        <?php echo $callback?>(selected.val(), selected.text());
                        selected.remove();
                        $('#<?= $class_field ?>').val('');
                    }
                <?php } else {?>
                    <?php echo $callback?>(selected.val(), selected.text());
                    selected.remove();
                    $('#<?= $class_field ?>').val('');
                <?php }?>
            }
            else {
                // reset form
                $('#div_<?= "{$class_field}_secondary_to"?>').hide();
            }
        });
    <?php }?>
</script>

<script type="text/javascript">
    OpenEyes.UI.AutoCompleteSearch.init({
        input: $(`[id="${'<?= "{$class}[$field]" ?>'}"]`),
        url: '/disorder/autocomplete',
        params: {
            'code': function () {return "<?= $code ?>"},
        },
        maxHeight: '200px',
        onSelect: function() {
            let response = OpenEyes.UI.AutoCompleteSearch.getResponse();
            let input = OpenEyes.UI.AutoCompleteSearch.getInput();

            const callback = '<?= $callback ?>';

            if ($('#DiagnosisSelection_disorder_id_secondary_to').is(':visible')) {
                var primary_selected = $('#classField').children('option:selected');
                if (primary_selected.val() != 'NONE') {
                    if (callback)
                        <?= $callback?>(primary_selected.val(), primary_selected.text());
                }
            }
            if (callback)
                <?= $callback?>(response.id, response.value);
            $(`[id="${'<?= "{$class}[$field]" ?>'}"]`).val('');
            $('#<?= $class_field ?>').children('option').map(function() {
                if ($(this).val() == response.id) {
                    $(this).remove();
                }
            });
        }
    });
</script>
