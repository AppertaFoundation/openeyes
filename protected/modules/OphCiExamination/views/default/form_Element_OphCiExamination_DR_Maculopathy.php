<?php

use OEModule\OphCiExamination\models\Element_OphCiExamination_DR_Maculopathy;
use OEModule\OphCiExamination\models\OphCiExamination_DRGrading_Feature;

/**
 * @var $element Element_OphCiExamination_DR_Maculopathy
 */
$key = 0;
$m0_maculopathy_feature = OphCiExamination_DRGrading_Feature::model()->findByAttributes(array('grade' => 'M0', 'active' => 1));
$m1_maculopathy_features = OphCiExamination_DRGrading_Feature::model()->findAllByAttributes(array('grade' => 'M1', 'active' => 1));
?>
<?php echo $form->hiddenInput($element, 'eye_id', $element->eye_id, array('class' => 'sideField'))?>
<div class="element-fields element-eyes">
    <?php foreach (array('left' => 'right', 'right'=> 'left') as $page_side => $eye_side) {
        $eye = null;
        if ($eye_side === 'left') {
            $eye = Eye::LEFT;
        } else {
            $eye = Eye::RIGHT;
        }
        ?>
        <div class="js-element-eye <?= $eye_side ?>-eye <?= $page_side ?> column" data-side="<?= $eye_side ?>">
            <div class="active-form" style="<?= $element->hasEye($eye_side) ? '' : 'display: none;' ?>">
                <div class="remove-side">
                    <i class="oe-i remove-circle small"></i>
                </div>
                <div class="flex-layout">
                    <table class="cols-10 last-left" id="js-maculopathy-feature-list">
                        <colgroup>
                            <col class="cols-3"/>
                        </colgroup>
                        <tbody>
                        <?php foreach ($element->{$eye_side . '_maculopathy_features'} as $i => $maculopathy_feature) {
                            if ($element->hasEye($eye_side)) {
                                $this->renderPartial(
                                    'form_MaculopathyFeature',
                                    array(
                                        'name_stub' => CHtml::modelName($element) . '[' . $eye_side . '_maculopathy_features]',
                                        'maculopathy_feature' => $maculopathy_feature,
                                        'key' => $key,
                                        'side' => $eye_side,
                                        'eye' => $eye
                                    )
                                );
                                ++$key;
                            }
                        }?>
                        </tbody>
                    </table>
                    <div class="add-data-actions flex-item-bottom">
                        <button class="button hint green js-add-select-search" id="add-to-dr-maculopathy-<?= $eye_side ?>">
                            <i class="oe-i plus pro-theme"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="inactive-form side" style="<?= $element->hasEye($eye_side) ? 'display: none;' : '' ?>">
                <div class="add-side">
                    <a href="#">Add <?= $eye_side ?> side</a>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            new OpenEyes.UI.AdderDialog({
                openButton: $("#add-to-dr-maculopathy-<?= $eye_side ?>"),
                itemSets: [
                    new OpenEyes.UI.AdderDialog.ItemSet([
                        {label: '<?= $m0_maculopathy_feature->name ?>', id: <?= $m0_maculopathy_feature->id ?>}
                    ], {'multiSelect': false, 'id': 'add-to-maculopathy-m0', 'header': 'M0'}),
                    new OpenEyes.UI.AdderDialog.ItemSet(
                        <?= CJSON::encode(array_map(
                            static function ($feature) {
                                return [
                                    'label' => $feature->name,
                                    'id' => $feature->id,
                                ];
                            },
                            $m1_maculopathy_features
                        ))?>,
                        {'multiSelect': false, 'id': 'add-to-maculopathy-m1', 'header': 'M1'}
                    ),
                ],
                liClass: 'auto-width',
                onSelect: function () {
                    // If the selected option is 'nil', display all other columns; otherwise hide all other columns.
                    let $adderDialog = $(this).closest('.oe-add-select-search');
                    if ($(this).data("label") === 'Nil') {
                        if ($(this).attr("class") === 'selected') {
                            // Selected, so show the MA column
                            $($adderDialog).find('th[data-id = "add-to-maculopathy-m1"]').show();
                            $($adderDialog).find('ul[data-id = "add-to-maculopathy-m1"]').closest('td').show();
                        } else {
                            // Deselected, so hide the MA column
                            $($adderDialog).find('th[data-id = "add-to-maculopathy-m1"]').hide();
                            $($adderDialog).find('ul[data-id = "add-to-maculopathy-m1"]').closest('td').hide();
                        }
                    }
                },
                onReturn: function(adderDialog, selectedItems) {
                    // Add selected items to list of features, then determine the DR grade based on the selected features.
                    addDRFeature(
                        $(".<?= $eye_side ?>-eye #js-maculopathy-feature-list"),
                        selectedItems,
                        $(".<?= CHtml::modelName($element) ?> .entry-template").text(),
                        '<?= $eye_side ?>'
                    );
                }
            });
            $(document).ready(function() {
                $("#js-maculopathy-feature-list tbody").on('click', ".trash", function(){
                    $(this).closest("tr").remove();
                });
            });
        </script>
    <?php }?>
    <script type="text/template" class="entry-template hidden">
        <tr>
            <td>
                {{grade}}
            </td>
            <td>
                {{name}}
                <input type="hidden" name="<?= CHtml::modelName($element) ?>[{{side}}_maculopathy_features][{{index}}][feature_id]" value="{{id}}"/>
            </td>
            <td>
                <i class="oe-i trash"></i>
            </td>
        </tr>
    </script>
</div>
