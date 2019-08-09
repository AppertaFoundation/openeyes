<?php
/* @var $this DisorderController */
/* @var $model Disorder */
/* @var $form CActiveForm */

$specialties = Specialty::model()->findAll();
?>

<div class="oe-full-content flex-layout flex-top">

<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'disorder-form',
    // Please note: When you enable ajax validation, make sure the corresponding
    // controller action is handling ajax validation correctly.
    // There is a call to performAjaxValidation() commented in generated controller code.
    // See class documentation of CActiveForm for details on this.
    'enableAjaxValidation'=>false,
)); ?>

    <p class="note">Fields with <span class="required">*</span> are required.</p>

    <?php echo $form->errorSummary($model); ?>

    <table class="standard highlight-rows">
        <tbody>
        <tr>
            <td>
                <?php echo $form->labelEx($model,'id'); ?>
            </td>
            <td>
                <?php echo $form->textField($model,'id',array('size'=>20,'maxlength'=>20,'disabled'=>($model->isNewRecord ? false : true),)); ?>
                <?php echo $form->error($model,'id'); ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo $form->labelEx($model,'fully_specified_name'); ?>
            </td>
            <td>
                <?php echo $form->textField($model,'fully_specified_name',array('size'=>60,'maxlength'=>255)); ?>
                <?php echo $form->error($model,'fully_specified_name'); ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo $form->labelEx($model,'term'); ?>
            </td>
            <td>
                <?php echo $form->textField($model,'term',array('size'=>60,'maxlength'=>255)); ?>
                <?php echo $form->error($model,'term'); ?>
            </td>
        </tr>
         <tr>
            <td>
                <?php echo $form->labelEx($model,'specialty_id'); ?>
            </td>
            <td>
                <?php echo $form->dropDownList($model, 'specialty_id', CHtml::listData($specialties, 'id', 'name')) ?>
                <?php echo $form->error($model,'specialty_id'); ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo $form->labelEx($model,'active'); ?>
            </td>
            <td>
                <?php echo $form->checkBox($model,'active',array('checked'=> ($model->active)? "checked":"")); ?>
                <?php echo $form->error($model,'active'); ?>
            </td>
        </tr>
        </tbody>
    </table>

    <div class="row flex-layout flex-left">
        <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save',
        array('class' => 'button green hint')); ?>
    </div>

<?php $this->endWidget(); ?>

</div><!-- form -->