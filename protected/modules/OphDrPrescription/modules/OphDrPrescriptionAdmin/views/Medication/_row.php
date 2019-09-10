<tr>
    <td><?= \CHtml::checkBox("delete-ids[{$medication->id}]", false, ['value' => $medication->id]); ?></td>
    <td><?=$medication->id?></td>
    <td><?=$medication->source_type?></td>
    <td><?=$medication->source_subtype?></td>
    <td><?=$medication->preferred_code?></td>
    <td><?=$medication->preferred_term?></td>
    <td><?=$medication->vtm_term?></td>
    <td><?=$medication->vmp_term?></td>
    <td><?=$medication->amp_term?></td>
    <td>
        <a href="/OphDrPrescription/admin/Medication/edit/<?=$medication->id?>" class="button">Edit</a>
    </td>
</tr>
