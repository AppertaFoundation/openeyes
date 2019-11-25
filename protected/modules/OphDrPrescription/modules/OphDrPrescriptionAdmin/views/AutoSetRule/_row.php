<tr class="clickable">
    <td><?= \CHtml::checkBox("delete-ids[{$set->id}]", false, ['value' => $set->id]); ?></td>
    <td><?=$set->id?></td>
    <td><?=$set->name?></td>
    <td>
        <?php
        // because of the usage_code condition the string will not contain all the rules
        $alternate_set = MedicationSet::model()->findByPk($set->id);
        echo ($alternate_set ? $alternate_set->rulesString() : '-');
        ?>
    </td>
    <td><?=$set->itemsCount()?></td>
    <td><?= $set->hidden ? \OEHtml::iconTick() : \OEHtml::iconRemove(['class' => 'medium']); ?></td>
    <td>
            <a href="/OphDrPrescription/admin/autoSetRule/edit/<?=$set->id?>" class="button">Edit</a>
      <a href="/OphDrPrescription/admin/autoSetRule/listMedications?set_id=<?=$set->id?>" class="button">List medications</a>
    </td>
</tr>
