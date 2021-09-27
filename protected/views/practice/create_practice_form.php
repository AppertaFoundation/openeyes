<?php
/* @var $this PracticeController */
/* @var $model Contact */
/* @var $form CActiveForm */
?>
<?php
$countries = CHtml::listData(Country::model()->findAll(), 'id', 'name');
$address_type_ids = CHtml::listData(AddressType::model()->findAll(), 'id', 'name');
?>
<div class="oe-popup-wrap" id="js-add-practice-event" style="display: none; z-index:100">
    <div class="oe-popup">
        <div class="title">
            Add Practice
            <div class="close-icon-btn">
                <i id="js-cancel-add-practice" class="oe-i remove-circle pro-theme"></i>
            </div>
        </div>
        <div id="practice-alert-box" class="alert-box warning" style="display:none;">
            <p id="errors"></p>
        </div>
        <div class="form">
            <?php $form = $this->beginWidget('CActiveForm', array(
                'id' => 'practice-form',
                /*
                 * Please note: When you enable ajax validation, make sure the corresponding
                 * controller action is handling ajax validation correctly.
                 * There is a call to performAjaxValidation() commented in generated controller code.
                 * See class documentation of CActiveForm for details on this.
                 */
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
                        <?php echo $form->textArea($contact, 'first_name', array('size' => 50, 'maxlength' => 300)); ?>
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
                        <?php echo $form->labelEx($contact, 'primary_phone'); ?>
                        <?php echo $form->error($contact, 'primary_phone'); ?>
                    </td>
                    <td>
                        <?php echo $form->telField($contact, 'primary_phone', array('size' => 15, 'maxlength' => 20)); ?>
                    </td>
                </tr>
                <tr>
                    <?php $this->renderPartial('../practice/_form_address', array('form' => $form, 'address' => $address, 'countries' => $countries, 'address_type_ids' => $address_type_ids)); ?>
                </tr>
                <tr>
                    <td colspan="2" class="align-right">
                        <?php echo CHtml::ajaxButton('Add',
                            Yii::app()->controller->createUrl('practice/create', array("context" => 'AJAX')),
                            [
                                'type' => 'POST',
                                'success' => 'js:function(event) {
                                     if (event.includes("error")) {
                                         $alertBox = $("#practice-alert-box");
                                         $alertBox.find("#errors").html(event);
                                         $alertBox.css("display","");
                                     } else {
                                         removeSelectedPractice();
                                         addGpItem("practice",event);
                                         $("#practice-form")[0].reset();
                                         $("#js-add-practice-event").css("display","none");
                                         $("#practice-alert-box").css("display","none");
                                     }
                                 }',
                            ],
                            array('class' => 'button hint green')
                        ); ?>
                    </td>
                </tr>
                </tbody>
            </table>
            <?php $this->endWidget(); ?>
        </div><!-- form -->
    </div>
</div>
