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
        'clientOptions' => array(
            'hideErrorMessage' => false,
        )
    )); ?>

    <p class="note text-right">Fields with <span class="required">*</span> are required.</p>
    <?php echo $form->errorSummary(array($contact,$model,$address)); ?>
    <table class="standard">
        <tbody>
        <?php if(isset($duplicateCheckOutput) && count($duplicateCheckOutput) > 0): ?>
            <tr id="conflicts" class="cols-full alert-box error" style="font-style: italic; font-size: small;">
                <td class="row field-row">
                    <p>Duplicate practice detected.</p>
                </td>
                <td>
                    <table class="last-left">
                        <thead>
                        <tr>
                            <th>Practice Name</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php for($i=0; $i<sizeof($duplicateCheckOutput); $i++): ?>
                                <tr>
                                    <td><?php echo $duplicateCheckOutput[$i]['first_name']; ?></td>
                                    <td><?php echo $duplicateCheckOutput[$i]['phone'].''?></td>
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
        <?php if (Yii::app()->params['institution_code']=='CERA'): ?>
            <tr>
                <td>
                    <label><?php echo $gp->getAttributeLabel('Practitioner'); ?></label>
                </td>
                <td>
                    <?php echo $form->error($gp, 'id'); ?>
                    <?php $this->widget('application.widgets.AutoCompleteSearch',['field_name' => 'gp_autocomplete_id']); ?>
                    <div id="gp_selected_wrapper">
                        <ul class="oe-multi-select js-selected_gps">
                            <?php if(!empty($cpas)): ?>
                                <?php $i=0; ?>
                                <?php foreach ($cpas as $cpa){ ?>
                                    <li>
                                        <div style="width: 100%">
                                            <span class="js-name" style="text-align:justify; float: left; padding: 5px"><?php echo $gp->findByPk($cpa->gp_id)->getCorrespondenceName().' - '.$gp->findByPk($cpa->gp_id)->getGPROle() ?></span>
                                            <i id=js-remove-gp-<?php echo $cpa->gp_id ?> class="oe-i remove-circle small-icon pad-left js-remove-gps" style="float: right"></i>
                                            <div>
                                                <?php
                                                    echo $form->textField($cpa,'provider_no',array(
                                                        'placeholder' => 'Enter provider number',
                                                        'maxlength' => 255,
                                                        'name' => 'ContactPracticeAssociate['.$i.'][provider_no]',
                                                        'style' => 'float: right; width: 150px;',
                                                    ));
                                                ?>
                                            </div>
                                            <?php echo $form->error($cpa, 'provider_no'); ?>

                                            <input type="hidden" name="Gp[<?= $i ?>][id]" class="js-gps" value=<?php echo $cpa->gp_id ?>>
                                        </div>
                                    </li>
                                    <?php $i++; ?>
                                <?php } ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </td>
            </tr>
        <?php endif; ?>
        <tr>
            <td colspan="2" class="align-right">
                <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
            </td>
        </tr>
        </tbody>
    </table>
    <?php $this->endWidget(); ?>
</div><!-- form -->

<script>
    OpenEyes.UI.AutoCompleteSearch.init({
        input: $('#gp_autocomplete_id'),
        url: '/gp/gpList',
        maxHeight: '200px',
        onSelect: function(){
            let AutoCompleteResponse = OpenEyes.UI.AutoCompleteSearch.getResponse();
            let addGp = true;

            // traversing the li's to make sure we don't have duplicates.
            $.each($('.js-selected_gps li'), function() {
                var gpId = $(this).find('.js-gps').val();

                if (gpId === AutoCompleteResponse.id){
                    addGp = false;
                    return addGp;
                }
            });

            let countExistingPractitioners = $('.js-selected_gps li').length;
            // If the gpid does not already exist in the list then add it to the list.
            if(addGp) {
                $('.js-selected_gps').append(
                    '<li>' +  '<div style="width: 100%">' +
                    '<span class="js-name" style="text-align:justify; float: left; padding: 5px">' + AutoCompleteResponse.label  + '</span>' +
                    '<i id=js-remove-gp-' + AutoCompleteResponse.id + ' class="oe-i remove-circle small-icon pad-left js-remove-gps" style="float: right"></i>' +
                    '<div>' + '<input id=js-gp-provider-no' + AutoCompleteResponse.id + ' value="" name="ContactPracticeAssociate[' + countExistingPractitioners + '][provider_no]" style="float: right" placeholder="Enter provider number">' + '</div>' +
                    '<input type="hidden" name="Gp[' + countExistingPractitioners + '][id]" class="js-gps" value="' + AutoCompleteResponse.id + '">' + '</div>' +
                    '</li>'
                );
            }

            removeGpClickEvent();
        }
    });

    $(document).ready(function ()
    {
        removeGpClickEvent();
        highLightError("Gp_id_em_","Please select at least one",'#gp_autocomplete_id');
    });

    function highLightError(elementId, containText,highLightFiled){
        if(document.getElementById(elementId) !== null && document.getElementById(elementId).innerHTML.includes(containText)){
            $(highLightFiled).addClass("error");
        }
    }

    function removeGpClickEvent() {
        $('.js-remove-gps').unbind("click").click(function(event) {
            $(this).parents('li').find('span').text('');
            $(this).parents('li').find('input').remove();
            $(this).parents('li').hide();
            $(this).parents('li').next('.errorMessage').remove();
            $(this).parents('li').remove();

            // After removing any practitioner, resetting the id's for both Gp and Contact Practice Associate.
            $('.js-selected_gps').children('li').each(function(index, element) {
                $(element).find('div').find('div').find('input').attr("name", "ContactPracticeAssociate[" + index + "][provider_no]");
                $(element).find('div').find('div').find('input').attr("id", "ContactPracticeAssociate_" + index + "_provider_no");
                $(element).find('.js-gps').attr("name", "Gp[" + index + "][id]");
            });
        });
    }

</script>