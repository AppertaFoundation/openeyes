<h2>Operation Report</h2>

<div class="row divider">
    <?php $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
        'id' => 'module-report-form',
        'enableAjaxValidation' => false,
        'layoutColumns' => array('label' => 2, 'field' => 10),
        'action' => Yii::app()->createUrl('/' . $this->module->id . '/report/downloadReport'),
    )) ?>

    <input type="hidden" name="report-name" value="Operations"/>

    <table class="standard cols-full">
        <colgroup>
            <col class="cols-1">
            <col class="cols-3">
            <col class="cols-1">
            <col class="cols-7">
        </colgroup>
        <tbody>
        <?php $this->renderPartial('//report/_institution_table_row', ['field_name' => "institution_id"]);?>
        <tr class="col-gap">
            <td>
                <?= \CHtml::label('Surgeon', 'surgeon_id') ?>
            </td>
            <td>
                <?php if (Yii::app()->getAuthManager()->checkAccess('Report', Yii::app()->user->id)) : ?>
                    <?= \CHtml::dropDownList('surgeon_id', null, $surgeons, array('empty' => 'All surgeons')) ?>
                <?php else : ?>
                    <?php
                    $user = User::model()->findByPk(Yii::app()->user->id);
                    echo CHtml::dropDownList(null, '',
                        array(Yii::app()->user->id => $user->fullName),
                        array(
                            'disabled' => 'disabled',
                            'readonly' => 'readonly',
                            'style' => 'background-color:#D3D3D3;',
                        ) //for some reason the chrome doesn't gray out
                    );
                    echo CHtml::hiddenField('surgeon_id', Yii::app()->user->id);
                    ?>
                <?php endif ?>
            </td>
            <td>Procedure</td>
            <td>
                <?php
                $this->widget('application.widgets.ProcedureSelection', array(
                    'element' => Element_OphTrOperationnote_ProcedureList::model(),
                    'newRecord' => true,
                    'last' => true,
                    'label' => '',
                    'popupButton' => false,
                ));
                ?>
            </td>
        </tr>
        <tr class="col-gap">
            <td>
                <?= \CHtml::label('Cataract Complications', 'cat_complications'); ?>
            </td>
            <td>
                <?php $this->widget('application.widgets.MultiSelectList', array(
                    'field' => 'complications',
                    'options' => CHtml::listData(OphTrOperationnote_CataractComplications::model()->findAll(), 'id', 'name'),
                    'htmlOptions' => array('empty' => '- Complications -', 'multiple' => 'multiple', 'nowrapper' => true),
                )); ?>
            </td>
            <td>Date Range</td>
            <td>
                <div class="flex-layout cols-full">
                    <input id="date_from"
                           placeholder="From"
                           class="start-date"
                           name="date_from"
                           autocomplete="off"
                           value= <?= @$_GET['date_from']; ?>
                    >
                    <input id="date_to"
                           placeholder="To"
                           class="end-date"
                           name="date_to"
                           autocomplete="off"
                           value= <?= @$_GET['date_to']; ?>
                    >
                </div>
            </td>
        </tr>
        </tbody>
    </table>
    <table class="standard cols-full">
        <colgroup>
            <col class="cols-4" span="3">
        </colgroup>
        <tbody>
        <tr>
            <td class="valign-top">
                <h3>Operation Booking</h3>
                <ul>
                    <li>
                        <?= \CHtml::checkBox('bookingcomments'); ?>
                        <?= \CHtml::label('Comments', 'bookingcomments') ?>
                    </li>
                    <li>
                        <?= \CHtml::checkBox('booking_diagnosis'); ?>
                        <?= \CHtml::label('Operation booking diagnosis', 'booking_diagnosis') ?>
                    </li>
                    <li>
                        <?= \CHtml::checkBox('surgerydate'); ?>
                        <?= \CHtml::label('Surgery Date', 'surgerydate') ?>
                    </li>
                    <li>
                        <?= \CHtml::checkBox('theatre'); ?>
                        <?= \CHtml::label('Theatre', 'theatre') ?>
                    </li>
                </ul>
            </td>
            <td class="valign-top">
                <h3>Examination</h3>
                <ul>
                    <li>
                        <?= \CHtml::checkBox('comorbidities'); ?>
                        <?= \CHtml::label('Comorbidities', 'comorbidities') ?>
                    </li>
                    <li>
                        <?= \CHtml::checkBox('first_eye'); ?>
                        <?= \CHtml::label('First or Second Eye', 'first_eye') ?>
                    </li>
                    <li>
                        <?= \CHtml::checkBox('refraction_values'); ?>
                        <?= \CHtml::label('Refraction Values', 'refraction_values') ?>
                    </li>
                    <li>
                        <?= \CHtml::checkBox('target_refraction'); ?>
                        <?= \CHtml::label('Target Refraction', 'target_refraction') ?>
                    </li>
                    <li>
                        <?= \CHtml::checkBox('cataract_surgical_management'); ?>
                        <?= \CHtml::label('Cataract Surgical Management', 'cataract_surgical_management') ?>
                    </li>
                    <li>
                        <?= \CHtml::checkBox('va_values'); ?>
                        <?= \CHtml::label('VA Values', 'va_values') ?>
                    </li>
                </ul>
            </td>
            <td class="valign-top">
                <h3>Operation Note</h3>
                <ul>
                    <li>
                        <?= \CHtml::checkBox('cataract_report'); ?>
                        <?= \CHtml::label('Cataract Report', 'cataract_report') ?>
                    </li>
                    <li>
                        <?= \CHtml::checkBox('incision_site'); ?>
                        <?= \CHtml::label('Cataract Operation Details', 'incision_site') ?>
                    </li>
                    <li>
                        <?= \CHtml::checkBox('cataract_complication_notes'); ?>
                        <?= \CHtml::label('Cataract Complication Notes', 'cataract_complication_notes') ?>
                    </li>
                    <li>
                        <?= \CHtml::checkBox('tamponade_used'); ?>
                        <?= \CHtml::label('Tamponade Used', 'tamponade_used') ?>
                    </li>
                    <li>
                        <?= \CHtml::checkBox('anaesthetic_type'); ?>
                        <?= \CHtml::label('Anaesthetic Type', 'anaesthetic_type') ?>
                    </li>
                    <li>
                        <?= \CHtml::checkBox('anaesthetic_delivery'); ?>
                        <?= \CHtml::label('Anaesthetic Delivery', 'anaesthetic_delivery') ?>
                    </li>
                    <li>
                        <?= \CHtml::checkBox('anaesthetic_complications'); ?>
                        <?= \CHtml::label('Anaesthetic Complications', 'anaesthetic_complications') ?>
                    </li>
                    <li>
                        <?= \CHtml::checkBox('anaesthetic_comments'); ?>
                        <?= \CHtml::label('Anaesthetic Comments', 'anaesthetic_comments') ?>
                    </li>
                    <li>
                        <?= \CHtml::checkBox('surgeon'); ?>
                        <?= \CHtml::label('Surgeon', 'surgeon') ?>
                    </li>
                    <li>
                        <?= \CHtml::checkBox('surgeon_role'); ?>
                        <?= \CHtml::label('Surgeon role', 'surgeon_role') ?>
                    </li>
                    <li>
                        <?= \CHtml::checkBox('assistant'); ?>
                        <?= \CHtml::label('Assistant', 'assistant') ?>
                    </li>
                    <li>
                        <?= \CHtml::checkBox('assistant_role'); ?>
                        <?= \CHtml::label('Assistant role', 'assistant_role') ?>
                    </li>
                    <li>
                        <?= \CHtml::checkBox('supervising_surgeon'); ?>
                        <?= \CHtml::label('Supervising surgeon', 'supervising_surgeon') ?>
                    </li>
                    <li>
                        <?= \CHtml::checkBox('supervising_surgeon_role'); ?>
                        <?= \CHtml::label('Supervising surgeon role', 'supervising_surgeon_role') ?>
                    </li>
                    <li>
                        <?= \CHtml::checkBox('opnote_comments'); ?>
                        <?= \CHtml::label('Operation Note Comments', 'opnote_comments') ?>
                    </li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>
                <h3>Patient Data</h3>
                <ul>
                    <li>
                        <?= \CHtml::checkBox('patient_oph_diagnoses'); ?>
                        <?= \CHtml::label('Patient Ophthalmic Diagnoses', 'patient_oph_diagnoses') ?>
                    </li>
                </ul>
            </td>
        </tr>
        </tbody>
    </table>
    <?php $this->endWidget() ?>

    <div class="errors alert-box alert with-icon" style="display: none">
        <p>Please fix the following input errors:</p>
        <ul>
        </ul>
    </div>

    <table class="standard cols-full">
        <tbody>
        <tr>
            <td>
                <div class="row flex-layout flex-right">
                    <button type="submit" class="button green hint display-module-report" name="run"><span
                                class="button-span button-span-blue">Display report</span></button>
                    &nbsp;
                    <button type="submit" class="button green hint download-module-report" name="run"><span
                                class="button-span button-span-blue">Download report</span></button>
                    <i class="spinner loader" style="display: none;"></i>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
    <div class="js-report-summary report-summary" style="display: none; overflow-y:scroll"></div>
</div>
