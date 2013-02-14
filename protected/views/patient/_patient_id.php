<div id="patientID">
	<div class="patientReminder clearfix">
		<div class="patientName">
			<?php echo CHtml::link($this->patient->getDisplayName(),array('/patient/view/'.$this->patient->id)) ?>
			(<?php if($this->patient->isDeceased()) { ?>Deceased<?php } else { echo $this->patient->getAge(); } ?>)
		</div>
		<div class="hospitalNumber">No. <?php echo $this->patient->hos_num?></div>
		<div class="nhsNumber"><span class="hide">NHS number:</span><?php echo $this->patient->nhsnum?></div>
		<ul class="icons">
			<li class="gender <?php echo strtolower($this->patient->getGenderString()) ?>"><?php echo $this->patient->getGenderString() ?></li>
		</ul>
		<div class="i_patient">
			<?php echo CHtml::link('Patient Summary',array('/patient/view/'.$this->patient->id)); ?>
		</div>
	</div>
</div><!-- #patientID -->
