<?php
$subspecialty_id = $this->firm->getSubspecialtyID();
$subspecialty_procedures = ProcedureSubspecialtyAssignment::model()->getProcedureListFromSubspecialty($subspecialty_id);
$procedure_itemsets = array_map(function ($key, $proc) {
    return array(
        'label' => $proc,
        'id' => $key
    );
}, array_keys($subspecialty_procedures), $subspecialty_procedures);
$right_eye_id = Eye::RIGHT;
$left_eye_id = Eye::LEFT;
$both_eye_id = Eye::BOTH;
$laterality_option = array(
    array(
        'id' => $right_eye_id,
        'label' => 'Right Eye',
        'name' => 'right',
        'right' => 'R',
        'left' => 'NA'
    ),
    array(
        'id' => $left_eye_id,
        'label' => 'Left Eye',
        'name' => 'left',
        'right' => 'NA',
        'left' => 'L'
    ),
    array(
        'id' => $both_eye_id,
        'label' => 'Right & Left Eyes',
        'name' => 'both',
        'right' => 'R',
        'left' => 'L'
    ),
);
?>
<div class="cols-10">
    <table class="cols-full">
        <colgroup>
            <col class="cols-3">
        </colgroup>
        <tbody id="js-proc-entries">
            <?php foreach ($element->procedure_assignments as $i => $procedure) {
                $eye_id = (int)$procedure->eye_id;
                ?>
                <tr data-key="<?=$i?>" class="js-proc-row">
                    <?=CHtml::hiddenField("{$name}[procedure_assignments][$i][proc_id]", $procedure->proc_id);?>
                    <?=CHtml::hiddenField("{$name}[procedure_assignments][$i][eye_id]", $eye_id);?>
                    <td>
                        <span class="oe-eye-lat-icons">
                            <i class="oe-i laterality <?=$eye_icons[$eye_id]['right']?> small pad"></i>
                            <i class="oe-i laterality <?=$eye_icons[$eye_id]['left']?> small pad"></i>
                        </span>
                    </td>
                    <td><?=$procedure->proc->term?></td>
                    <td>
                        <i class="oe-i trash"></i>
                    </td>
                </tr>
            <?php }?>
            <?php foreach ($element->additionalprocedure_assignments as $procedure) {
                $eye_id = (int)$procedure->eye_id;
                ?>
                <tr>
                    <td>
                        <span class="oe-eye-lat-icons">
                            <i class="oe-i laterality <?=$eye_icons[$eye_id]['right']?> small pad"></i>
                            <i class="oe-i laterality <?=$eye_icons[$eye_id]['left']?> small pad"></i>
                        </span>
                    </td>
                    <td><?=$procedure->proc->term?></td>
                    <td>
                        Legacy Additional Procedure
                    </td>
                </tr>
            <?php }?>
            <tr class="js-anaesthetic-row">
                <th>Anaesthetic</th>
                <td>
                <?= $form->checkBoxes(
                    $element,
                    'AnaestheticType',
                    'anaesthetic_type',
                    null,
                    false,
                    false,
                    false,
                    false,
                    array(
                        'fieldset-class' => $element->getError('anaesthetic_type') ? 'highlighted-error error' : '',
                        'field'=>'AnaestheticType',
                    )
                ); ?>
                </td>
                <td></td>
            </tr>
        </tbody>
    </table>        
</div>
<div class="add-data-actions flex-item-bottom ">
    <button id="js-add-proc-btn" class="adder" data-popup="add-to-idg-consent-procedures-additional-unbooked"></button>         
</div>
<script type="text/template" class="hidden" id="js-new-procedure-row">
    <tr data-key="{{key}}" class="js-proc-row">
        <input type="hidden" name="<?=$name?>[procedure_assignments][{{key}}][proc_id]" value="{{proc_id}}">
        <input type="hidden" name="<?=$name?>[procedure_assignments][{{key}}][eye_id]" value="{{eye_id}}">
        <td>
            <span class="oe-eye-lat-icons">
                <i class="oe-i laterality {{right}} small pad"></i>
                <i class="oe-i laterality {{left}} small pad"></i>
            </span>
        </td>

        <td>
            {{term}}
        </td>
        <td>
            <i class="oe-i trash"></i>
        </td>
    </tr>
</script>
<?php
    $this->renderPartial(
        'procedure_selection',
        array(
            'procedure_list' => $procedure_itemsets,
            'laterality' => $laterality_option,
            'search_source' => '/procedure/autocomplete',
            'bind_add_button' => '#js-add-proc-btn',
            'entry_container' => '#js-proc-entries',
            'template' => '#js-new-procedure-row',
            'aneathetic_row' => '.js-anaesthetic-row',
        )
    );
    ?>
