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
    <?php $this->renderPartial('//report/_institution_table_row', ['field_name' => "institution_id"]);?>
    <tr>
      <td>
            <?=\CHtml::label('Date From', 'date_from') ?>
      </td>
      <td>
        <input id="date_from"
               placeholder="dd-mm-yyyy"
               class="start-date"
               name="date_from"
               autocomplete="off"
               value= <?= date('d-m-Y'); ?>
        >
      </td>
      <td>
            <?=\CHtml::label('Date To', 'date_to') ?>
      </td>
      <td>
        <input id="date_to"
               placeholder="dd-mm-yyyy"
               class="end-date"
               name="date_to"
               autocomplete="off"
               value= <?= date('d-m-Y'); ?>
        >
      </td>
    </tr>
    <tr>
      <td>
            <?=\CHtml::label('Given by', 'given_by_id') ?>
      </td>
      <td>
            <?php if (Yii::app()->getAuthManager()->checkAccess('Report', Yii::app()->user->id)) : ?>
                <?=\CHtml::dropDownList(
                    'given_by_id',
                    '',
                    CHtml::listData(
                        User::model()->findAll(array('order' => 'first_name asc,last_name asc')),
                        'id',
                        'fullName'
                    ),
                    array('empty' => 'Select')
                ) ?>
            <?php else : ?>
                <?php
                $user = User::model()->findByPk(Yii::app()->user->id);
                echo CHtml::dropDownList(
                    null,
                    '',
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
            <?=\CHtml::label('Drugs', 'drug_id') ?>
      </td>
      <td>
            <?=\CHtml::dropDownList(
                'drug_id',
                '',
                CHtml::listData(
                    OphTrIntravitrealinjection_Treatment_Drug::model()->findAll(
                        array('order' => 'name asc')
                    ),
                    'id',
                    'name'
                ),
                array('empty' => 'Select')
            ) ?>
      </td>
    </tr>
    <tr>
      <td>
            <?=\CHtml::label('Pre Injection Antiseptic', 'pre_antisept_drug_id') ?>
      </td>
      <td>
            <?=\CHtml::dropDownList(
                'pre_antisept_drug_id',
                '',
                CHtml::listData(
                    OphTrIntravitrealinjection_AntiSepticDrug::model()->findAll(
                        array('order' => 'name asc')
                    ),
                    'id',
                    'name'
                ),
                array('empty' => 'Select')
            ) ?>
      </td>
      <td>
        <input type="hidden" name="summary" value="0"/>
            <?=\CHtml::checkBox('summary'); ?>
            <?=\CHtml::label('Summarise patient data', 'summary') ?>
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
            <?=\CHtml::checkBox('pre_va'); ?>
            <?=\CHtml::label('Pre injection VA', 'pre_va') ?>
      </td>
      <td>
        <input type="hidden" name="post_va" value="0"/>
            <?=\CHtml::checkBox('post_va'); ?>
            <?=\CHtml::label('Post injection VA', 'post_va') ?>
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
          <i class="spinner loader" style="display: none;"></i>
        </div>
      </td>
    </tr>
    </tbody>
  </table>
  <div class="js-report-summary report-summary" style="display: none;">
  </div>
</div>
