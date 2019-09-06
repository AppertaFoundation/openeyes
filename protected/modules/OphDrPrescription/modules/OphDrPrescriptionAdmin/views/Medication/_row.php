<tr>
    <td><?= \CHtml::checkBox("delete-ids[{$set->id}]", false, ['value' => $set->id]); ?></td>
    <td><?=$set->id?></td>
    <td><?=$set->source_type?></td>
    <td><?=$set->source_subtype?></td>
    <td><?=$set->preferred_code?></td>
    <td><?=$set->preferred_term?></td>
    <td><?=implode(', ', array_map( function($medicationSearchIndex) { return $medicationSearchIndex->alternative_term; }, $set->medicationSearchIndexes))?></td>
    <td><?=$set->vtm_term?></td>
    <td><?=$set->vmp_term?></td>
    <td><?=$set->amp_term?></td>
    <td>
        <a href="/OphDrPrescription/admin/Medication/edit/<?=$set->id?>" class="button">Edit</a>
    </td>
</tr>
