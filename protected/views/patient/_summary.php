<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>
<div id="box_gradient_top"></div>
<div id="box_gradient_bottom">
<div class="details" id="personal_details">
	<h3>Personal Details:</h3>
	<div class="data_row">
		<div class="data_label">First name(s):</div>
		<div class="data_value"><?php echo $model->first_name; ?></div>
	</div>
	<div class="data_row">
		<div class="data_label">Last name:</div>
		<div class="data_value"><?php echo $model->last_name; ?></div>
	</div>
	<div class="data_row address">
		<div class="data_label">Address:</div>
		<div class="data_value">
<?php
	if (!empty($address)) {
		$fields = array(
			'address1' => $address->address1,
			'address2' => $address->address2,
			'city' => $address->city,
			'county' => $address->county,
			'postcode' => $address->postcode);
		$addressList = array_filter($fields, 'filter_nulls');

		$numLines = 1;
		foreach ($addressList as $name => $string) {
			if ($name === 'postcode') {
				$string = strtoupper($string);
			}
			if ($name != 'email') {
				echo $string;
			}
			if (!empty($string) && $string != end($addressList)) {
				echo '<br />';
			}
			$numLines++;
		}
		// display extra blank lines if needed for padding
		for ($numLines; $numLines <= 5; $numLines++) {
			echo '<br />';
		}
	} else {
		echo 'Unknown';
	} ?>
		</div>
	</div>
	<div class="data_row">
		<div class="data_label">Date of Birth:</div>
		<div class="data_value"><?php
		echo $model->NHSDate('dob');
		echo ' (Age ' . $model->getAge()  . ')';
		?></div>
	</div>
	<div class="data_row row_buffer">
		<div class="data_label">Gender:</div>
		<div class="data_value"><?php echo $model->gender == 'F' ? 'Female' : 'Male'; ?></div>
	</div>
</div>
<div class="details" id="contact_details">
	<h3>Contact Details:</h3>
	<div class="data_row telephone">
		<div class="data_label">Telephone:</div>
		<div class="data_value"><?php echo !empty($model->primary_phone)
			? $model->primary_phone : 'Unknown'; ?></div>
	</div>
	<div class="data_row row_buffer">
		<div class="data_label">Email:</div>
		<div class="data_value"><?php echo !empty($address->email)
                        ? $address->email : 'Unknown'; ?></div>

	</div>
	<div class="data_row row_buffer">
		<div class="data_label">Next of Kin:</div>
		<div class="data_value">Unknown</div>
	</div>
</div>
<div class="details" id="recent_episodes">
	<h3>Recent Episodes:</h3>
	<div id="view_all"></div>
	<div class="clear"></div>
<?php
	$this->widget('zii.widgets.grid.CGridView', array(
		'dataProvider'=>$episodes,
		'columns'=>array(
			array('name'=>'Start Date','value'=>'$data->NHSDate(\'start_date\')'),
			array('name'=>'End Date','value'=>'$data->NHSDate(\'end_date\')'),
			array('name'=>'Firm', 'value'=>'$data->firm->name'),
			array('name'=>'Subspecialty', 'value'=>'$data->firm->serviceSubspecialtyAssignment->subspecialty->name'),
			array('name'=>'Eye','value'=>'$data->getPrincipalDiagnosisEyeText()'), // 'diagnosis.location',
			array('name'=>'Diagnosis','value'=>'$data->getPrincipalDiagnosisDisorderTerm()'), // 'disorder.name'
//			array('class'=>'CButtonColumn', 'buttons'=>array(
//				'view'=>array(
//					'label'=>'View',
//					'url'=>'"#"',
//					'click'=>'function() {console.log("test");$(".ui-tabs").tabs("select", 1); return false;}',
//					),
//				),
//				'template'=>'{view} View',
//			),
		),
		'enablePagination'=>false,
		'enableSorting'=>false,
		'summaryText'=>'',
		'emptyText'=>'No episodes found.'
	)); ?>
</div>
</div>
<script type="text/javascript">
	$('#view_all').die('click').live('click', function() {
		$('#patient-tabs').tabs('select', 1);
	});
</script>
<?php
function filter_nulls($data) {
	return $data !== null;
}
