<?php
/* @var $this PracticeController */
/* @var $model Contact */
/* @var $form CActiveForm */
?>
<?php
$countries = CHtml::listData(Country::model()->findAll(), 'id', 'name');
$address_type_ids = CHtml::listData(AddressType::model()->findAll(), 'id', 'name');
?>

<div class="form">
    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'practice-form',
        // Please note: When you enable ajax validation, make sure the corresponding
        // controller action is handling ajax validation correctly.
        // There is a call to performAjaxValidation() commented in generated controller code.
        // See class documentation of CActiveForm for details on this.
        'enableAjaxValidation' => true,
    )); ?>

    <p class="note text-right">Fields with <span class="required">*</span> are required.</p>
    <?php echo $form->errorSummary($model); ?>
    <table class="standard">
        <tbody>
        <tr>
            <td>
                <?php echo $form->labelEx($contact, 'first_name'); ?>
            </td>
            <td>
                <?php echo $form->textArea($contact, 'first_name', array('size' => 30, 'maxlength' => 300)); ?>
                <?php echo $form->error($contact, 'first_name'); ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo $form->labelEx($model, 'code'); ?>
            </td>
            <td>
                <?php echo $form->textField($model, 'code', array('size' => 15, 'maxlength' => 20)); ?>
                <?php echo $form->error($model, 'code'); ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo $form->labelEx($model, 'phone'); ?>
            </td>
            <td>
                <?php echo $form->telField($model, 'phone', array('size' => 15, 'maxlength' => 20)); ?>
                <?php echo $form->error($model, 'phone'); ?>
            </td>
        </tr>
        <tr>
            <?php $this->renderPartial('_form_address', array('form' => $form, 'address' => $address, 'countries' => $countries, 'address_type_ids' => $address_type_ids)); ?>
        </tr>
        <tr>
            <td colspan="2" class="align-right">
                <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
            </td>
        </tr>
        </tbody>
    </table>
    <?php $this->endWidget(); ?>
</div><!-- form -->