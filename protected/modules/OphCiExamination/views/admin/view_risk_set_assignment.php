<?php
/** @var OphCiExaminationRisk $model */
$active_sets = array_map(function ($e) {
    return $e->id;
}, $model->medicationSets);
?>

<table id="medication-sets-list">
    <tbody>
    <?php foreach ($model->medicationSets as $set) : ?>
        <?php /** @var MedicationSet $set */ ?>
        <tr>
            <td>
                <label for="chk_risk_set_<?= $set->id ?>">
                    <?php echo CHtml::encode($set->name); ?>
                    <?php if ($set->rulesString()) {
                        echo "(" . $set->rulesString() . ")";
                    } ?>
                </label>
                <input type="hidden" id="chk_risk_set_<?= $set->id ?>"
                       name="OEModule_OphCiExamination_models_OphCiExaminationRisk[medicationSets][]"
                       value="<?= $set->id ?>">
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>