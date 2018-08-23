<h2>Intravitreal Injection Report</h2>

<div class="row divider">
    <?php
    $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
        'id' => 'module-report-form',
        'enableAjaxValidation' => false,
        'layoutColumns' => array('label' => 2, 'field' => 10),
        'action' => Yii::app()->createUrl('/' . $this->module->id . '/report/downloadReport'),
    )) ?>
  <input type="hidden" name="report-name" value="Injections"/>
  <table class="standard cols-full">
    <tbody>
    <tr>
      <td>
          <?php echo CHtml::label('Date From', 'date_from') ?>
      </td>
      <td>
          <?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
              'name' => 'date_from',
              'id' => 'date_from',
              'options' => array(
                  'showAnim' => 'fold',
                  'dateFormat' => Helper::NHS_DATE_FORMAT_JS,
                  'maxDate' => 0,
                  'defaultDate' => '-1y',
              ),
              'value' => date('j M Y', strtotime('-1 year')),
          )) ?>
      </td>
      <td>
          <?php echo CHtml::label('Date To', 'date_to') ?>
      </td>
      <td>
          <?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
              'name' => 'date_to',
              'id' => 'date_to',
              'options' => array(
                  'showAnim' => 'fold',
                  'dateFormat' => Helper::NHS_DATE_FORMAT_JS,
                  'maxDate' => 0,
                  'defaultDate' => 0,
              ),
              'value' => date('j M Y'),
          )) ?>
      </td>
    </tr>
    <tr>
      <td>
          <?php echo CHtml::label('Given by', 'given_by_id') ?>
      </td>
      <td>
          <?php if (Yii::app()->getAuthManager()->checkAccess('Report', Yii::app()->user->id)): ?>
              <?php echo CHtml::dropDownList('given_by_id', '',
                  CHtml::listData(User::model()->findAll(array('order' => 'first_name asc,last_name asc')), 'id',
                      'fullName'), array('empty' => '- Please select -')) ?>
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
              echo CHtml::hiddenField('given_by_id', Yii::app()->user->id);
              ?>
          <?php endif ?>
      </td>
      <td>
          <?php echo CHtml::label('Drugs', 'drug_id') ?>
      </td>
      <td>
          <?php echo CHtml::dropDownList(
              'drug_id',
              '',
              CHtml::listData(
                  OphTrIntravitrealinjection_Treatment_Drug::model()->findAll(
                      array('order' => 'name asc')), 'id', 'name'),
              array('empty' => '- Please select -')) ?>
      </td>
    </tr>
    <tr>
      <td>
          <?php echo CHtml::label('Pre Injection Antiseptic', 'pre_antisept_drug_id') ?>
      </td>
      <td>
          <?php echo CHtml::dropDownList(
              'pre_antisept_drug_id',
              '',
              CHtml::listData(
                  OphTrIntravitrealinjection_AntiSepticDrug::model()->findAll(
                      array('order' => 'name asc')),
                  'id', 'name'),
              array('empty' => '- Please select -')) ?>
      </td>
      <td>
        <input type="hidden" name="summary" value="0"/>
          <?php echo CHtml::checkBox('summary'); ?>
          <?php echo CHtml::label('Summarise patient data', 'summary') ?>
      </td>
    </tr>
    </tbody>
  </table>
  <table class="standard cols-full">
    <thead>
    <tr>
      <th>Examination Information</th>
    </tr>
    </thead>
    <tbody>
    <tr>
      <td>
        <input type="hidden" name="pre_va" value="0"/>
          <?php echo CHtml::checkBox('pre_va'); ?>
          <?php echo CHtml::label('Pre injection VA', 'pre_va') ?>
      </td>
      <td>
        <input type="hidden" name="post_va" value="0"/>
          <?php echo CHtml::checkBox('post_va'); ?>
          <?php echo CHtml::label('Post injection VA', 'post_va') ?>
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
          <button type="submit" class="button green hint display-module-report" name="run">
            <span class="button-span button-span-blue">Display report</span>
          </button>
          &nbsp;
          <button type="submit" class="button green hint download-module-report" name="run">
            <span class="button-span button-span-blue">Download report</span>
          </button>
          <img class="loader" style="display: none;"
               src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif') ?>" alt="loading..."/>&nbsp;
        </div>
      </td>
    </tr>
    </tbody>
  </table>
  <div class="js-report-summary" style="display: none;">
  </div>
</div>
