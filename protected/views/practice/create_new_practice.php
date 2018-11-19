<?php
/**
 * Created by PhpStorm.
 * User: Fivium
 * Date: 20/11/2018
 * Time: 10:29 AM
 */
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
            <?php
            $countries = CHtml::listData(Country::model()->findAll(), 'id', 'name');
            $address_type_ids = CHtml::listData(AddressType::model()->findAll(), 'id', 'name');
            $form = $this->beginWidget('CActiveForm', array(
                'id' => 'practice-form',
                // Please note: When you enable ajax validation, make sure the corresponding
                // controller action is handling ajax validation correctly.
                // There is a call to performAjaxValidation() commented in generated controller code.
                // See class documentation of CActiveForm for details on this.
                'enableAjaxValidation' => true,

            ));
            ?>
            <table class="standard row">
                <tbody>
                <tr>
                    <td>
                        <?php echo CHtml::activeLabelEx($model, 'first_name'); ?>
                    </td>
                    <td>
                        <?php echo CHtml::activeTextField($model, 'first_name', array('size' => 30, 'maxlength' => 30)); ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo CHtml::activeLabelEx($practice, 'phone'); ?>
                    </td>
                    <td>
                        <?php echo CHtml::activeTextField($practice, 'phone', array('size' => 30, 'maxlength' => 30)); ?>
                    </td>
                </tr>
                <tr>

                    <?php echo $this->renderPartial('_form_address', array(
                        'form' => $form,
                        'address' => $practiceaddress,
                        'countries' => $countries,
                        'address_type_ids' => $address_type_ids,
                    ));
                    ?>
                </tr>
                <tr>
                    <td colspan="2" class="align-right">
                        <?php echo CHtml::ajaxButton('Add',
                            Yii::app()->controller->createUrl('practice/create', array("context" => 'AJAX')),
                            [
                                'type' => 'POST',
                                'error' => 'js:function(error){
                            console.log(error);
                                event.preventDefault();
                                $("#errors").text("Please input the mandatory fields.");
                                $(".alert-box").css("display","");
                          }',
                                'success' => 'js:function(event){
                            console.log(event);
                           removeSelectedPractice();
                           addGpItem("selected_practice_wrapper",event);
                           $("#practice-form")[0].reset();
                            $("#js-add-practice-event").css("display","none");
                          }',
                            ]
                        );

                        ?>
                    </td>
                </tr>
                </tbody>
            </table>
            <?php $this->endWidget(); ?>


        </div>
    </div>
</div>