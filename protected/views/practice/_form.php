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
    <div class="row field-row">
        <div class="large-6 column">
            <div class="row field-row">
                <div class="large-3 column"><?php echo $form->labelEx($contact, 'title'); ?></div>
                <div class="large-4 column end">
                    <?php echo $form->telField($contact, 'title', array('size' => 15, 'maxlength' => 20)); ?>
                    <?php echo $form->error($contact, 'title'); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row field-row">
        <div class="large-6 column">
            <div class="row field-row">
                <div class="large-3 column"><?php echo $form->labelEx($contact, 'first_name'); ?></div>
                <div class="large-4 column end">
                    <?php echo $form->telField($contact, 'first_name', array('size' => 15, 'maxlength' => 20)); ?>
                    <?php echo $form->error($contact, 'first_name'); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row field-row">
        <div class="large-6 column">
            <div class="row field-row">
                <div class="large-3 column"><?php echo $form->labelEx($contact, 'last_name'); ?></div>
                <div class="large-4 column end">
                    <?php echo $form->telField($contact, 'last_name', array('size' => 15, 'maxlength' => 20)); ?>
                    <?php echo $form->error($contact, 'last_name'); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row field-row">
        <div class="large-6 column">
            <div class="row field-row">
                <div class="large-3 column"><?php echo $form->labelEx($model, 'code'); ?></div>
                <div class="large-4 column end">
                    <?php echo $form->telField($model, 'code', array('size' => 15, 'maxlength' => 20)); ?>
                    <?php echo $form->error($model, 'code'); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row field-row">
        <div class="large-6 column">
            <div class="row field-row">
                <div class="large-3 column"><?php echo $form->labelEx($contact, 'primary_phone'); ?></div>
                <div class="large-4 column end">
                    <?php echo $form->telField($contact, 'primary_phone', array('size' => 15, 'maxlength' => 20)); ?>
                    <?php echo $form->error($contact, 'primary_phone'); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row field-row">
        <div class="large-6 column">
            <?php $this->renderPartial('../patient/_form_address', array('form' => $form, 'address' => $address, 'countries' => $countries, 'address_type_ids' => $address_type_ids)); ?>
        </div>
    </div>
    <div class="row buttons text-right">
      <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->