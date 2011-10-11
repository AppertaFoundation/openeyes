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

$patientName = $model->first_name . ' ' . $model->last_name;
$this->breadcrumbs=array(
	"{$patientName} ({$model->hos_num})",
);
$this->widget('application.extensions.fancybox.EFancyBox', array(
	'target'=>'a.fancybox',
	'config'=>array()
	));

$address_str = '';

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
			$address_str .= $string;
		}
		if (!empty($string) && $string != end($addressList)) {
			$address_str .= '<br />';
		}
		$numLines++;
	}
	// display extra blank lines if needed for padding
	for ($numLines; $numLines <= 5; $numLines++) {
		$address_str .= '<br />';
	}
} else {
	$address_str .= 'Unknown';
} ?>
		<h2>Patient Summary</h2>
			<div class="wrapTwo clearfix">
				<div class="halfColumnLeft">
 
					<!-- double re-enforcement of mode change not currently required, but might be needed in the future
					<div class="patientBox withIcon">
						<img class="boxIcon" src="img/_elements/icons/patient.png" alt="patient" width="36" height="35" />
						<div id="patientHeader"><h3>Mrs Lucinda Smith <span class="normal">(1008504)</span></h3></div>
					</div>
					-->

					<div class="whiteBox" id="personal_details">
						<h4>Personal Details:</h4>
						<div class="data_row">
							<div class="data_label">First name(s):</div>
							<div class="data_value"><?php echo $model->first_name?></div>
						</div>
						<div class="data_row">
							<div class="data_label">Last name:</div>
							<div class="data_value"><?php echo $model->last_name?></div>
						</div>
						<div class="data_row">
							<div class="data_label">Address:</div>
							<div class="data_value"><?php echo $address_str?></div>
						</div>
						<div class="data_row">
							<div class="data_label">Date of Birth:</div>
							<div class="data_value"><?php echo date('jS F, Y', strtotime($model->dob)).' (Age '.$model->getAge().')'?></div>
						</div>
						<div class="data_row">
							<div class="data_label">Gender:</div>
							<div class="data_value"><?php echo $model->gender == 'F' ? 'Female' : 'Male'?></div>
						</div>
					</div> <!-- #personal_details -->
					<div class="whiteBox" id="contact_details">
						<h4>Contact Details:</h4>
						<div class="data_row">
							<div class="data_label">Telephone:</div>
							<div class="data_value"><?php echo !empty($model->primary_phone) ? $model->primary_phone : 'Unknown'?></div>
						</div>
						<div class="data_row">
							<div class="data_label">Email:</div>
							<div class="data_value"><?php echo !empty($address->email) ? $address->email : 'Unknown'?></div>

						</div>
						<div class="data_row">
							<div class="data_label">Next of Kin:</div>
							<div class="data_value">Unknown</div>
						</div>
					</div>
				</div>	<!-- .halfColumn -->

				<div class="halfColumnRight">
					<div class="blueBox">
						<h5>All Episodes<span style="float:right;">&nbsp; open <?php echo $episodes_open?> &nbsp;|&nbsp;<span style="font-weight:normal;">closed <?php echo $episodes_closed?></span></span></h5>
						<div id="yw0" class="grid-view">
							<?php if (empty($episodes)) {?>
								<div class="summary">No episodes</div>
							<?php }else{?>
								<table class="items">
									<thead>
										<tr><th id="yw0_c0">Start  Date</th><th id="yw0_c1">End  Date</th><th id="yw0_c2">Firm</th><th id="yw0_c3">Specialty</th><th id="yw0_c4">Eye</th><th id="yw0_c5">Diagnosis</th></tr>
									</thead>
									<tbody>
										<?php foreach ($episodes as $i => $episode) {?>
											<tr id="<?php echo $episode->id?>" class="all-episode <?php if ($i %2 == 0){?>even<?php }else{?>odd<?php }?><?php if ($episode->end_date !== null){?> closed<?php }?>">
												<td><?php echo date('d/m/y', strtotime($episode->start_date))?></td>
												<td><?php echo $episode->end_date !== null ? date('d/m/y', strtotime($episode->end_date)) : ''?></td>
												<td><?php echo CHtml::encode($episode->firm->name)?></td>
												<td><?php echo CHtml::encode($episode->firm->serviceSpecialtyAssignment->specialty->name)?></td>
												<td><?php echo $episode->getPrincipalDiagnosis()->getEyeText()?></td>
												<td><?php echo $episode->getPrincipalDiagnosis() ? $episode->getPrincipalDiagnosis()->disorder->term : 'No diagnosis'?></td>
											</tr>
										<?php }?>
									</tbody>
								</table>
								<div class="table_endRow"></div>
							<?php }?>
						</div> <!-- .grid-view -->
					</div>	<!-- .blueBox -->
					<p><a href="/patient/episodes/<?php echo $model->id?>"><span class="aPush">View all Episodes, Summaries and Events</span></a></p>
				</div> <!-- .halfColumn -->
			</div><!-- .wrapTwo -->
			<script type="text/javascript">
				$('tr.all-episode').unbind('click').click(function() {
					window.location.href = '/patient/episodes/<?php echo $model->id?>/episode/'+$(this).attr('id');
					return false;
				});
			</script>
