<div class="element-fields">
	<div class="fields-row">
		<?php
			echo $form->datePicker($this->event, 'event_date', array('maxDate' => 'today'), array(
				'style' => 'margin-left:8px',
			), array(
				'label' => 2,
				'field' => 2
			));
		?>
	</div>
</div>