<h2>Therapy application report</h2>
<div class="row divider">
		<form>
      <table class="standard cols-full">
        <tbody>
        <tr>
          <td><?php echo CHtml::label('Consultant', 'firm_id') ?></td>
          <td>
              <?php if ( Yii::app()->getAuthManager()->checkAccess('Report', Yii::app()->user->id) ):?>
                  <?php echo CHtml::dropDownList('firm_id', null, $firms, array('empty' => 'All consultants')) ?>
              <?php else: ?>
                  <?php
                  $firm = Firm::model()->findByAttributes( array('consultant_id' => Yii::app()->user->id));

                  if($firm) {
                      echo CHtml::dropDownList(null, '',
                          array($firm->id => $firm->name),
                          array('disabled' => 'disabled', 'readonly' => 'readonly', 'style' => 'background-color:#D3D3D3;') //for some reason the chrome doesn't gray out
                      );
                      echo CHtml::hiddenField('consultant_id', $firm->id);
                  } else {
                      echo CHtml::dropDownList(null, '',array(),
                          array(  'disabled' => 'disabled',
                              'readonly' => 'readonly',
                              'style' => 'background-color:#D3D3D3;',
                              'empty' => '- select -')); //for some reason the chrome doesn't gray out
                  }
                  ?>
              <?php endif ?>
          </td>
        </tr>
        <tr>
          <td><?php echo CHtml::label('Date From', 'date_from') ?></td>
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
                  'value' => $date_from,
              ))?>
          </td>
          <td><?php echo CHtml::label('Date To', 'date_to') ?></td>
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
                  'value' => $date_to,
              ))?>
          </td>
        </tr>
        <tr>
          <td>Submission Information</td>
          <td>
              <?php echo CHtml::label('Submission Date', 'submission') ?>
              <?php echo CHtml::checkBox('submission'); ?>
          </td>
        </tr>
        <tr>
          <td>Injection Information</td>
          <td>
              <?php echo CHtml::label('First Injection', 'first_injection') ?>
              <?php echo CHtml::checkBox('first_injection'); ?>
          </td>
          <td>
              <?php echo CHtml::label('Last Injection', 'last_injection') ?>
              <?php echo CHtml::checkBox('last_injection'); ?>
          </td>
        </tr>
        </tbody>
      </table>
      <div class="row flex-layout flex-right">
          <?php
          $htmlOptions = array('class' => 'button green hint');
          if (!$this->canUseTherapyReport()) {
              $htmlOptions = array(
                  'disabled' => 'disabled',
                  'readonly' => 'readonly',
              );
          } ?>
					<?php echo CHtml::submitButton('Generate Report', $htmlOptions) ?>
        &nbsp; &nbsp;
      </div>
		</form>
</div>
