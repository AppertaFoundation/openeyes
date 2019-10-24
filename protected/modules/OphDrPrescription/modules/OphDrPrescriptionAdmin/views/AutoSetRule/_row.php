<tr>
    <td><?= \CHtml::checkBox("delete-ids[{$set->id}]", false, ['value' => $set->id]); ?></td>
    <td><?=$set->id?></td>
    <td><?=$set->name?></td>
    <td><?=$set->itemsCount()?></td>
		<td></td>
    <td>
			<a href="/OphDrPrescription/admin/autoSetRule/edit/<?=$set->id?>" class="button">Edit</a>
      <a href="/OphDrPrescription/admin/autoSetRule/listMedications?set_id=<?=$set->id?>" class="button">List medications</a>
    </td>
</tr>
