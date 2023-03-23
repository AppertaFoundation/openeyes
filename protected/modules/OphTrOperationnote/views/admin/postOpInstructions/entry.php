<tr data-row="<?=$index?>" >
    <td>
        <?=\CHtml::activeHiddenField($instruction, "[$index]id");  ?>
        <?=\CHtml::activeTextField($instruction, "[$index]content", ['data-test' => 'content']);  ?>
    </td>
    <td>
        <?=\CHtml::activeDropDownList($instruction, "[$index]site_id", CHtml::listData(Site::model()->findAll(), 'id', 'name')) ?>
    </td>
    <td>
        <?=\CHtml::activeDropDownList($instruction, "[$index]subspecialty_id", CHtml::listData(Subspecialty::model()->findAll(), 'id', 'name')) ?>
    </td>
    <td class="actions">
        <div class="wrapper">
        <a href="javascript:void(0)" class="save">save</a>        
        <?php if (!$instruction->isNewRecord) { ?>
            | <a href="javascript:void(0)" class="delete" >delete</a>
        <?php } ?>
        </div>
    </td>
</tr>