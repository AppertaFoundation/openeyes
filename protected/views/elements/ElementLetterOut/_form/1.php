<?php
/*
_____________________________________________________________________________
(C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
(C) OpenEyes Foundation, 2011
This file is part of OpenEyes.
OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
_____________________________________________________________________________
http://www.openeyes.org.uk   info@openeyes.org.uk
--
*/
 $service = $model->getService() ?>

<?php $allPhraseOptions = $service->getAllPhraseOptions() ?>

<?php

$templateOptions = array();

$templates = $service->getLetterTemplates();

foreach ($templates as $key => $template) {
	$templateOptions[$key] = $template['name'];
}

?>
<script type="text/javascript">
	var contactData = new Array();

<?php

$patientContactOptions = array();

foreach ($service->getContactData() as $key => $data) {

	$patientContactOptions[$key] = $data['identifier'];
?>
	contactData['<?= $key ?>'] = new Array(
		'<?php echo $data['address'] ?>',
		'<?php echo $data['full_name'] ?>',
		'<?php echo $data['dear_name'] ?>',
		'<?php if (!empty($data['nick_name'])) { echo $data['nick_name']; } ?>'
	);

<?php
}
?>

	templates = new Array();
<?php

$templateOptions = array();

$templates = $service->getLetterTemplates();

foreach ($templates as $key => $template) {

	$templateOptions[$key] = $template['name'];
?>
	templates['<?= $key ?>'] = new Array(
		'<?php echo $template['phrase'] ?>',
		'<?php echo $template['send_to'] ?>',
		'<?php echo $template['cc'] ?>'
	);
<?php
}

?>
</script>

<h2 class="element_letterout">Letter out</h2>

<div class="row_element_letterout">
	<h3 class="element_letterout"><?php echo $form->labelEx($model,'to_address'); ?></h3 class="element_letterout">
	<span class="element_letterout_left">
	<?php echo CHtml::dropDownList('to', '', $patientContactOptions, array('empty'=>'To')); ?>
	<?php echo CHtml::dropDownList('template', '', $templateOptions, array('empty'=>'Template')); ?>
	</span>
	<span class="element_letterout_right">
		<?php echo $form->textArea($model,'to_address',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'to_address'); ?>
	</span>
</div>

<div class="row_element_letterout">
	<h3 class="element_letterout"><?php echo $form->labelEx($model,'date'); ?></h3 class="element_letterout">
	<span class="element_letterout_left">...
	</span>
	<span class="element_letterout_right">
		<?php
			if (empty($model->date)) {
				echo CHtml::textField('ElementLetterOut[date]', date("Y-m-d"));
			} else {
				echo $form->textField($model, 'date');
			}
		?>
		<?php echo $form->error($model,'date'); ?>
	</span>
</div>

<div class="row_element_letterout">
	<h3 class="element_letterout"><?php echo $form->labelEx($model,'dear'); ?></h3 class="element_letterout">
	<span class="element_letterout_left">...
	</span>
	<span class="element_letterout_right">
		<?php echo $form->textField($model, 'dear', array('size' => 50)); ?>
		<?php echo $form->error($model,'dear'); ?>
	</span>
</div>

<div class="row_element_letterout">
	<span class="element_letterout_right">
		Nickname:
		<?php echo CHtml::checkBox('use_nickname', '', array()); ?>
	</span>
</div>

<div class="row_element_letterout">
	<h3 class="element_letterout"><?php echo $form->labelEx($model,'re'); ?></h3 class="element_letterout">
	<span class="element_letterout_left">...
	</span>
	<span class="element_letterout_right">
		<?php
			if (empty($model->re)) {
				// The user is creating a new letterout, display default re: value for patient
				echo CHtml::textField('ElementLetterOut[re]', $service->getDefaultRe(), array('size' => 100));
			} else {
				echo $form->textField($model, 're', array('size' => 100));
			}
		?>
		<?php echo $form->error($model,'re'); ?>
	</span>
</div>
<div class="row_element_letterout">
	<h3 class="element_letterout"><?php echo $form->labelEx($model,'value'); ?></h3 class="element_letterout">
	<span class="element_letterout_left">
	<?php echo CHtml::dropDownList('Introduction', '', $allPhraseOptions['Introduction'], array('empty'=>'Introduction')); ?>
	<br />
	<?php echo CHtml::dropDownList('Findings', '', $allPhraseOptions['Findings'], array('empty'=>'Findings')); ?>
	<br />
	<?php echo CHtml::dropDownList('Diagnosis', '', $allPhraseOptions['Diagnosis'], array('empty'=>'Diagnosis')); ?>
	<br />
	<?php echo CHtml::dropDownList('Management', '', $allPhraseOptions['Management'], array('empty'=>'Management')); ?>
	<br />
	<?php echo CHtml::dropDownList('Drugs', '', $allPhraseOptions['Drugs'], array('empty'=>'Drugs')); ?>
	<br />
	<?php echo CHtml::dropDownList('Outcome', '', $allPhraseOptions['Outcome'], array('empty'=>'Outcome')); ?>
	</span>
	<span class="element_letterout_right">
		<?php echo $form->textArea($model,'value',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'value'); ?>
	</span>
</div>

<div class="row_element_letterout">
	<h3 class="element_letterout"><?php echo $form->labelEx($model,'from_address'); ?></h3 class="element_letterout">
	<span class="element_letterout_left">
	<?php echo CHtml::dropDownList('from', '', $service->getFromOptions(), array('empty'=>'From')); ?>
	</span>
	<span class="element_letterout_right">
		<?php echo $form->textArea($model,'from_address',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'from_address'); ?>
	</span>
</div>

<div class="row_element_letterout">
	<h3 class="element_letterout"><?php echo $form->labelEx($model,'cc'); ?></h3 class="element_letterout">
	<span class="element_letterout_left">
	<?php echo CHtml::dropDownList('cc_list', '', $patientContactOptions, array('empty'=>'CC')); ?>
	</span>
	<span class="element_letterout_right">
		<?php echo $form->textArea($model,'cc',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'cc'); ?>
	</span>
</div>
<script type="text/javascript">
	$(function() {
		$('#to').change(function() {
			// Populate the 'to' textarea
			if (!$('#to').val()) {
				return;
			}

			populateToAndContact($('#to').val());
		});
	});

	$(function() {
		$('#cc_list').change(function() {
			if (!$('#cc_list').val()) {
				return;
			}

			$('#ElementLetterOut_cc').append('cc: ' + contactData[$('#cc_list').val()][0] + "\n");
		});
	});

	$(function() {
		$('#Introduction').change(function() {
			populateValue('Introduction');
		});
	});
	$(function() {
		$('#Findings').change(function() {
			populateValue('Findings');
		});
	});
	$(function() {
		$('#Diagnosis').change(function() {
			populateValue('Diagnosis');
		});
	});
	$(function() {
		$('#Management').change(function() {
			populateValue('Management');
		});
	});
	$(function() {
		$('#Drugs').change(function() {
			populateValue('Drugs');
		});
	});
	$(function() {
		$('#Outcome').change(function() {
			populateValue('Outcome');
		});
	});

	$(function() {
		$('#template').change(function() {
			if ($('#template').val()) {
				template = templates[$('#template').val()];

				$('#ElementLetterOut_value').val(template[0]);

				populateToAndContact(template[1]);

				$('#ElementLetterOut_cc').val('cc: ' + contactData[template[2]][0]);
			}
		});
	});

        $(function() {
                $('#from').change(function() {
                        if ($('#from').val()) {
				$('#ElementLetterOut_from_address').val('Yours sincerely\n\n\n\n\n' + formatAddress($('#from').val()));
                        }
                });
        });

	function populateToAndContact(id) {
		contact = contactData[id];

		$('#ElementLetterOut_to_address').val(formatAddress(contact[0]));

		// The non-nickname is the default
		dear = contact[2];

		if (contact[3] == '') {
			// No nickname, grey out use_nickname box
			$("#use_nickname").attr("disabled", "disabled");
		} else {
			// Ungrey use_nickname box
			$("#use_nickname").removeAttr("disabled");

			if ($('#use_nickname').attr('checked')) {
				// A mickname is available and the use_nickname box is checked
				dear = contact[3];
			}
		}

		$('#ElementLetterOut_dear').val('Dear ' + dear);
	}

	function populateValue(phrase) {
		if ($('#' + phrase).val()) {
			$('#ElementLetterOut_value').append($('#' + phrase).val() + '. ');
		}
	}

	function formatAddress(address) {
		return address.split(', ').join(",\n");
	}
</script>