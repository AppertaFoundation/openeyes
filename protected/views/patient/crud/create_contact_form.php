<div class="oe-popup-wrap" id="extra_gp_adding_form" style="display: none; z-index:100">
    <div class="oe-popup">
        <div class="form">
            <?php
            $extra_gp_form = $this->beginWidget('CActiveForm', array(
                'id' => 'extra-gp-form',
                'enableAjaxValidation' => true,
            ));
            ?>
            <?php echo $extra_gp_form->errorSummary($extra_gp_contact); ?>
            <div class="title">
                <div id="extra_gp_adding_title" data-type="">Add New Contact</div>
                <div class="close-icon-btn">
                    <i class="oe-i remove-circle pro-theme js-cancel-add-contact"></i>
                </div>
            </div>
            <div class="alert-box warning" id="extra_gp_practitioner-alert-box" style="display:none;">
                <p id="extra_gp_errors"></p>
            </div>
            <table class="standard row">
                <tbody>
                <tr>
                    <td>Title:</td>
                    <td class="flex-layout">
                        <?php echo $extra_gp_form->textField($extra_gp_contact, 'title', array('size' => 30, 'maxlength' => 20)); ?>
                        <?php echo $extra_gp_form->error($extra_gp_contact, 'title'); ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo $extra_gp_form->labelEx($extra_gp_contact, 'first_name'); ?>
                    </td>
                    <td>
                        <?php echo $extra_gp_form->textField($extra_gp_contact, 'first_name', array('size' => 30, 'maxlength' => 100, 'autocomplete' => 'off')); ?>
                        <?php echo $extra_gp_form->error($extra_gp_contact, 'first_name'); ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo $extra_gp_form->labelEx($extra_gp_contact, 'last_name'); ?>
                    </td>
                    <td>
                        <?php echo $extra_gp_form->textField($extra_gp_contact, 'last_name', array('size' => 30, 'maxlength' => 100, 'autocomplete' => 'off')); ?>
                        <?php echo $extra_gp_form->error($extra_gp_contact, 'last_name'); ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo $extra_gp_form->labelEx($extra_gp_contact, 'primary_phone'); ?>
                    </td>
                    <td>
                        <?php echo $extra_gp_form->textField($extra_gp_contact, 'primary_phone', array('size' => 30, 'maxlength' => 20, 'autocomplete' => 'off')); ?>
                        <?php echo $extra_gp_form->error($extra_gp_contact, 'primary_phone'); ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo $extra_gp_form->labelEx($extra_gp_contact, 'Role'); ?>
                    </td>
                    <td>
                        <?php echo $extra_gp_form->error($extra_gp_contact, 'contact_label_id'); ?>

                        <?php $this->widget('application.widgets.AutoCompleteSearch',['field_name' => 'extra_gp_autocomplete_contact_label_id']); ?>

                    </td>
                </tr>
                <tr id="extra_gp_selected_contact_label_wrapper" style="display: <?php echo $extra_gp_contact->label ? '' : 'none' ?>">
                    <td></td>
                    <td>
                        <div>
                      <span class="js-name">
                        <?php echo isset($extra_gp_contact->label) ? $extra_gp_contact->label->name : ''; ?>
                      </span>
                            <?php echo CHtml::hiddenField('Contact[contact_label_id]', $extra_gp_contact->contact_label_id, array('class' => 'hidden_id')); ?>
                        </div>
                    </td>
                    <td>
                        <a href="javascript:void(0)" class="oe-i trash removeReading remove"></a>
                    </td>
                </tr>
                <tr id="extra_gp_no_contact_label_result" style="display:none">
                    <td></td>
                    <td>
                        <div>
                            <div class="selected_gp">No result</div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="align-right">
                        <?php
                            echo CHtml::ajaxButton('Next',
                                Yii::app()->controller->createUrl('gp/validateGpContact'),
                                array(
                                    'type' => 'POST',
                                    'success' => 'js:function(event){
                                    if (event.includes("error")){
                                        $("#extra_gp_errors").html(event);
                                        $("#extra_gp_practitioner-alert-box").css("display","");
                                    }else{
                                        $("#extra-gp-form")[0].reset();
                                        $("#extra_gp_errors").text("");
                                        $("#extra_gp_practitioner-alert-box").css("display","none");
                                        $("#extra_gp_adding_form").css("display","none");
                                        $("#extra_practice_adding_existing_form").css("display","");
                                    }
                                  }',
                                ),
                                array('class' => 'button hint green')
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


<div class="oe-popup-wrap" id="extra_practice_adding_existing_form" style="display: none; z-index:100">
    <div class="oe-popup">
        <div class="title">
            Add Existing Practice
            <div class="close-icon-btn">
                <i class="oe-i remove-circle pro-theme js-cancel-add-contact"></i>
            </div>
        </div>
        <div class="alert-box warning" id="extra-existing-practice-alert-box" style="display:none;">
            <p id="extra-existing-practice-errors"></p>
        </div>
        <div class="form">
            <?php $extra_existing_practice_form = $this->beginWidget('CActiveForm', array(
                'id' => 'extra-adding-existing-practice-form',
                'enableAjaxValidation' => true,
            )); ?>

            <p class="note text-right">Fields with <span class="required">*</span> are required.</p>
            <?php echo $extra_existing_practice_form->errorSummary($extra_practice_associate); ?>
            <table class="standard">
                <tbody>
                <tr>
                    <td>
                        <?php echo $extra_existing_practice_form->labelEx($extra_practice_associate, 'practice_id'); ?>
                    </td>
                    <td>
                        <?php $this->widget('application.widgets.AutoCompleteSearch', ['field_name' => 'autocomplete_extra_practice_id']); ?>
                        <div id="selected_practice_associate_wrapper">
                            <ul class="oe-multi-select js-selected-practice-associate">
                            </ul>
                            <?= CHtml::hiddenField('PracticeAssociate[practice_id]', $extra_practice_associate->practice_id,
                                array('class' => 'hidden_id')); ?>
                        </div>
                        <div id="no_practice_associate_result" style="display: none;">
                            <div>No result</div>
                        </div>
                        <a id="js-add-extra-practice-btn" href="#">Add Practice</a>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="align-right">
                        <?php echo CHtml::ajaxButton('Add',
                            Yii::app()->controller->createUrl('practiceAssociate/create'),
                            [
                                'type' => 'POST',
                                'success' => 'js:function(event){
                                 if (event.includes("error")){
                                        let error = JSON.parse(event);
                                        $("#extra-existing-practice-errors").html(error.error);
                                        $("#extra-existing-practice-alert-box").css("display","");
                                  }else{
                                      let gp = JSON.parse(event);
                                      if($("#extra_gp_adding_title").text() === "Add Referring Practitioner"){
                                        addExtraGp("js-selected_gp", gp.gp_id);
                                      } else {
                                       addExtraGp("js-selected_extra_gps", gp.gp_id);
                                      }

                                      extraContactFormCleaning();
                                      $(".js-extra-practice-gp-id").val("");
//                                      clearing practice_id value (stored in the hidden field) from the HTML DOM after the contact/gp has been successfully added
                                      $("#PracticeAssociate_practice_id").val("");
                                }
                          }',
                            ],
                            array('class' => 'button hint green')
                        );
                        ?>
                    </td>
                </tr>
                </tbody>
            </table>
            <?php $this->endWidget(); ?>
        </div><!-- form -->
    </div>
</div>


<?php
$extra_practice_countries = CHtml::listData(Country::model()->findAll(), 'id', 'name');
$extra_practice_address_type_ids = CHtml::listData(AddressType::model()->findAll(), 'id', 'name');
?>

<div class="oe-popup-wrap" id="extra_practice_adding_new_form" style="display: none; z-index:100">
    <div class="oe-popup">
        <div class="title">
            Add Practice
            <div class="close-icon-btn">
                <i class="oe-i remove-circle pro-theme js-cancel-add-contact"></i>
            </div>
        </div>
        <div id="extra-practice-practice-alert-box" class="alert-box warning" style="display:none;">
            <p id="extra-practice-errors"></p>
        </div>
        <div class="form">
            <?php $extra_practice_form = $this->beginWidget('CActiveForm', array(
                'id' => 'extra-adding-practice-form',
                'enableAjaxValidation' => true,
            )); ?>

            <p class="note text-right">Fields with <span class="required">*</span> are required.</p>
            <?php echo $extra_practice_form->errorSummary($extra_practice); ?>
            <table class="standard">
                <tbody>
                <tr>
                    <td>
                        <?php echo $extra_practice_form->labelEx($extra_practice_contact, 'first_name'); ?>
                    </td>
                    <td>
                        <?php echo $extra_practice_form->textArea($extra_practice_contact, 'first_name', array('maxlength' => 300, 'cols' => 40)); ?>
                        <?php echo $extra_practice_form->error($extra_practice_contact, 'first_name'); ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo $extra_practice_form->labelEx($extra_practice, 'code'); ?>
                    </td>
                    <td>
                        <?php echo $extra_practice_form->textField($extra_practice, 'code', array('size' => 15, 'maxlength' => 20)); ?>
                        <?php echo $extra_practice_form->error($extra_practice, 'code'); ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo $extra_practice_form->labelEx($extra_practice_contact, 'primary_phone'); ?>
                        <?php echo $extra_practice_form->error($extra_practice_contact, 'primary_phone'); ?>
                    </td>
                    <td>
                        <?php echo $extra_practice_form->telField($extra_practice_contact, 'primary_phone', array('size' => 15, 'maxlength' => 20)); ?>
                    </td>
                </tr>
                <tr>
                    <?php $this->renderPartial('../practice/_form_address', array('form' => $extra_practice_form, 'address' => $extra_practice_address, 'countries' => $extra_practice_countries, 'address_type_ids' => $extra_practice_address_type_ids)); ?>
                </tr>
                <tr>
                    <td colspan="2" class="align-right">
                        <?php echo CHtml::ajaxButton('Add',
                            Yii::app()->controller->createUrl('practice/createAssociate'),
                            [
                                'type' => 'POST',
                                'success' => 'js:function(event){
                                 if (event.includes("error")){
                                        $("#extra-practice-errors").html(event);
                                        $("#extra-practice-practice-alert-box").css("display","");
                                  }else{
                                      let gp = JSON.parse(event);
                                      if($("#extra_gp_adding_title").text() === "Add Referring Practitioner"){
                                        addExtraGp("js-selected_gp", gp.gp_id);
                                      } else {
                                       addExtraGp("js-selected_extra_gps", gp.gp_id);
                                      }
                                      extraContactFormCleaning();
                                      $(".js-extra-practice-gp-id").val("");
//                                      clearing practice_id value (stored in the hidden field) from the HTML DOM after the contact/gp has been successfully added
                                      $("#PracticeAssociate_practice_id").val("");
                                }
                          }',
                            ],
                            array('class' => 'button hint green')
                        );
                        ?>
                    </td>
                </tr>
                </tbody>
            </table>
            <?php $this->endWidget(); ?>
        </div><!-- form -->
    </div>
</div>

<script>
    OpenEyes.UI.AutoCompleteSearch.init({
        input: $('#extra_gp_autocomplete_contact_label_id'),
        url: '/gp/contactLabelList',
        maxHeight: '200px',
        onSelect: function(){
            let AutoCompleteResponse = OpenEyes.UI.AutoCompleteSearch.getResponse();
            removeSelectedContactLabel();
            addItem('extra_gp_selected_contact_label_wrapper', {item: AutoCompleteResponse});
            $('#extra_gp_autocomplete_contact_label_id').val('');
        }
    });

    OpenEyes.UI.AutoCompleteSearch.init({
        input: $('#autocomplete_extra_practice_id'),
        url: '/patient/practiceList',
        maxHeight: '200px',
        onSelect: function(){
            let AutoCompleteResponse = OpenEyes.UI.AutoCompleteSearch.getResponse();
            $('.js-selected-practice-associate').find('li').remove();
            $('.js-selected-practice-associate').append('<li><span class="js-name" style="text-align:justify">'+AutoCompleteResponse.label+'</span><i id="js-remove-extra-practice-'+AutoCompleteResponse.value+'" class="oe-i remove-circle small-icon pad-left"></i></li>');
            $('#PracticeAssociate_practice_id').val(AutoCompleteResponse.value);
            $('#js-remove-extra-practice-'+AutoCompleteResponse.value).click(function () {
                $(this).parent('li').remove();
                $('#PracticeAssociate_practice_id').val("");
            });
            }
    });
    $('#extra_gp_selected_contact_label_wrapper').find('.removeReading').click(function () {
        $('#extra_gp_selected_contact_label_wrapper').css('display','none');
    });
</script>
