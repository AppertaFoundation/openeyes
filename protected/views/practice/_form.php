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
    <?php foreach ($gpIdProviderNoList as $gpIdProviderNo){ ?>
        <?php if(isset($gpIdProviderNo[2]) && $gpIdProviderNo[2] >= 1): ?>
            <div id="conflicts" class="cols-full alert-box error" style="font-style: italic; font-size: small;">
                <div class="row field-row">
                    <p>Duplicate provider number detected.</p>
                </div>
            </div>
            <?php break; ?>
        <?php endif; ?>
    <?php } ?>
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
                            <?php if(!empty($gpIdProviderNoList)): ?>
                                <?php foreach ($gpIdProviderNoList as $gpIdProviderNo){ ?>
                                    <li style="<?php echo isset($gpIdProviderNo[2]) && $gpIdProviderNo[2] >= 1 ? 'background-color: #cd0000; color: #fff': '' ?>">
                                        <div style="width: 100%">
                                            <span class="js-name" style="text-align:justify; float: left; padding: 5px"><?php echo $gp->findByPk($gpIdProviderNo[0])->getCorrespondenceName().' - '.$gp->findByPk($gpIdProviderNo[0])->getGPROle() ?></span>
                                            <i id=js-remove-gp-<?php echo $gpIdProviderNo[0] ?> class="oe-i remove-circle small-icon pad-left js-remove-gps" style="float: right"></i>
                                            <div><input name="ContactPracticeAssociate[provider_no][]" style="float: right" id=js-gp-provider-no-<?php echo $gpIdProviderNo[1] ?> placeholder="Enter provider number" value=<?php echo $gpIdProviderNo[1] ?>></div>
                                            <input type="hidden" name="Gp[id][]" class="js-gps" value=<?php echo $gpIdProviderNo[0] ?>>
                                        </div>
                                    </li>
                                    <?php echo isset($gpIdProviderNo[2]) && $gpIdProviderNo[2] >= 1 ? '<div class="errorMessage">Duplicate Provider Number.</div>': ''?>
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

            // If the gpid does not already exist in the list then add it to the list.
            if(addGp) {
                $('.js-selected_gps').append(
                    '<li>' +  '<div style="width: 100%">' +
                    '<span class="js-name" style="text-align:justify; float: left; padding: 5px">' + AutoCompleteResponse.label  + '</span>' +
                    '<i id=js-remove-gp-' + AutoCompleteResponse.id + ' class="oe-i remove-circle small-icon pad-left js-remove-gps" style="float: right"></i>' +
                    '<div>' + '<input id=js-gp-provider-no' + AutoCompleteResponse.id + ' value="" name="ContactPracticeAssociate[provider_no][]" style="float: right" placeholder="Enter provider number">' + '</div>' +
                    '<input type="hidden" name="Gp[id][]" class="js-gps" value="' + AutoCompleteResponse.id + '">' + '</div>' +
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
        $('.js-remove-gps').click(function(event) {
            $(this).parents('li').find('span').text('');
            $(this).parents('li').find('input').remove();
            $(this).parents('li').hide();
            $(this).parents('li').next('.errorMessage').remove();
            $(this).parents('li').remove();
        });
    }

</script>