<?php
/* @var $this CaseSearchController */
/* @var $id int */
/* @var $model CaseSearchParameter */
/* @var $form CActiveForm */
?>
<tr class="parameter" id="<?php echo $id; ?>">
    <td>
        <div class="<?php echo $model->name; ?> flex-layout"
             style="padding-bottom: 6px; padding-top: 6px;"
        >
            <div class="cols-10">
                <?php $model->renderParameter($id); ?>
                <?php echo CHtml::activeHiddenField($model, "[$id]id"); ?>
            </div>
            <div class="cols-2">
                <i id="<?= $id ?>-remove" class="oe-i trash"></i>
            </div>
            <hr/>
        </div>
    </td>
</tr>
<script type="text/javascript">
    $('#<?= $id?>-remove').on('click', function () {
        this.closest('.parameter').remove();
    })
</script>
