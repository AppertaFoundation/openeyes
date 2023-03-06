<?php
/* @var $this GpController */
/* @var $model Contact */
/* @var $gp Practitioner */
/* @var $context String */
$this->pageTitle = 'Update ' . \SettingMetadata::model()->getSetting('general_practitioner_label');
//$dataProvided = $dataProvider->getData();
//$items_per_page = $dataProvider->getPagination()->getPageSize();
//$page_num = $dataProvider->getPagination()->getCurrentPage();
//$from = ($page_num * $items_per_page) + 1;
//$to = min(($page_num + 1) * $items_per_page, $dataProvider->totalItemCount);
?>

<div class="oe-home oe-allow-for-fixing-hotlist">
    <div class="oe-full-header flex-layout">
        <div class="title wordcaps">
            Update&nbsp;<b>Practitioner</b>
        </div>
    </div>
    <div class="oe-full-content oe-new-patient flex-layout flex-top">
        <div class="patient-content">
            <div class="form">
                <?php
                \Yii::app()->assetManager->RegisterScriptFile('js/Gp.js');
                $form = $this->beginWidget('CActiveForm', array(
                    'id' => 'gp-form',
                    // Please note: When you enable ajax validation, make sure the corresponding
                    // controller action is handling ajax validation correctly.
                    // There is a call to performAjaxValidation() commented in generated controller code.
                    // See class documentation of CActiveForm for details on this.
                    'enableAjaxValidation' => true,
                )); ?>
                <?php if ($gp->hasErrors()) { ?>
                <div class="error alert-box"><?php echo $form->error($gp, 'contact'); ?></div>
                <?php } ?>
                    <table class="standard row">
                        <tbody>
                            <tr>
                                <td>
                                    <div class="column"><?php echo $form->labelEx($model, 'title'); ?></div>
                                </td>
                                <td>
                                    <?php echo $form->textField($model, 'title', array('size' => 30, 'maxlength' => 20)); ?>
                                    <?php echo $form->error($model, 'title'); ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <?php echo $form->labelEx($model, 'first_name'); ?>
                                </td>
                                <td>
                                    <?php echo $form->textField($model, 'first_name', array('size' => 30, 'maxlength' => 100, 'autocomplete' => 'off')); ?>
                                    <?php echo $form->error($model, 'first_name'); ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <?php echo $form->labelEx($model, 'last_name'); ?>
                                </td>
                                <td>
                                    <?php echo $form->textField($model, 'last_name', array('size' => 30, 'maxlength' => 100, 'autocomplete' => 'off')); ?>
                                    <?php echo $form->error($model, 'last_name'); ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <?php echo $form->labelEx($model, 'primary_phone'); ?>
                                </td>
                                <td>
                                    <?php echo $form->telField($model, 'primary_phone', array('size' => 15, 'maxlength' => 20, 'autocomplete' => 'off')); ?>
                                    <?php echo $form->error($model, 'primary_phone'); ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label><?php echo $model->getAttributeLabel('Role'); ?> <span class="required">*</span></label>
                                </td>
                                <td>
                                    <?php echo $form->error($model, 'contact_label_id'); ?>
                                    <?php $this->widget('application.widgets.AutoCompleteSearch', ['field_name' => 'autocomplete_contact_label_id']); ?>
                                </td>
                            </tr>
                            <tr id="selected_contact_label_wrapper" style="display: <?php echo $model->label ? '' : 'none' ?>">
                                <td></td>
                                <td>
                                    <div>
                                        <span class="js-name">
                                            <?php echo isset($model->label) ? $model->label->name : ''; ?>
                                        </span>
                                        <?php echo CHtml::hiddenField(
                                            'Contact[contact_label_id]',
                                            $model->contact_label_id,
                                            array('class' => 'hidden_id')
                                        ); ?>
                                    </div>
                                </td>
                                <td>
                                    <a href="javascript:void(0)" class="oe-i trash removeReading remove"></a>
                                </td>
                            </tr>
                            <tr id="no_contact_label_result" style="display:none">
                                <td></td>
                                <td>
                                    <div>
                                        <div class="selected_gp">No result</div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Active</td>
                                <td>
                                  <?=
                                    \CHtml::activeRadioButtonList(
                                        $gp,
                                        'is_active',
                                        [1 => 'Yes', 0 => 'No'],
                                        ['separator' => ' ']
                                    );
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="align-right">
                                    <?php
                                    echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save');
                                    ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <?php if ($cpas) : ?>
                        <div>
                            <h3 class="box-title">Associated Practices</h3>
                            <br />
                            <div>
                                <table id="practice-grid" class="standard">
                                    <thead>
                                        <tr>
                                            <th>Provider Number</th>
                                            <th>Practice Contact</th>
                                            <th>Practice Address</th>
                                            <th>Code</th>
                                            <th>Telephone</th>
                                            <th/>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i = 0; ?>
                                        <?php foreach ($cpas as $cpa) : ?>
                                            <tr id="r<?php echo $cpa->id; ?>">
                                                <?php echo CHtml::hiddenField('ContactPracticeAssociate[' . $i . '][id]', $cpa->id, array('class' => 'hidden_id')); ?>
                                                <td>
                                                    <?php
                                                        echo $form->textField($cpa, 'provider_no', array(
                                                        'placeholder' => 'Enter provider number',
                                                        'maxlength' => 255,
                                                        'name' => 'ContactPracticeAssociate[' . $i . '][provider_no]',
                                                        ));
                                                    ?>
                                                    <?php echo $form->error($cpa, 'provider_no'); ?>
                                                </td>

                                                <td><?php echo CHtml::encode($cpa->practice->contact->first_name); ?></td>
                                                <td><?php echo CHtml::encode($cpa->practice->getAddressLines()); ?></td>
                                                <td><?php echo CHtml::encode($cpa->practice->code); ?></td>
                                                <td><?php echo CHtml::encode($cpa->practice->phone); ?></td>
                                                <td/>
                                            </tr>
                                            <?php $i++; ?>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php $this->endWidget(); ?>
            </div><!-- form -->
            <script>
                OpenEyes.UI.AutoCompleteSearch.init({
                    input: $('#autocomplete_contact_label_id'),
                    url: '/gp/contactLabelList',
                    maxHeight: '200px',
                    onSelect: function(){
                        let AutoCompleteResponse = OpenEyes.UI.AutoCompleteSearch.getResponse();
                        removeSelectedContactLabel();
                        addItem('selected_contact_label_wrapper', {item: AutoCompleteResponse});
                        $('#autocomplete_contact_label_id').val('');
                    }
                });
            </script>
        </div>
    </div>
</div>
