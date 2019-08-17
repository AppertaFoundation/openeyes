<?php
/* @var $this PracticeController */
/* @var $model Contact */
/* @var $form CActiveForm */
?>
<?php
$countries = CHtml::listData(Country::model()->findAll(), 'id', 'name');
$address_type_ids = CHtml::listData(AddressType::model()->findAll(), 'id', 'name');
//$gp = new Gp();
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
                <?php echo $form->telField($model, 'phone', array('size' => 15, 'maxlength' => 20)); ?>
                <?php echo $form->error($model, 'phone'); ?>
            </td>
        </tr>
        <tr>
            <?php $this->renderPartial('_form_address', array('form' => $form, 'address' => $address, 'countries' => $countries, 'address_type_ids' => $address_type_ids)); ?>
        </tr>
        <?php if (Yii::app()->params['institution_code']=='CERA'): ?>
            <tr>
                <td>
                    <label><?php echo $gp->getAttributeLabel('Practitioner'); ?> <span class="required">*</span></label>
                </td>
                <td>
                    <?php echo $form->error($gp, 'id'); ?>

                    <?php $this->widget('application.widgets.AutoCompleteSearch',['field_name' => 'gp_autocomplete_id']); ?>
                    <div id="gp_selected_wrapper">
                        <ul class="oe-multi-select js-selected_gps">
                            <?php if(!empty($gpIdList)): ?>
                                <?php foreach ($gpIdList as $gpId){ ?>
                                    <?php if(!empty($gpId)): ?>
                                    <li>
                                        <span class="js-name" style="text-align:justify"><?php echo $gp->findByPk($gpId)->getCorrespondenceName().' - '.$gp->findByPk($gpId)->getGPROle() ?></span>
                                        <i id=js-remove-gp-<?php echo $gpId ?> class="oe-i remove-circle small-icon pad-left js-remove-gps"></i>
                                        <input type="hidden" name="Gp[id][]" class="js-gps" value=<?php echo $gpId ?>>
                                    </li>
                                    <?php endif; ?>
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
            $('.js-selected_gps').append(
                '<li>' +
                    '<span class="js-name" style="text-align:justify">'+ AutoCompleteResponse.label +'</span>' +
                    '<i id=js-remove-gp-'+AutoCompleteResponse.id  + ' class="oe-i remove-circle small-icon pad-left js-remove-gps"></i>' +
                    '<input type="hidden" name="Gp[id][]" class="js-gps" value="'+ AutoCompleteResponse.id +'">' +
                '</li>'
            );
        }
    });

    $('.js-remove-gps').click(function() {
        $(this).parent('li').find('span').text('');
        $(this).parent('li').find('input').remove();
        $(this).parent('li').hide();
    });

    $(document).ready(function ()
    {
        highLightError("Gp_id_em_","Please select at least one",'#gp_autocomplete_id');
    });

    function highLightError(elementId, containText,highLightFiled){
        if(document.getElementById(elementId) !== null && document.getElementById(elementId).innerHTML.includes(containText)){
            $(highLightFiled).addClass("error");
        }
    }

</script>