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
        <?php if(isset($duplicateCheckOutput)): ?>
            <tr id="conflicts" class="cols-full alert-box error" style="font-style: italic; font-size: small;">
                <td class="row field-row">
                    <p>Duplicate practice detected.</p>
                </td>
                <td>
                    <table class="last-left">
                        <thead>
                        <tr>
                            <th>Practice Name</th>
                            <th>Address</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php for($i=0; $i<sizeof($duplicateCheckOutput); $i++): ?>
                                <tr>
                                    <td><?php echo $duplicateCheckOutput[$i]['first_name']; ?></td>
                                    <td>
                                        <?php echo $duplicateCheckOutput[$i]['address1'].', '.$duplicateCheckOutput[$i]['city'].', '.$duplicateCheckOutput[$i]['postcode'].', '. Country::model()->find('id = '.$duplicateCheckOutput[$i]['country_id'])->name.'.'?>
                                    </td>
                                    <td></td>
                                </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </td>
            </tr>
        <?php endif; ?>
        <tr>
            <td>
                <?php echo $form->labelEx($contact, 'first_name'); ?>
            </td>
            <td>
                <?php echo $form->textArea($contact, 'first_name', array('size' => 30, 'maxlength' => 300, 'class' => 'cols-10')); ?>
                <?php echo $form->error($contact, 'first_name'); ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo $form->labelEx($model, 'code'); ?>
            </td>
            <td>
                <?php echo $form->textField($model, 'code', array('size' => 15, 'maxlength' => 20, 'class' => 'cols-10')); ?>
                <?php echo $form->error($model, 'code'); ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo $form->labelEx($model, 'phone'); ?>
            </td>
            <td>
                <?php echo $form->telField($model, 'phone', array('size' => 15, 'maxlength' => 20, 'class' => 'cols-10')); ?>
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