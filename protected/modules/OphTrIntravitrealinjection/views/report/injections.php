<div class="box reports">
	<div class="report-fields">
		<h2>Intravitreal Injection Report</h2>
		<?php
		$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
			'id'=>'module-report-form',
			'enableAjaxValidation'=>false,
			'layoutColumns' => array('label'=>2,'field'=>10),
			'action' => Yii::app()->createUrl('/'.$this->module->id.'/report/downloadReport'),
		))?>
			<input type="hidden" name="report-name" value="Injections" />
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
									'value'=> date('j M Y',strtotime('-1 year')),
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
									'value'=> date('j M Y'),
							))?>
				</div>
			</div>

			<div class="row field-row">
				<div class="large-2 column">
					<?php echo CHtml::label('Given by', 'given_by_id') ?>
				</div>
				<div class="large-4 column end">
					<?php echo CHtml::dropDownList('given_by_id','',CHtml::listData(User::model()->findAll(array('order' => 'first_name asc,last_name asc')),'id','fullName'),array('empty' => '- Please select -'))?>
				</div>
			</div>

			<div class="row field-row">
				<div class="large-2 column">
					<?php echo CHtml::label('Drugs', 'drug_id') ?>
				</div>
				<div class="large-4 column end">
					<?php echo CHtml::dropDownList('drug_id','',CHtml::listData(OphTrIntravitrealinjection_Treatment_Drug::model()->findAll(array('order' => 'name asc')),'id','name'),array('empty' => '- Please select -'))?>
				</div>
			</div>

			<div class="row field-row">
				<div class="large-2 column">
					<?php echo CHtml::label('Pre Injection Antiseptic', 'pre_antisept_drug_id') ?>
				</div>
				<div class="large-4 column end">
					<?php echo CHtml::dropDownList('pre_antisept_drug_id','',CHtml::listData(OphTrIntravitrealinjection_AntiSepticDrug::model()->findAll(array('order' => 'name asc')),'id','name'),array('empty' => '- Please select -'))?>
				</div>
			</div>

			<div class="row field-row">
				<div class="large-2 column">
					&nbsp;
				</div>
				<div class="large-4 column end">
					<input type="hidden" name="summary" value="0" />
					<?php echo CHtml::checkBox('summary'); ?>
					<?php echo CHtml::label('Summarise patient data', 'summary') ?>
				</div>
			</div>

			<div class="examinationInformation">
				<h3>Examination Information</h3>
				<div class="row field-row">
					<div class="large-2 column">
						&nbsp;
					</div>
					<div class="large-4 column end">
						<input type="hidden" name="pre_va" value="0" />
						<?php echo CHtml::checkBox('pre_va'); ?>
						<?php echo CHtml::label('Pre injection VA', 'pre_va') ?>
					</div>
				</div>
				<div class="row field-row">
					<div class="large-2 column">
						&nbsp;
					</div>
					<div class="large-4 column end">
						<input type="hidden" name="post_va" value="0" />
						<?php echo CHtml::checkBox('post_va'); ?>
						<?php echo CHtml::label('Post injection VA', 'post_va') ?>
					</div>
				</div>
			</div>
		<?php $this->endWidget()?>
		<div class="errors alert-box alert with-icon" style="display: none">
			<p>Please fix the following input errors:</p>
			<ul>
			</ul>
		</div>
		<div class="row field-row">
			<div class="large-6 column end">
				<button type="submit" class="classy blue mini display-module-report" name="run"><span class="button-span button-span-blue">Display report</span></button>
				<button type="submit" class="classy blue mini download-module-report" name="run"><span class="button-span button-span-blue">Download report</span></button>
				<img class="loader" style="display: none;" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" alt="loading..." />&nbsp;
			</div>
		</div>
		<div class="reportSummary report curvybox white blueborder" style="display: none;">
		</div>
	</div>
</div>
