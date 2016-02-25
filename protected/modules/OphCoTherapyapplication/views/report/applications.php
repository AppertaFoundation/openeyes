<div class="box reports">
	<div class="report-fields">
		<h2>Therapy application report</h2>
		<form>
		<div class="row field-row">
			<div class="large-2 column">
				<?php echo CHtml::label('Consultant', 'firm_id') ?>
			</div>
			<div class="large-4 column end">
				<?php echo CHtml::dropDownList('firm_id', null, $firms, array('empty' => 'All consultants')) ?>
			</div>
		</div>
		<div class="row field-row">
			<div class="large-2 column">
				<?php echo CHtml::label('Date From', 'date_from') ?>
			</div>
			<div class="large-4 column end">
				<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
								'name'=>'date_from',
								'id'=>'date_from',
								'options'=>array(
										'showAnim'=>'fold',
										'dateFormat'=>Helper::NHS_DATE_FORMAT_JS,
										'maxDate'=> 0,
										'defaultDate' => "-1y"
								),
								'value'=> $date_from
						))?>
			</div>
		</div>
		<div class="row field-row">
			<div class="large-2 column">
				<?php echo CHtml::label('Date To', 'date_to') ?>
			</div>
			<div class="large-4 column end">
				<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
								'name'=>'date_to',
								'id'=>'date_to',
								'options'=>array(
										'showAnim'=>'fold',
										'dateFormat'=>Helper::NHS_DATE_FORMAT_JS,
										'maxDate'=> 0,
										'defaultDate' => 0
								),
								'value'=> $date_to
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
					<?php echo CHtml::submitButton('Generate Report') ?>
				</div>
			</div>
		</form>
		</div>
	</div>
</div>
