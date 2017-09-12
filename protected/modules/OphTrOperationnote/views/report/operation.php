<div class="box reports">
	<div class="report-fields">
		<h2>Operation Report</h2>
		<?php
        $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
            'id' => 'module-report-form',
            'enableAjaxValidation' => false,
            'layoutColumns' => array('label' => 2, 'field' => 10),
            'action' => Yii::app()->createUrl('/'.$this->module->id.'/report/downloadReport'),
        ))?>
			<input type="hidden" name="report-name" value="Operations" />
			<div class="row field-row">
				<div class="large-2 column">
					<?php echo CHtml::label('Surgeon', 'surgeon_id') ?>
				</div>
				<div class="large-4 column end">
					<?php echo CHtml::dropDownList('surgeon_id', null, $surgeons, array('empty' => 'All surgeons')) ?>
				</div>
			</div>
			<?php
                $this->widget('application.widgets.ProcedureSelection', array(
                    'newRecord' => true,
                    'last' => true,
                ));
            ?>
			<div class="row field-row">
				<div class="large-2 column">
					<?php echo CHtml::label('Cataract Complications', 'cat_complications'); ?>
				</div>
				<div class="large-4 column end">
					<?php $this->widget('application.widgets.MultiSelectList', array(
                            'field' => 'complications',
                            'options' => CHtml::listData(OphTrOperationnote_CataractComplications::model()->findAll(), 'id', 'name'),
                            'htmlOptions' => array('empty' => '- Complications -', 'multiple' => 'multiple', 'nowrapper' => true),
                    )); ?>
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
                            'value' => @$_GET['date_from'],
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
                            'value' => @$_GET['date_to'],
                        ))?>
				</div>
			</div>
			<h3>Operation Booking</h3>
			<div class="row field-row">
				<div class="large-2 column">
					&nbsp;
				</div>
				<div class="large-4 column end">
					<?php echo CHtml::checkBox('bookingcomments'); ?>
					<?php echo CHtml::label('Comments', 'bookingcomments') ?>
				</div>
			</div>
			<div class="row field-row">
				<div class="large-2 column">
					&nbsp;
				</div>
				<div class="large-4 column end">
					<?php echo CHtml::checkBox('booking_diagnosis'); ?>
					<?php echo CHtml::label('Operation booking diagnosis', 'booking_diagnosis') ?>
				</div>
			</div>
			<div class="row field-row">
				<div class="large-2 column">
					&nbsp;
				</div>
				<div class="large-4 column end">
					<?php echo CHtml::checkBox('surgerydate'); ?>
					<?php echo CHtml::label('Surgery Date', 'surgerydate') ?>
				</div>
			</div>
			<div class="row field-row">
				<div class="large-2 column">
					&nbsp;
				</div>
				<div class="large-4 column end">
					<?php echo CHtml::checkBox('theatre'); ?>
					<?php echo CHtml::label('Theatre', 'theatre') ?>
				</div>
			</div>

			<h3>Examination</h3>
			<div class="row field-row">
				<div class="large-2 column">
					&nbsp;
				</div>
				<div class="large-4 column end">
					<?php echo CHtml::checkBox('comorbidities'); ?>
					<?php echo CHtml::label('Comorbidities', 'comorbidities') ?>
				</div>
			</div>
			<div class="row field-row">
				<div class="large-2 column">
					&nbsp;
				</div>
				<div class="large-4 column end">
					<?php echo CHtml::checkBox('first_eye'); ?>
					<?php echo CHtml::label('First or Second Eye', 'first_eye') ?>
				</div>
			</div>
			<div class="row field-row">
				<div class="large-2 column">
					&nbsp;
				</div>
				<div class="large-4 column end">
					<?php echo CHtml::checkBox('refraction_values'); ?>
					<?php echo CHtml::label('Refraction Values', 'refraction_values') ?>
				</div>
			</div>
			<div class="row field-row">
				<div class="large-2 column">
					&nbsp;
				</div>
				<div class="large-4 column end">
					<?php echo CHtml::checkBox('target_refraction'); ?>
					<?php echo CHtml::label('Target Refraction', 'target_refraction') ?>
				</div>
			</div>
			<div class="row field-row">
				<div class="large-2 column">
					&nbsp;
				</div>
				<div class="large-4 column end">
					<?php echo CHtml::checkBox('cataract_surgical_management'); ?>
					<?php echo CHtml::label('Cataract Surgical Management', 'cataract_surgical_management') ?>
				</div>
			</div>
			<div class="row field-row">
				<div class="large-2 column">
					&nbsp;
				</div>
				<div class="large-4 column end">
					<?php echo CHtml::checkBox('va_values'); ?>
					<?php echo CHtml::label('VA Values', 'va_values') ?>
				</div>
			</div>
			<h3>Operation Note</h3>
			<div class="row field-row">
				<div class="large-2 column">
					&nbsp;
				</div>
				<div class="large-4 column end">
					<?php echo CHtml::checkBox('cataract_report'); ?>
					<?php echo CHtml::label('Cataract Report', 'cataract_report') ?>
				</div>
			</div>
			<div class="row field-row">
				<div class="large-2 column">
					&nbsp;
				</div>
				<div class="large-4 column end">
					<?php echo CHtml::checkBox('incision_site'); ?>
					<?php echo CHtml::label('Cataract Operation Details', 'incision_site') ?>
				</div>
			</div>
		<div class="row field-row">
			<div class="large-2 column">
				&nbsp;
			</div>
			<div class="large-4 column end">
				<?php echo CHtml::checkBox('cataract_complication_notes'); ?>
				<?php echo CHtml::label('Cataract Complication Notes', 'cataract_complication_notes') ?>
			</div>
		</div>
			<div class="row field-row">
				<div class="large-2 column">
					&nbsp;
				</div>
				<div class="large-4 column end">
					<?php echo CHtml::checkBox('tamponade_used'); ?>
					<?php echo CHtml::label('Tamponade Used', 'tamponade_used') ?>
				</div>
			</div>
			<div class="row field-row">
				<div class="large-2 column">
					&nbsp;
				</div>
				<div class="large-4 column end">
					<?php echo CHtml::checkBox('anaesthetic_type'); ?>
					<?php echo CHtml::label('Anaesthetic Type', 'anaesthetic_type') ?>
				</div>
			</div>
			<div class="row field-row">
				<div class="large-2 column">
					&nbsp;
				</div>
				<div class="large-4 column end">
					<?php echo CHtml::checkBox('anaesthetic_delivery'); ?>
					<?php echo CHtml::label('Anaesthetic Delivery', 'anaesthetic_delivery') ?>
				</div>
			</div>
			<div class="row field-row">
				<div class="large-2 column">
					&nbsp;
				</div>
				<div class="large-4 column end">
					<?php echo CHtml::checkBox('anaesthetic_complications'); ?>
					<?php echo CHtml::label('Anaesthetic Complications', 'anaesthetic_complications') ?>
				</div>
			</div>
			<div class="row field-row">
				<div class="large-2 column">
					&nbsp;
				</div>
				<div class="large-4 column end">
					<?php echo CHtml::checkBox('anaesthetic_comments'); ?>
					<?php echo CHtml::label('Anaesthetic Comments', 'anaesthetic_comments') ?>
				</div>
			</div>
			<div class="row field-row">
				<div class="large-2 column">
					&nbsp;
				</div>
				<div class="large-4 column end">
					<?php echo CHtml::checkBox('surgeon'); ?>
					<?php echo CHtml::label('Surgeon', 'surgeon') ?>
				</div>
			</div>
			<div class="row field-row">
				<div class="large-2 column">
					&nbsp;
				</div>
				<div class="large-4 column end">
					<?php echo CHtml::checkBox('surgeon_role'); ?>
					<?php echo CHtml::label('Surgeon role', 'surgeon_role') ?>
				</div>
			</div>
			<div class="row field-row">
				<div class="large-2 column">
					&nbsp;
				</div>
				<div class="large-4 column end">
					<?php echo CHtml::checkBox('assistant'); ?>
					<?php echo CHtml::label('Assistant', 'assistant') ?>
				</div>
			</div>
			<div class="row field-row">
				<div class="large-2 column">
					&nbsp;
				</div>
				<div class="large-4 column end">
					<?php echo CHtml::checkBox('assistant_role'); ?>
					<?php echo CHtml::label('Assistant role', 'assistant_role') ?>
				</div>
			</div>
			<div class="row field-row">
				<div class="large-2 column">
					&nbsp;
				</div>
				<div class="large-4 column end">
					<?php echo CHtml::checkBox('supervising_surgeon'); ?>
					<?php echo CHtml::label('Supervising surgeon', 'supervising_surgeon') ?>
				</div>
			</div>
			<div class="row field-row">
				<div class="large-2 column">
					&nbsp;
				</div>
				<div class="large-4 column end">
					<?php echo CHtml::checkBox('supervising_surgeon_role'); ?>
					<?php echo CHtml::label('Supervising surgeon role', 'supervising_surgeon_role') ?>
				</div>
			</div>
			<div class="row field-row">
				<div class="large-2 column">
					&nbsp;
				</div>
				<div class="large-4 column end">
					<?php echo CHtml::checkBox('opnote_comments'); ?>
					<?php echo CHtml::label('Operation Note Comments', 'opnote_comments') ?>
				</div>
			</div>
			<h3>Patient Data</h3>
			<div class="row field-row">
				<div class="large-2 column">
					&nbsp;
				</div>
				<div class="large-4 column end">
					<?php echo CHtml::checkBox('patient_oph_diagnoses'); ?>
					<?php echo CHtml::label('Patient Ophthalmic Diagnoses', 'patient_oph_diagnoses') ?>
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
		<div class="reportSummary report curvybox white blueborder" style="display: none; overflow-y:scroll">
		</div>
	</div>
</div>
