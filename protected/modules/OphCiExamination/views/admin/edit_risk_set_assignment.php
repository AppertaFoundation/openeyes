<?php
    /** @var OphCiExaminationRisk $model */
    $active_sets = array_map(function ($e) { return $e->id; }, $model->medicationSets);
?>

<h3>Related medication sets (check all that apply):</h3>
<table>
	<tbody>
	<?php foreach (MedicationSet::model()->byName()->findAll("hidden=0") as $set): ?>
    <?php /** @var MedicationSet $set */ ?>
	<tr>
		<td>
			<input id="chk_risk_set_<?=$set->id?>" type="checkbox" name="OEModule_OphCiExamination_models_OphCiExaminationRisk[medicationSets][]" value="<?=$set->id?>" <?php echo in_array($set->id, $active_sets) ? "checked" : "" ?> />
		</td>
		<td>
            <label for="chk_risk_set_<?=$set->id?>">
			<?php echo CHtml::encode($set->name); ?>
			<?php if($set->rulesString()) { echo "(".$set->rulesString().")"; } ?>
            </label>
		</td>
	</tr>
	<?php endforeach; ?>
	</tbody>
</table>