<tr data-row="<?=$index?>" >
    <td>
        <?php echo CHtml::activeHiddenField($instruction, "[$index]id");  ?>
        <?php echo CHtml::activeTextField($instruction, "[$index]content");  ?>
    </td>
    <td>
        <?php echo CHtml::activeDropDownList($instruction, "[$index]site_id", CHtml::listData(Site::model()->findAll(),'id', 'name')) ?>
    </td>
    <td>
        <?php echo CHtml::activeDropDownList($instruction, "[$index]subspecialty_id", CHtml::listData(Subspecialty::model()->findAll(),'id', 'name')) ?>
    </td>
    <td class="actions">
        <div class="wrapper">
            <?php if($instruction->isNewRecord):?>
                <a href="javascript:void(0)" class="save">save</a>
            <?php else: ?>
                <a href="javascript:void(0)" class="save">save</a> | <a href="javascript:void(0)" class="delete">delete</a>
            <?php endif; ?>
        </div>
    </td>
</tr>