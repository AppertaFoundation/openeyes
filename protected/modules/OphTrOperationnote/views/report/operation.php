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
        <tr class="col-gap">
          <td>
              <?php echo CHtml::label('Surgeon', 'surgeon_id') ?>
          </td>
          <td>
              <?php if (Yii::app()->getAuthManager()->checkAccess('Report', Yii::app()->user->id)): ?>
                  <?php echo CHtml::dropDownList('surgeon_id', null, $surgeons, array('empty' => 'All surgeons')) ?>
              <?php else: ?>
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
                  'newRecord' => true,
                  'last' => true,
                  'label' => '',
                  'popupButton'=> false,
              ));
              ?>
          </td>
        </tr>
        <tr class="col-gap">
          <td>
              <?php echo CHtml::label('Cataract Complications', 'cat_complications'); ?>
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
                <?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                    'name' => 'date_from',
                    'id' => 'date_from',
                    'options' => array(
                        'showAnim' => 'fold',
                        'dateFormat' => Helper::NHS_DATE_FORMAT_JS,
                        'maxDate' => 0,
                        'defaultDate' => '-1y',
                    ),
                    'htmlOptions'=>array(
                        'placeholder'=>'From',
                        'class'=>'cols-5'
                    ),
                    'value' => @$_GET['date_from'],
                )) ?>
                <?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                    'name' => 'date_to',
                    'id' => 'date_to',
                    'options' => array(
                        'showAnim' => 'fold',
                        'dateFormat' => Helper::NHS_DATE_FORMAT_JS,
                        'maxDate' => 0,
                        'defaultDate' => 0,
                    ),
                    'htmlOptions'=>array(
                        'placeholder'=>'To',
                        'class'=>'cols-5'
                    ),
                    'value' => @$_GET['date_to'],
                )) ?>
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
                <?php echo CHtml::checkBox('bookingcomments'); ?>
                <?php echo CHtml::label('Comments', 'bookingcomments') ?>
            </li>
            <li>
                <?php echo CHtml::checkBox('booking_diagnosis'); ?>
                <?php echo CHtml::label('Operation booking diagnosis', 'booking_diagnosis') ?>
            </li>
            <li>
                <?php echo CHtml::checkBox('surgerydate'); ?>
                <?php echo CHtml::label('Surgery Date', 'surgerydate') ?>
            </li>
            <li>
                <?php echo CHtml::checkBox('theatre'); ?>
                <?php echo CHtml::label('Theatre', 'theatre') ?>
            </li>
          </ul>
        </td>
        <td class="valign-top">
          <h3>Examination</h3>
          <ul>
            <li>
                <?php echo CHtml::checkBox('comorbidities'); ?>
                <?php echo CHtml::label('Comorbidities', 'comorbidities') ?>
            </li>
            <li>
                <?php echo CHtml::checkBox('first_eye'); ?>
                <?php echo CHtml::label('First or Second Eye', 'first_eye') ?>
            </li>
            <li>
                <?php echo CHtml::checkBox('refraction_values'); ?>
                <?php echo CHtml::label('Refraction Values', 'refraction_values') ?>
            </li>
            <li>
                <?php echo CHtml::checkBox('target_refraction'); ?>
                <?php echo CHtml::label('Target Refraction', 'target_refraction') ?>
            </li>
            <li>
                <?php echo CHtml::checkBox('cataract_surgical_management'); ?>
                <?php echo CHtml::label('Cataract Surgical Management', 'cataract_surgical_management') ?>
            </li>
            <li>
                <?php echo CHtml::checkBox('va_values'); ?>
                <?php echo CHtml::label('VA Values', 'va_values') ?>
            </li>
          </ul>
        </td>
        <td class="valign-top">
          <h3>Operation Note</h3>
          <ul>
            <li>
                <?php echo CHtml::checkBox('cataract_report'); ?>
                <?php echo CHtml::label('Cataract Report', 'cataract_report') ?>
            </li>
            <li>
                <?php echo CHtml::checkBox('incision_site'); ?>
                <?php echo CHtml::label('Cataract Operation Details', 'incision_site') ?>
            </li>
            <li>
                <?php echo CHtml::checkBox('cataract_complication_notes'); ?>
                <?php echo CHtml::label('Cataract Complication Notes', 'cataract_complication_notes') ?>
            </li>
            <li>
                <?php echo CHtml::checkBox('tamponade_used'); ?>
                <?php echo CHtml::label('Tamponade Used', 'tamponade_used') ?>
            </li>
            <li>
                <?php echo CHtml::checkBox('anaesthetic_type'); ?>
                <?php echo CHtml::label('Anaesthetic Type', 'anaesthetic_type') ?>
            </li>
            <li>
                <?php echo CHtml::checkBox('anaesthetic_delivery'); ?>
                <?php echo CHtml::label('Anaesthetic Delivery', 'anaesthetic_delivery') ?>
            </li>
            <li>
                <?php echo CHtml::checkBox('anaesthetic_complications'); ?>
                <?php echo CHtml::label('Anaesthetic Complications', 'anaesthetic_complications') ?>
            </li>
            <li>
                <?php echo CHtml::checkBox('anaesthetic_comments'); ?>
                <?php echo CHtml::label('Anaesthetic Comments', 'anaesthetic_comments') ?>
            </li>
            <li>
                <?php echo CHtml::checkBox('surgeon'); ?>
                <?php echo CHtml::label('Surgeon', 'surgeon') ?>
            </li>
            <li>
                <?php echo CHtml::checkBox('surgeon_role'); ?>
                <?php echo CHtml::label('Surgeon role', 'surgeon_role') ?>
            </li>
            <li>
                <?php echo CHtml::checkBox('assistant'); ?>
                <?php echo CHtml::label('Assistant', 'assistant') ?>
            </li>
            <li>
                <?php echo CHtml::checkBox('assistant_role'); ?>
                <?php echo CHtml::label('Assistant role', 'assistant_role') ?>
            </li>
            <li>
                <?php echo CHtml::checkBox('supervising_surgeon'); ?>
                <?php echo CHtml::label('Supervising surgeon', 'supervising_surgeon') ?>
            </li>
            <li>
                <?php echo CHtml::checkBox('supervising_surgeon_role'); ?>
                <?php echo CHtml::label('Supervising surgeon role', 'supervising_surgeon_role') ?>
            </li>
            <li>
                <?php echo CHtml::checkBox('opnote_comments'); ?>
                <?php echo CHtml::label('Operation Note Comments', 'opnote_comments') ?>
            </li>
          </ul>
        </td>
      </tr>
      <tr>
        <td>
          <h3>Patient Data</h3>
          <ul>
            <li>
                <?php echo CHtml::checkBox('patient_oph_diagnoses'); ?>
                <?php echo CHtml::label('Patient Ophthalmic Diagnoses', 'patient_oph_diagnoses') ?>
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
      <div class="row flex-layout flex-right">
        <button type="submit" class="classy green hint display-module-report" name="run"><span
              class="button-span button-span-blue">Display report</span></button>
        &nbsp;
        <button type="submit" class="classy green hint download-module-report" name="run"><span
              class="button-span button-span-blue">Download report</span></button>
        <img class="loader" style="display: none;"
             src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif') ?>" alt="loading..."/>&nbsp;
      </div>
    <div class="report-summary" style="display: none; overflow-y:scroll">
    </div>
</div>
