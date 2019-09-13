<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<?php
$key = 0;
$method_values = array();
foreach (OEModule\OphCiExamination\models\OphCiExamination_ColourVision_Method::model()->findAll() as $method) {
    $method_values[] = "'" . $method->id . "' : " . json_encode(CHtml::listData($method->values, 'id', 'name'));
}

?>
<div class="element-fields eye-divider">
    <div class="element-eyes">
        <script type="text/javascript">
            var colourVisionMethodValues = {
                <?php  echo implode(',', $method_values); ?>
            };
        </script>
        <?php echo $form->hiddenField($element, 'eye_id', array('class' => 'sideField')) ?>
        <?php foreach (array('left' => 'right', 'right' => 'left') as $page_side => $eye_side) : ?>
            <div class="js-element-eye <?= $eye_side ?>-eye column <?= $page_side ?> " data-side="<?= $eye_side ?>">
                <div class="active-form flex-layout" style="<?= $element->hasEye($eye_side) ? '' : 'display: none;' ?>">

                    <div class="remove-side"><i class="oe-i remove-circle small"></i></div>
                    <div class="cols-9">
                        <table class="cols-full standard colourvision_table_<?= $eye_side ?>"<?php if (!$element->{$eye_side . '_readings'}) {
                            ?> style="display: none;" <?php
                                                                            } ?>>
                            <thead>
                            <tr>
                                <th>Method</th>
                                <th>Value</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody class="plain" id="colourvision_right">
                            <?php foreach ($element->{$eye_side . '_readings'} as $reading) {
                                $this->renderPartial('form_OphCiExamination_ColourVision_Reading', array(
                                    'name_stub' => CHtml::modelName($element) . '[' . $eye_side . '_readings]',
                                    'reading' => $reading,
                                    'key' => $key,
                                    'side' => $eye_side,
                                    'method_name' => $reading->method->name,
                                    'method_id' => $reading->method->id,
                                ));
                                ++$key;
                            } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="add-data-actions flex-item-bottom" id="<?= $eye_side ?>-add-colour_vision_reading">
                        <button class="button hint green" id="add-procedure-btn-<?= $eye_side ?>" type="button">
                            <i class="oe-i plus pro-theme"></i>
                        </button>
                        <!-- oe-add-select-search -->
                    </div>
                </div>
                <div class="inactive-form" style="display: <?php if ($element->hasEye($eye_side)) {
                    ?> none <?php
                                                           } ?>">
                    <div class="add-side">
                        <a href="#">Add <?= $eye_side ?> side <span class="icon-add-side"></span></a>
                    </div>
                </div>
            </div>
            <script type="text/javascript">
                $(function () {
                    new OpenEyes.UI.AdderDialog({
                        openButton: $('#add-procedure-btn-<?= $eye_side?>'),
                        itemSets: [new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
                            array_map(function ($reading) {
                                return ['label' => $reading->name, 'id' => $reading->id];
                            }, $element->getAllReadingMethods($eye_side))) ?> , {'multiSelect': true}),
                        ],
                        onReturn: function (adderDialog, selectedItems) {
                            if (selectedItems.length) {
                                let eye_side = '<?=$eye_side?>';
                                let $table = $('.colourvision_table_' + eye_side);
                                $table.show();
                                OphCiExamination_ColourVision_addReading(selectedItems, eye_side, $table);
                                return true;
                            } else {
                                return false;
                            }
                        },
                        onOpen: function () {
                            $('#<?= $eye_side ?>-add-colour_vision_reading').find('li').each(function () {
                                let method_id = $(this).data('id');
                                let already_used = $('.colourvision_table_' + '<?= $eye_side ?>')
                                    .find('input[type="hidden"][name*="method_id"][value="' + method_id + '"]').length > 0;
                                $(this).toggle(!already_used);
                            });
                        },
                    });
                });
            </script>
        <?php endforeach; ?>
    </div>
</div>
<script id="colourvision_reading_template" type="text/html">
    <?php
    $this->renderPartial('form_OphCiExamination_ColourVision_Reading', array(
        'name_stub' => CHtml::modelName($element) . '[{{side}}_readings]',
        'key' => '{{key}}',
        'side' => '{{side}}',
        'method_name' => '{{method_name}}',
        'method_id' => '{{method_id}}',
        'method_values' => '{{& method_values}}',
    )) ?>
</script>