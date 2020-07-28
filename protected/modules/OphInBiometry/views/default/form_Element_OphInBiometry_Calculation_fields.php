<div class="data-group">
    <table class="cols-11 last-left">
        <colgroup>
            <col class="cols-4">
            <col class="cols-4">
            <col class="cols-4">
        </colgroup>
        <tbody>
        <tr>
            <td>
                Target Refraction:
            </td>
            <td>
                <?php
                if (!$element->{'target_refraction_' . $side}) {
                    $element->{'target_refraction_' . $side} = \OEModule\OphCiExamination\models\Element_OphCiExamination_CataractSurgicalManagement::getLatestTargetRefraction($this->patient, $side);
                }
                ?>
                <?php echo $form->textField(
                    $element,
                    'target_refraction_'.$side,
                    [
                        'nowrapper'=> true
                    ],
                    null,
                    array('label' => 4, 'field' => 2)
                )?>
            </td>
            <td>
                <button id="biometry-<?= $side ?>-comment-button"
                        class="button js-add-comments"
                        data-comment-container="#biometry-<?= $side ?>-comments"
                        type="button" style="<?= $element->{'comments_' . $side} ? 'visibility: hidden;' : '' ?>"
                >
                    <i class="oe-i comments small-icon"></i>
                </button>
            </td>
        </tr>
        </tbody>
    </table>
</div>
    <?php //echo $form->dropDownList($element, 'formula_id_'.$side, CHtml::listData(OphInBiometry_Calculation_Formula::model()->findAll(array('order'=> 'display_order asc')),'id','name'),array('empty'=>'Select'),null,array('label'=>3, 'field'=>6))?>
    <!--
    <div class="data-group">
        <div class="large-8 column">
            <table name="table" id="iol-table_<?php echo $side?>" align="center" cellspacing="0" width="200" style="margin-top: 10px">
                <thead>
                <tr>
                    <td align="left" width="60%"><h4 style="margin-left: 4px">IOL power</h4></td>
                    <td align="right" width="40%"><h4>Refraction</h4></td>
                </tr>
                </thead>
                <tbody id="tableBody_<?php echo $side?>">
                </tbody>
            </table>
        </div>
    </div>
    -->
