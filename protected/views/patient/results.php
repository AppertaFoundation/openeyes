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

$based_on = array();
if (@$_GET['last_name'] && $_GET['last_name'] != '0') {
	$based_on[] = 'LAST NAME: <strong>"'.$_GET['last_name'].'"</strong>';
}
if (@$_GET['first_name'] && $_GET['first_name'] != '0') {
	$based_on[] = 'FIRST NAME: <strong>"'.$_GET['first_name'].'"</strong>';
}
if (@$_GET['hos_num'] && $_GET['hos_num'] != '0') {
	$based_on[] = 'HOSPITAL NUMBER: <strong>'.$_GET['hos_num']."</strong>";
}
$based_on = implode(', ', $based_on);

?>
			<h2>Search Results</h2>
			<div class="wrapTwo clearfix">
				<div class="wideColumn">
					<p><strong><?php echo $total_items?> patients found</strong>, based on <?php echo $based_on?></p>

					<?php if (Yii::app()->params['pas_down']) {?>
						<div id="pas-error" class="alertBox">
							Warning: the PAS is currently down. Search results are likely to be stale.
						</div>
					<?php }?>

					<div class="whiteBox">
						<?php
						$from = 1+(($pagen-1)*$items_per_page);
						$to = $pagen*$items_per_page;
						if ($to > $total_items) {
							$to = $total_items;
						}
						?>
						<h3>Results. You are viewing patients <?php echo $from?> to <?php echo $to?>, of <?php echo $total_items?></h3>

						<div id="patient-grid" class="grid-view">
							<table class="items">
								<thead>
									<tr>
										<th id="patient-grid_c0"><a href="/patient/results/<?php echo $first_name?>/<?php echo $last_name?>/<?php echo $nhs_num?>/<?php echo $gender?>/0/<?php if ($sort_dir == 0) {?>1<?php }else{?>0<?php }?>/<?php echo $pagen?>">Hospital Number</a></th>
										<th id="patient-grid_c1"><a href="/patient/results/<?php echo $first_name?>/<?php echo $last_name?>/<?php echo $nhs_num?>/<?php echo $gender?>/1/<?php if ($sort_dir == 0) {?>1<?php }else{?>0<?php }?>/<?php echo $pagen?>">Title</a></th>
										<th id="patient-grid_c2"><a href="/patient/results/<?php echo $first_name?>/<?php echo $last_name?>/<?php echo $nhs_num?>/<?php echo $gender?>/2/<?php if ($sort_dir == 0) {?>1<?php }else{?>0<?php }?>/<?php echo $pagen?>">First name</a></th>
										<th id="patient-grid_c3"><a href="/patient/results/<?php echo $first_name?>/<?php echo $last_name?>/<?php echo $nhs_num?>/<?php echo $gender?>/3/<?php if ($sort_dir == 0) {?>1<?php }else{?>0<?php }?>/<?php echo $pagen?>">Last name</a></th>
										<th id="patient-grid_c4"><a href="/patient/results/<?php echo $first_name?>/<?php echo $last_name?>/<?php echo $nhs_num?>/<?php echo $gender?>/4/<?php if ($sort_dir == 0) {?>1<?php }else{?>0<?php }?>/<?php echo $pagen?>">Date of birth</a></th>
										<th id="patient-grid_c5"><a href="/patient/results/<?php echo $first_name?>/<?php echo $last_name?>/<?php echo $nhs_num?>/<?php echo $gender?>/5/<?php if ($sort_dir == 0) {?>1<?php }else{?>0<?php }?>/<?php echo $pagen?>">Gender</a></th>
										<th id="patient-grid_c5"><a href="/patient/results/<?php echo $first_name?>/<?php echo $last_name?>/<?php echo $nhs_num?>/<?php echo $gender?>/6/<?php if ($sort_dir == 0) {?>1<?php }else{?>0<?php }?>/<?php echo $pagen?>">NHS number</a></th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($dataProvider->getData() as $i => $result) {?>
										<tr class="<?php if ($i%2 == 0) {?>even<?php }else{?>odd<?php }?>">
											<td><?php echo $result->hos_num?></td>
											<td><?php echo $result->title?></td>
											<td><?php echo $result->first_name?></td>
											<td><?php echo $result->last_name?></td>
											<td><?php echo $result->dob?></td>
											<td><?php echo $result->gender?></td>
											<td><?php echo $result->nhs_num?></td>
										</tr>
									<?php }?>
								</tbody>
							</table>
						</div>

						<div class="resultsPagination">Viewing patients:
							<?php for ($i=0; $i<$pages; $i++) {?>
								<?php if ($i == $pagen-1) {
									$to = ($i+1)*$items_per_page;
									if ($to > $total_items) {
										$to = $total_items;
									}
									?>
									<span class="showingPage"><?php echo 1+($i*$items_per_page)?> - <?php echo $to?></span>
								<?php }else{?>
									<?php
									$to = ($i+1)*$items_per_page;
									if ($to > $total_items) {
										$to = $total_items;
									}
									?>
									<span class="otherPages"><a href="/patient/results/<?php echo $first_name?>/<?php echo $last_name?>/<?php echo $nhs_num?>/<?php echo $gender?>/<?php echo $sort_by?>/<?php echo $sort_dir?>/<?php echo $i+1?>"><?php echo 1+($i*$items_per_page)?> - <?php echo $to?></a></span>
								<?php }?>
							<?php }?>
						</div>
					
					</div> <!-- .whiteBox -->
				
				</div>	<!-- .wideColumn -->
				
				<div class="narrowColumn">
					<!--
					<form id="refine-patient-search" action="/patient/results" method="post">
						<input type="hidden" name="Patient[first_name]" value="<?php echo $_GET['first_name']?>" />
						<input type="hidden" name="Patient[last_name]" value="<?php echo $_GET['last_name']?>" />

						<div id="refine_patient_details" class="form_greyBox clearfix">
							<h3>Refine your search</h3>
							<h4>Add, or modify, the details below to help you find the patient you are looking for.</h4>
							
							<div class="multiInputRight clearfix">
								<label for="dob">Age range:<span class="labelHint">e.g. 20 to 40</span></label>
								<input size="2" maxlength="2" type="text" value="00" name="dob_day" id="dob_day" /><strong style="margin:0 5px 0 8px;"> to </strong>
								<input size="2" maxlength="2" type="text" value="99" name="dob_month" id="dob_month" />
							</div>
							<div class="inputLayout clearfix">	
								<label for="nhs_number">NHS #:<span class="labelHint">for example: #111-222-3333</span></label>
								<input type="text" value="<?php if (@$_GET['nhs_num']!='0') echo @$_GET['nhs_num']?>" name="Patient[nhs_num]" id="Patient_nhs_num" />
							</div>
							<div class="customRight clearfix">
								<label style="float:left;"for="gender">Gender:<span class="labelHint">if known</span></label>
								<input	value="M" id="Patient_gender_0" type="radio" name="Patient[gender]"<?php if (@$_GET['gender'] == 'M') echo ' checked="checked"'?> /> 
								<label style="padding-right:10px;" for="Patient_gender_0">Male</label>
								<input value="F" id="Patient_gender_1" type="radio" name="Patient[gender]"<?php if (@$_GET['gender'] == 'F') echo ' checked="checked"'?> /> 
								<label for="Patient_gender_1">Female</label>
							</div>
						
							<div class="form_button">
								<img class="loader" src="/img/ajax-loader.gif" alt="loading..." style="display: none; margin-right: 10px;" />
								<button type="submit" class="classy blue tall" id="refinePatient_details"><span class="button-span button-span-blue">Find patient</span></button>
							</div>
							
						</div>
					</form>
					-->
				
					<p><a href="/">Clear this search and <span class="aPush">start a new search</span></a></p>
				
				</div> <!-- .narrowColumn -->
			</div><!-- .wrapTwo -->
			<script type="text/javascript">
				$('#patient-grid .items tr td').click(function() {
					window.location.href = '/patient/viewhosnum/'+$(this).parent().children(":first").html();
					return false;
				});
				$('#refinePatient_details').click(function() {
					if ($(this).hasClass('blue')) {
						disableButtons();
						return true;
					}
					return false;
				});
			</script>
