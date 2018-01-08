<div class="box reports">
	<div class="report-fields">
		<h2>Therapy application report</h2>
		<form>
		<div class="row field-row">
			<div class="large-2 column">
				<?php echo CHtml::label('Consultant', 'firm_id') ?>
			</div>
			<div class="large-4 column end">

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
			</div>
		</div>
		<div class="row field-row">
			<div class="large-2 column">
				<?php echo CHtml::label('Date From', 'date_from') ?>
			</div>
			<div class="large-4 column end">
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
			</div>
		</div>
		<div class="row field-row">
			<div class="large-2 column">
				<?php echo CHtml::label('Date To', 'date_to') ?>
			</div>
			<div class="large-4 column end">
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
			</div>
		</div>

		<h3>Submission Information</h3>
		<div class="row field-row">
			<div class="large-2 column">
				<?php echo CHtml::label('Submission Date', 'submission') ?>
			</div>
			<div class="large-4 column end">
				<?php echo CHtml::checkBox('submission'); ?>
			</div>
		</div>
		<h3>Injection Information</h3>
		<div class="row field-row">
			<div class="large-2 column">
				<?php echo CHtml::label('First Injection', 'first_injection') ?>
			</div>
			<div class="large-4 column end">
				<?php echo CHtml::checkBox('first_injection'); ?>
			</div>
		</div>
		<div class="row field-row">
			<div class="large-2 column">
				<?php echo CHtml::label('Last Injection', 'last_injection') ?>
			</div>
			<div class="large-4 column end">
				<?php echo CHtml::checkBox('last_injection'); ?>
			</div>
		</div>
			<div class="row field-row">
				<div class="large-4 column end">
                    <?php
                    $htmlOptions = array();
                        if(!$this->canUseTherapyReport()){
                            $htmlOptions = array(
                                    'disabled' => 'disabled',
                                    'readonly' => 'readonly'
                            );
                        }

                    ?>
					<?php echo CHtml::submitButton('Generate Report', $htmlOptions) ?>
				</div>
			</div>
		</form>
		</div>
	</div>
</div>
