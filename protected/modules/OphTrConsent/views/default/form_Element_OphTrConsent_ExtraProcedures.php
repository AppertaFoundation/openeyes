<?php
$name = CHtml::modelName($element);
$extra_procedures = OphTrConsent_Extra_Procedure::model()->findAll();
$extra_procedure_itemsets = array_map(function ($proc) {
    return array(
        'label' => $proc->term,
        'id' => $proc->id
    );
}, $extra_procedures);
?>

<div class="element-fields full-width">
    <!-- to keep retain the extra procedure element -->
    <?= CHtml::hiddenField("{$name}[]", 1); ?>
    <div class="flex">
        <div class="cols-10">
            <table class="cols-full">
                <tbody id="js-extra-proc-entries">
                    <?php foreach ($element->extra_procedure_assignments as $i => $extra_procedure) {?>
                        <tr data-key="<?= $i ?>" class="js-proc-row">
                            <?= CHtml::hiddenField("{$name}[extra_procedure_assignments][$i][proc_id]", $extra_procedure->extra_proc_id); ?>
                            <td><?= $extra_procedure->extra_procedure->term ?></td>
                            <td><i class="oe-i trash"></i></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="add-data-actions flex-item-bottom ">
            <button id="js-add-extra-proc-btn" class="adder"></button>
        </div>
    </div>
</div>
<script type="text/template" class="hidden" id="js-new-extra-procedure-row">
    <tr data-key="{{key}}" class="js-proc-row">
        <input type="hidden" name="<?= $name ?>[extra_procedure_assignments][{{key}}][proc_id]" value="{{proc_id}}">
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
        'procedure_list' => $extra_procedure_itemsets,
        'laterality' => false,
        'search_source' => '/OphTrConsent/ExtraProcedures/autocomplete',
        'bind_add_button' => '#js-add-extra-proc-btn',
        'entry_container' => '#js-extra-proc-entries',
        'template' => '#js-new-extra-procedure-row',
        'is_extra_procedure' => true,
    )
);
?>
