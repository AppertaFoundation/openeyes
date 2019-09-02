<tr>
    <td><?= \CHtml::checkBox("delete-ids[{$set->id}]", false, ['value' => $set->id]); ?></td>
    <td><?=$set->id?></td>
    <td><?=$set->name?></td>
    <td><?=$set->itemsCount()?></td>
		<td></td>
    <td>
			<a href="/OphDrPrescription/admin/autoSetRule/edit/<?=$set->id?>" class="button">Edit</a>
			<a class="button js-list-medication" data-set_id="{{id}}">List medications</a>
    </td>
</tr>
