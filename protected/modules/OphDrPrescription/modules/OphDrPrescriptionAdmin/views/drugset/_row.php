<tr>
    <td><?= \CHtml::checkBox('delete-ids[]', false, ['value' => $set->id]); ?></td>
    <td><?=$set->id?></td>
    <td><?=$set->name?></td>
    <td><?=$set->rulesString()?></td>
    <td><?=$set->itemsCount()?></td>
    <td><?=$set->hidden ? "Yes" : "No";?></td>
    <td><a href="/OphDrPrescription/admin/drugset/edit/<?=$set->id?>" class="button">Edit</a></td>
</tr>
