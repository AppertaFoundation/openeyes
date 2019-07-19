<tr>
    <td><?= \CHtml::checkBox("delete-ids[{$set->id}]", false, ['value' => $set->id]); ?></td>
    <td><?=$set->id?></td>
    <td><?=$set->name?></td>
    <td><?=$set->rulesString()?></td>
    <td><?=$set->itemsCount()?></td>
    <td><?=$set->hidden ? \OEHtml::iconTick() : \OEHtml::iconRemove(['class' => 'medium']);?></td>
    <td><?=$set->automatic ? \OEHtml::iconTick() : \OEHtml::iconRemove(['class' => 'medium']);?></td>
    <td>
        <?php if(!$set->automatic): ?>
            <a href="/OphDrPrescription/admin/DrugSet/edit/<?=$set->id?>" class="button">Edit</a>
        <?php else: ?>
            <i class="oe-i info pad-left small js-has-tooltip"
               data-tooltip-content="Automatic set cannot be edited here."></i>
        <?php endif; ?>
    </td>
</tr>
