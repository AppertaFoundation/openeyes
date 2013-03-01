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

$patientName = $this->patient->first_name . ' ' . $this->patient->last_name;
$this->breadcrumbs=array(
	"{$patientName} ({$this->patient->hos_num})",
);

$address_str = '';

$address = $this->patient->address;

if (!empty($address)) {
	$fields = array(
		'address1' => str_replace(',','',$address->address1),
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
				<?php $this->renderPartial('//base/_messages'); ?>
				<?php if($address && !$address->isCurrent()) { // No current address available ?>
					<div id="no-current-address-error" class="alertBox">
						<h3>Warning: The patient has no current address. The address shown is their last known address.</h3>
					</div>
				<?php } ?>
				<?php if ($this->patient->isDeceased()) {?>
					<div id="deceased-notice" class="alertBox">
						This patient is deceased (<?php echo $this->patient->NHSDate('date_of_death'); ?>)
					</div>
				<?php } ?>
				<?php if(!$this->patient->practice || !$this->patient->practice->address) { ?>
				<div id="no-practice-address" class="alertBox">
					Patient has no GP practice address, please correct in PAS before printing GP letter.
				</div>
				<?php } ?>
				
				<div class="halfColumnLeft">
 
					<!-- double re-enforcement of mode change not currently required, but might be needed in the future
					<div class="patientBox withIcon">
						<img class="boxIcon" src="/img/_elements/icons/patient.png" alt="patient" width="36" height="35" />
						<div id="patientHeader"><h3>Mrs Lucinda Smith <span class="normal">(1008504)</span></h3></div>
					</div>
					-->

					<div class="whiteBox patientDetails" id="personal_details">
						<div class="patient_actions">
							<?php /*<span class="aBtn"><a href="#">Edit</a></span>*/?><span class="aBtn"><a class="sprite showhide" href="#"><span class="hide"></span></a></span>
						</div>
						<h4>Personal Details:</h4>
						<div class="data_row">
							<div class="data_label">First name(s):</div>
							<div class="data_value"><?php echo $this->patient->first_name?></div>
						</div>
						<div class="data_row">
							<div class="data_label">Last name:</div>
							<div class="data_value"><?php echo $this->patient->last_name?></div>
						</div>
						<div class="data_row">
							<div class="data_label">Address:</div>
							<div class="data_value"><?php echo $address_str?></div>
						</div>
						<div class="data_row">
							<div class="data_label">Date of Birth:</div>
							<div class="data_value">
								<?php echo $this->patient->NHSDate('dob') ?>
							</div>
						</div>
						<div class="data_row">
							<?php if($this->patient->date_of_death) { ?>
							<div class="data_label">Date of Death:</div>
							<div class="data_value"><?php echo $this->patient->NHSDate('date_of_death') . ' (Age '.$this->patient->getAge().')' ?></div>
							<?php } else { ?>
							<div class="data_label">Age:</div>
							<div class="data_value"><?php echo $this->patient->getAge() ?></div>
							<?php } ?>
						</div>
						<div class="data_row">
							<div class="data_label">Gender:</div>
							<div class="data_value"><?php echo $this->patient->gender == 'F' ? 'Female' : 'Male'?></div>
						</div>
					</div> <!-- #personal_details -->
					<div class="whiteBox patientDetails" id="contact_details">
						<div class="patient_actions">
							<?php /*<span class="aBtn"><a href="#">Edit</a></span>*/?><span class="aBtn"><a class="sprite showhide" href="#"><span class="hide"></span></a></span>
						</div>
						<h4>Contact Details:</h4>
						<div class="data_row">
							<div class="data_label">Telephone:</div>
							<div class="data_value"><?php echo !empty($this->patient->primary_phone) ? $this->patient->primary_phone : 'Unknown'?></div>
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

					<div class="whiteBox patientDetails" id="gp_details">
						<div class="patient_actions">
							<?php /*<span class="aBtn"><a href="#">Edit</a></span>*/?><span class="aBtn"><a class="sprite showhide" href="#"><span class="hide"></span></a></span>
						</div>
						<h4>General Practitioner:</h4>
						<div class="data_row">
							<div class="data_label">Name:</div>
							<div class="data_value"><?php echo ($this->patient->gp) ? $this->patient->gp->contact->fullName : 'Unknown'; ?></div>
						</div>
						<?php if (Yii::app()->user->checkAccess('admin')) { ?>
						<div class="data_row goldenrod">
							<div class="data_label">GP Address:</div>
							<div class="data_value"><?php echo ($this->patient->gp && $this->patient->gp->contact->address) ? $this->patient->gp->contact->address->letterLine : 'Unknown'; ?></div>
						</div>
						<div class="data_row goldenrod">
							<div class="data_label">GP Telephone:</div>
							<div class="data_value"><?php echo ($this->patient->gp && $this->patient->gp->contact->primary_phone) ? $this->patient->gp->contact->primary_phone : 'Unknown'; ?></div>
						</div>
						<?php } ?>
						<div class="data_row">
							<div class="data_label">Practice Address:</div>
							<div class="data_value"><?php echo ($this->patient->practice && $this->patient->practice->address) ? $this->patient->practice->address->letterLine : 'Unknown'; ?></div>
						</div>
						<div class="data_row">
							<div class="data_label">Practice Telephone:</div>
							<div class="data_value"><?php echo ($this->patient->practice && $this->patient->practice->phone) ? $this->patient->practice->phone : 'Unknown'; ?></div>
						</div>
					</div>

					<div class="whiteBox patientDetails" id="contact_details">
						<div class="patient_actions">
							<?php /*<span class="aBtn"><a href="#">Edit</a></span>*/?><span class="aBtn"><a class="sprite showhide" href="#"><span class="hide"></span></a></span>
						</div>
						<h4>Associated contacts:</h4>
						<div class="data_row">
							<table class="subtleWhite smallText">
								<thead>
									<tr>
										<th width="33%">Name</th>
										<th>Location</th>
										<th>Type</th>
										<th colspan="2"></th>
									</tr>
								</thead>
								<tbody id="patient_contacts">	
									<?php foreach ($this->patient->contactAssignments as $pca) {
										if (!in_array($pca->contact->parent_class,array('Specialist','Consultant'))) {
											if ($uca = UserContactAssignment::model()->find('contact_id=?',array($pca->contact_id))) {
												if (!$uca->user) continue;
											}
										}?>
										<tr>
											<td><span class="large"><?php if ($pca->contact->title) echo $pca->contact->title.' '?><?php echo $pca->contact->first_name?> <?php echo $pca->contact->last_name?></span><br /><?php echo $pca->contact->qualifications?></td>
											<td>
												<?php if ($pca->site) {?>
													<?php echo $pca->site->name?>
												<?php } else if ($pca->institution) {?>
													<?php echo $pca->institution->name?>
												<?php } else if ($pca->contact->address) {?>
													<?php echo str_replace(',','',$pca->contact->address->summary)?>
												<?php }?>
											</td>
											<td>
												<?php
												switch ($pca->contact->parent_class) {
													case 'Specialist':
														echo Specialist::model()->findByPk($pca->contact->parent_id)->specialist_type->name;
														break;
													case 'Consultant':
														echo 'Consultant Ophthalmologist';
														break;
													default:
														if ($uca = UserContactAssignment::model()->find('contact_id=?',array($pca->contact_id))) {
															echo $uca->user->role ? $uca->user->role : 'Staff';
														} else {
															echo $pca->contact->parent_class;
														}
												}
												?>
											</td>
											<td colspan="2" align="right"><?php /*<a href="#" class="small"><strong>Edit</strong></a>&nbsp;&nbsp;*/?><a id="removecontact<?php echo $pca->contact->id?>_<?php echo $pca->site_id?>_<?php echo $pca->institution_id?>" href="#" class="small"><strong>Remove</strong></a></td>
										</tr>
									<?php }?>
								</tbody>
							</table>	
						</div>
						<div class="data_tow">
							<span>Add contact:</span>
							<?php
							$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
								'name'=>"contactname",
								'id'=>"contactname",
								'value'=>'',
								'source'=>"js:function(request, response) {

									var filter = $('#contactfilter').val();

									$('img.loader').show();

									$.ajax({
										'url': '" . Yii::app()->createUrl('patient/possiblecontacts') . "',
										'type':'GET',
										'data':{'term': request.term, 'filter': filter},
										'success':function(data) {
											data = $.parseJSON(data);

											var result = [];

											contactCache = {};

											for (var i = 0; i < data.length; i++) {
												var index = $.inArray(data[i]['line'], currentContacts);
												if (index == -1) {
													result.push(data[i]['line']);
													contactCache[data[i]['line']] = data[i];
												}
											}

											response(result);

											$('img.loader').hide();
										}
									});
								}",
								'options'=>array(
									'minLength'=>'3',
									'select'=>"js:function(event, ui) {
										var value = ui.item.value;

										$('#contactname').val('');

										var querystr = 'patient_id=".$this->patient->id."&contact_id='+contactCache[value]['contact_id'];

										if (contactCache[value]['site_id']) {
											querystr += '&site_id='+contactCache[value]['site_id'];
										}

										if (contactCache[value]['institution_id']) {
											querystr += '&institution_id='+contactCache[value]['institution_id'];
										}

										$.ajax({
											'type': 'GET',
											'dataType': 'json',
											'url': '".Yii::app()->createUrl('patient/associatecontact')."?'+querystr,
											'success': function(data) {
												if (data[\"name\"]) {
													$('#patient_contacts').append('<tr><td><span class=\"large\">'+data[\"name\"]+'</span><br />'+data[\"qualifications\"]+'</td><td>'+data[\"location\"]+'</td><td>'+data[\"type\"]+'<td colspan=\"2\" align=\"right\"><a id=\"removecontact'+data[\"id\"]+'_'+data[\"site_id\"]+'_'+data[\"institution_id\"]+'\" href=\"#\" class=\"small\"><strong>Remove</strong></a></td></tr>');

													if (data[\"location\"].length >0) {
														currentContacts.push(data[\"name\"]+' ('+data[\"type\"]+', '+data[\"location\"]+')');
													} else {
														currentContacts.push(data[\"name\"]+' ('+data[\"type\"]+')');
													}
												}
											}
										});

										return false;
									}",
								),
								'htmlOptions'=>array(
									'placeholder' => 'search for contacts'
								),
							));
							?>
							&nbsp;
							&nbsp;&nbsp;
							<select id="contactfilter" name="contactfilter">
								<option value="">- Filter -</option>
								<option value="moorfields" selected="selected">Moorfields staff</option>
								<option value="consultant">Consultant ophthalmologist</option>
								<option value="specialist">Non-ophthalmic specialist</option>
							</select>
							&nbsp;
							<img src="<?php echo Yii::app()->createUrl('img/ajax-loader.gif')?>" class="loader" alt="loading..." style="display: none;" />
						</div>
					</div>
				</div>

				<div class="halfColumnRight">
					<div class="blueBox">
						<h5>All Episodes<span style="float:right;">&nbsp; open <?php echo $episodes_open?> &nbsp;|&nbsp;<span style="font-weight:normal;">closed <?php echo $episodes_closed?></span></span></h5>
						<div id="yw0" class="grid-view">
							<?php if (empty($episodes)) {?>
								<div class="summary">No episodes</div>
							<?php }else{?>
								<table class="items">
									<thead>
										<tr><th id="yw0_c0">Start  Date</th><th id="yw0_c1">End  Date</th><th id="yw0_c2">Firm</th><th id="yw0_c3">Subspecialty</th><th id="yw0_c4">Eye</th><th id="yw0_c5">Diagnosis</th></tr>
									</thead>
									<tbody>
										<?php foreach ($ordered_episodes as $specialty_episodes) {?>
											<tr>
											<td colspan="6" class="all-episode"><b><?php echo $specialty_episodes['specialty']->name ?></b></td>
											</tr>
											<?php foreach ($specialty_episodes['episodes'] as $i => $episode) {?>
												<tr id="<?php echo $episode->id?>" class="clickable all-episode <?php if ($i %2 == 0){?>even<?php }else{?>odd<?php }?><?php if ($episode->end_date !== null){?> closed<?php }?>">
													<td><?php echo $episode->NHSDate('start_date'); ?></td>
													<td><?php echo $episode->NHSDate('end_date'); ?></td>
													<td><?php echo CHtml::encode($episode->firm->name)?></td>
													<td><?php echo CHtml::encode($episode->firm->serviceSubspecialtyAssignment->subspecialty->name)?></td>
													<td><?php echo ($episode->diagnosis) ? $episode->eye->name : 'No diagnosis' ?></td>
													<td><?php echo ($episode->diagnosis) ? $episode->diagnosis->term : 'No diagnosis' ?></td>
												</tr>
											<?php }?>
										<?php }?>
									</tbody>
								 
								</table>
								<div class="table_endRow"></div>
							<?php }?>
						</div> <!-- .grid-view -->
					</div>	<!-- .blueBox -->
					<p><?php echo CHtml::link('<span class="aPush">Create or View Episodes and Events</span>',Yii::app()->createUrl('patient/episodes/'.$this->patient->id))?></p>
					<?php $this->renderPartial('_ophthalmic_diagnoses')?>
					<?php $this->renderPartial('_systemic_diagnoses')?>
					<?php $this->renderPartial('_allergies'); ?>
				</div> <!-- .halfColumn -->
			</div><!-- .wrapTwo -->
			<script type="text/javascript">
				$('tr.all-episode').unbind('click').click(function() {
					window.location.href = '<?php echo Yii::app()->createUrl('patient/episode')?>/'+$(this).attr('id');
					return false;
				});
				$(this).undelegate('a[id^="removecontact"]','click').delegate('a[id^="removecontact"]','click',function() {
					var e = $(this).attr('id').replace(/^removecontact/,'').split('_');

					var id = e[0];
					var site_id = e[1];
					var institution_id = e[2];

					var el = $(this);

					if ($(this).parent().parent().children('td:nth-child(2)').length >0) {
						var name = $(this).parent().parent().children('td:first').children('span').html()+' ('+$.trim($(this).parent().parent().children('td:nth-child(3)').html())+', '+$.trim($(this).parent().parent().children('td:nth-child(2)').html())+')';
					} else {
						var name = $.trim($(this).parent().parent().children('td:first').children('span').html())+' ('+$.trim($(this).parent().parent().children('td:nth-child(3)').html())+')';
					}

					$.ajax({
						'type': 'GET',
						'url': '<?php echo Yii::app()->createUrl('patient/unassociatecontact')?>?patient_id=<?php echo $this->patient->id?>&contact_id='+id+'&site_id='+site_id+'&institution_id='+institution_id,
						'success': function(resp) {
							if (resp == "1") {
								el.parent().parent().remove();

								var newCurrentContacts = [];
								for (var i in currentContacts) {
									if (currentContacts[i] != name) {
										newCurrentContacts.push(currentContacts[i]);
									}
								}

								currentContacts = newCurrentContacts;

								$('#contactname').focus();

							} else {
								alert("Sorry, something went wrong. Please try again or contact support for assistance.");
							}
						}
					});

					return false;
				});

				$('#contactfilter').change(function() {
					$('#contactname').focus();
				});

				var currentContacts = [];

				<?php if ($this->patient->gp || ($this->patient->practice && $this->patient->practice->address)) {
					$gp_dropdown_string = (($this->patient->gp && $this->patient->gp->contact->fullName) ? $this->patient->gp->contact->fullName : 'Unknown') . '(Gp';
					$gp_dropdown_string .= (($this->patient->practice && $this->patient->practice->address) ? ', ' . $this->patient->practice->address->summary : '') . ')';
				?>
					currentContacts.push("<?php echo $gp_dropdown_string ?>");
				<?php } ?>

				<?php foreach ($this->patient->contactAssignments as $pca) {?>
					<?php if ($pca->site) {
						switch ($pca->contact->parent_class) {
							case 'Specialist':
								$type = Specialist::model()->findByPk($pca->contact->parent_id)->specialist_type->name;
								break;
							case 'Consultant':
								$type = 'Consultant Ophthalmologist';
								break;
							default:
								$type = $pca->contact->parent_class;
								break;
						}
						?>
						currentContacts.push("<?php if ($pca->contact->title) echo $pca->contact->title.' '; echo $pca->contact->first_name.' '.$pca->contact->last_name.' ('.$type.', '.$pca->site->name.')';?>");
					<?php } else if ($pca->institution) {
						if (($uca = UserContactAssignment::model()->find('contact_id=?',array($pca->contact_id))) && $uca->user) {?>
						currentContacts.push("<?php if ($pca->contact->title) echo $pca->contact->title.' '; echo $pca->contact->first_name.' '.$pca->contact->last_name.' ('.($uca ? ($uca->user->role ? $uca->user->role : 'Staff') : $pca->contact->parent_class).', '.$pca->institution->name.')';?>");
						<?php }?>
					<?php } else {?>
						currentContacts.push("<?php if ($pca->contact->title) echo $pca->contact->title.' '; echo $pca->contact->first_name.' '.$pca->contact->last_name.' ('.$pca->contact->parent_class.($pca->contact->address ? ', '.$pca->contact->address->summary : '').')';?>");
					<?php }?>
				<?php }?>

				var contactCache = {};
			</script>
			<?php
			function filter_nulls($data) {
				return $data !== null;
			}
