<div id="patientID">
	<div class="i_patient">
		<?php echo CHtml::link('Patient Summary',array('/patient/view/'.$this->patient->id),array('class'=>'small'))?>
		<img class="i_patient" src="<?php echo Yii::app()->createUrl('img/_elements/icons/patient_small.png')?>" alt="patient_small" width="26" height="30" />
	</div>
	<div class="patientReminder">
		<div class="patientName">
			<?php echo CHtml::link($this->patient->getDisplayName(),array('/patient/view/'.$this->patient->id)) ?>
			<?php if($this->patient->isDeceased()) { ?>(Deceased)<?php } ?>
		</div>
		<div class="hospitalNumber">No. <?php echo $this->patient->hos_num?></div>
		<div class="nhsNumber"><span class="hide">NHS number:</span><?php echo $this->patient->nhsnum?></div>
	</div>
</div><!-- #patientID -->
