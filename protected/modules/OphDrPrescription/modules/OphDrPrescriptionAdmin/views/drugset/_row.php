<tr>
    <td><?= \CHtml::checkBox('delete-ids[]', ['value' => $set->id]); ?></td>
    <td><?=$set->id?></td>
    <td><?=$set->name?></td>
    <td><?=$set->rulesString()?></td>
    <td><?=$set->itemsCount()?></td>
    <td><?=$set->hidden ? "Yes" : "No";?></td>
    <td>
        <a href="/OphDrPrescription/refSetAdmin/edit/<?=$set->id?>" class="button">Edit</a>
        <a href="/OphDrPrescription/refMedicationSetAdmin/list?ref_set_id=<?=$set->id?>" class="button">List medications</a>
    </td>
</tr>