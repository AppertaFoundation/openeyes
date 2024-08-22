<?php

use OEModule\OphCiExamination\models\Element_OphCiExamination_DR_Retinopathy;
use OEModule\OphCiExamination\models\OphCiExamination_DRGrading_Feature;

    /**
 * @var $element Element_OphCiExamination_DR_Retinopathy
 */

$key = 0;
$r0_retinopathy_feature = OphCiExamination_DRGrading_Feature::model()->findByAttributes(array('grade' => 'R0', 'active' => 1));
$r1_retinopathy_features = OphCiExamination_DRGrading_Feature::model()->findAllByAttributes(array('grade' => 'R1', 'active' => 1));
$r2_retinopathy_features = OphCiExamination_DRGrading_Feature::model()->findAllByAttributes(array('grade' => 'R2', 'active' => 1));
$r3s_retinopathy_features = OphCiExamination_DRGrading_Feature::model()->findAllByAttributes(array('grade' => 'R3s', 'active' => 1));
$r3a_retinopathy_features = OphCiExamination_DRGrading_Feature::model()->findAllByAttributes(array('grade' => 'R3a', 'active' => 1));
?>
<?php echo $form->hiddenInput($element, 'eye_id', $element->eye_id, array('class' => 'sideField'))?>
<div class="element-fields element-eyes">
    <?php foreach (array('left' => 'right', 'right' => 'left') as $page_side => $eye_side) {
        $eye = null;
        if ($eye_side === 'left') {
            $eye = Eye::LEFT;
        } else {
            $eye = Eye::RIGHT;
        }
        ?>
        <?= $form->hiddenField($element, $eye_side.'_overall_grade') ?>
        <div class="js-element-eye <?= $eye_side ?>-eye <?= $page_side ?> column" data-side="<?= $eye_side ?>" data-test="dr-retinopathy-side">
            <div class="active-form" style="<?= $element->hasEye($eye_side) ? '' : 'display: none;' ?>">
                <div class="remove-side">
                    <i class="oe-i remove-circle small"></i>
                </div>
                <div class="flex-layout">
                    <table class="cols-10 last-left" id="js-retinopathy-feature-list">
                        <colgroup>
                            <col class="cols-3"/>
                        </colgroup>
                        <tbody>
                        <?php foreach ($element->{$eye_side . '_retinopathy_features'} as $i => $retinopathy_feature) {
                            if ($element->hasEye($eye_side)) {
                                $this->renderPartial(
                                    'form_RetinopathyFeature',
                                    array(
                                        'name_stub' => CHtml::modelName($element) . '[' . $eye_side . '_retinopathy_features]',
                                        'retinopathy_feature' => $retinopathy_feature,
                                        'key' => $key,
                                        'side' => $eye_side,
                                        'eye' => $eye
                                    )
                                );
                                ++$key;
                            }
                        } ?>
                        </tbody>
                    </table>
                    <div class="add-data-actions flex-item-bottom">
                        <button class="button hint green js-add-select-search" id="add-to-dr-retinopathy-<?= $eye_side ?>" data-test="add-to-dr-retinopathy-<?= $eye_side ?>-btn">
                            <i class="oe-i plus pro-theme"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="inactive-form side" style="<?= $element->hasEye($eye_side)? 'display: none;': ''?>">
                <div class="add-side">
                    <a href="#">Add <?= $eye_side ?> side</a>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            new OpenEyes.UI.AdderDialog({
                openButton: $("#add-to-dr-retinopathy-<?= $eye_side ?>"),
                itemSets: [
                    new OpenEyes.UI.AdderDialog.ItemSet([
                        {label: '<?= $r0_retinopathy_feature->name ?>', 'id': "<?= $r0_retinopathy_feature->id ?>-<?= $eye_side ?>"},
                        {label: 'DR', 'id': 'dr-option-<?= $eye_side ?>'}
                    ], {'multiSelect': false, 'id': 'add-to-retinopathy-dr-<?= $eye_side ?>', 'header': 'DR'}),
                    new OpenEyes.UI.AdderDialog.ItemSet(
                        <?= CJSON::encode(array_map(
                            static function ($feature) {
                                return [
                                    'label' => $feature->name,
                                    'id' => $feature->id,
                                ];
                            },
                            $r1_retinopathy_features
                        ))?>,
                        {'multiSelect': true, 'id': 'add-to-retinopathy-r1-<?= $eye_side ?>', 'hideByDefault': true, 'header': 'R1'}
                    ),
                    new OpenEyes.UI.AdderDialog.ItemSet(
                        [
                            {label: '1 MA', 'id': '1'},
                            {label: '2 MA', 'id': '2'},
                            {label: '3 MA', 'id': '3'},
                            {label: '4 MA', 'id': '4'},
                            {label: '5+ MA', 'id': '5+'}
                        ],
                        {'multiSelect': false, 'id': 'add-to-retinopathy-ma-<?= $eye_side ?>', 'hideByDefault': true, 'header': '(MA)'}
                    ),
                    new OpenEyes.UI.AdderDialog.ItemSet(
                        <?= CJSON::encode(array_map(
                            static function ($feature) {
                                return [
                                    'label' => $feature->name,
                                    'id' => $feature->id,
                                ];
                            },
                            $r2_retinopathy_features
                        ))?>,
                        {'multiSelect': true, 'id': 'add-to-retinopathy-r2-<?= $eye_side ?>', 'hideByDefault': true, 'header': 'R2'}
                    ),
                    new OpenEyes.UI.AdderDialog.ItemSet(
                        <?= CJSON::encode(array_map(
                            static function ($feature) {
                                return [
                                    'label' => $feature->name,
                                    'id' => $feature->id,
                                ];
                            },
                            $r3s_retinopathy_features
                        ))?>,
                        {'multiSelect': true, 'id': 'add-to-retinopathy-r3s-<?= $eye_side ?>', 'hideByDefault': true, 'header': 'R3 Stable'}
                    ),
                    new OpenEyes.UI.AdderDialog.ItemSet(
                        <?= CJSON::encode(array_map(
                            static function ($feature) {
                                return [
                                    'label' => $feature->name,
                                    'id' => $feature->id,
                                ];
                            },
                            $r3a_retinopathy_features
                        ))?>,
                        {'multiSelect': true, 'id': 'add-to-retinopathy-r3a-<?= $eye_side ?>', 'hideByDefault': true, 'header': 'R3 Active'}
                    ),
                ],
                liClass: 'auto-width',
                onReturn: function(adderDialog, selectedItems) {
                    // Add selected items to list of features, then determine the DR grade based on the selected features.
                    addDRFeature(
                        $(".<?= $eye_side ?>-eye #js-retinopathy-feature-list"),
                        selectedItems,
                        $(".<?= CHtml::modelName($element) ?> .entry-template").text(),
                        '<?= $eye_side ?>'
                    );
                },
                onOpen: function (adderDialog) {
                    if ($(adderDialog).find('ul[data-id = "add-to-retinopathy-dr-<?= $eye_side ?>"] .selected').data('label') === 'No DR') {
                        // 'No DR' has been selected, so hide all the columns except for the first one when dialog is opened
                        adderDialog.toggleColumnById(
                            [
                                'add-to-retinopathy-r1-<?= $eye_side ?>',
                                'add-to-retinopathy-ma-<?= $eye_side ?>',
                                'add-to-retinopathy-r2-<?= $eye_side ?>',
                                'add-to-retinopathy-r3s-<?= $eye_side ?>',
                                'add-to-retinopathy-r3a-<?= $eye_side ?>'
                            ],
                            false
                        );
                    }
                },
                onSelect: function() {
                    // If the selected option is 'DR', display all other columns; otherwise hide all other columns.
                    let $adderDialog = $(this).closest('.oe-add-select-search');
                    if ($(this).data("label") === 'DR' && $(this).attr("class") !== 'selected') {
                        // DR Selected, so show all the other options
                        $($adderDialog).find('th:not(:first)').show();
                        $($adderDialog).find('ul:not(:first)').closest('td').show();
                        // If MA option has not been selected, hide the MA column
                        if ($($adderDialog).find('ul[data-id = "add-to-retinopathy-ma-<?= $eye_side ?>"]').children('.selected').length === 0) {
                            $($adderDialog).find('th[data-id = "add-to-retinopathy-ma-<?= $eye_side ?>"]').hide();
                            $($adderDialog).find('ul[data-id = "add-to-retinopathy-ma-<?= $eye_side ?>"]').closest('td').hide();
                        }
                    } else if ($(this).data("label") === 'No DR' && $(this).attr("class") !== 'selected') {
                        // 'No DR' selected, so hide all the other options
                        $($adderDialog).find('th:not(:first)').hide();
                        $($adderDialog).find('ul:not(:first)').closest('td').hide();
                    } else if ($(this).data("label") === 'MA') {
                        if ($(this).attr("class") !== 'selected') {
                            // Selected, so show the MA column
                            $($adderDialog).find('th[data-id = "add-to-retinopathy-ma-<?= $eye_side ?>"]').show();
                            $($adderDialog).find('ul[data-id = "add-to-retinopathy-ma-<?= $eye_side ?>"]').closest('td').show();
                        } else {
                            // Deselected, so hide the MA column
                            $($adderDialog).find('th[data-id = "add-to-retinopathy-ma-<?= $eye_side ?>"]').hide();
                            $($adderDialog).find('ul[data-id = "add-to-retinopathy-ma-<?= $eye_side ?>"]').closest('td').hide();
                        }
                    }
                }
            });

            $(document).ready(function() {
                $("#js-retinopathy-feature-list tbody").on('click', ".trash", function(){
                    $(this).closest("tr").remove();
                });
            });
        </script>
    <?php }?>
    <script type="text/template" class="entry-template hidden">
        <tr data-test="dr-rentinopathy-row">
            <td>
                {{grade}}
            </td>
            <td>
                {{name}}
                <input type="hidden" name="<?= CHtml::modelName($element) ?>[{{side}}_retinopathy_features][{{index}}][feature_id]" value="{{id}}"/>
                <input
                   type="hidden"
                   name="<?= CHtml::modelName($element) ?>[{{side}}_retinopathy_features][{{index}}][feature_count]"
                   value="{{feature_count}}"/>
            </td>
            <td>
                <i class="oe-i trash"></i>
            </td>
        </tr>
    </script>
</div>
