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

$based_on = '';

if (@$_GET['first_name'] && $_GET['first_name'] != '0') {
	$based_on = 'FIRST NAME: <strong>"'.$_GET['first_name'].'"</strong>';
}
if (@$_GET['last_name'] && $_GET['last_name'] != '0') {
	if (@$based_on) {
		$based_on .= ', ';
	}
	$based_on .= 'LAST NAME: <strong>"'.$_GET['last_name'].'"</strong>';
}
if (@$_GET['hos_num'] && $_GET['hos_num'] != '0') {
	if (@$based_on) {
		$based_on .= ', ';
	}
	$based_on .= 'HOSPITAL NUMBER: <strong>'.$_GET['hos_num']."</strong>";
}
?>
			<h2>Search Results</h2>
			<div class="wrapTwo clearfix">
				<div class="wideColumn">
					<p><strong><?php echo $total_items?> patients found</strong>, based on <?php echo $based_on?></p>
					
					<div class="whiteBox">
						<?php
						$from = 1+($pagen*$items_per_page);
						$to = ($pagen+1)*$items_per_page;
						if ($to > $total_items) {
							$to = $total_items;
						}
						?>
						<h3>Results. You are viewing patients <?php echo $from?> to <?php echo $to?>, of <?php echo $total_items?></h3>

						<?php $this->widget('zii.widgets.grid.CGridView', array(
							'id'=>'patient-grid',
							'dataProvider'=>$dataProvider,
							'template'=>"{items}\n{pager}",
							'columns'=>array(
								'pas_key',
								'title',
								'first_name',
								'last_name',
								'dob',
								'gender',
								'hos_num',
								'nhs_num'
							)
						));
						?>

						<div class="resultsPagination">Viewing patients:
							<?php for ($i=0; $i<$pages; $i++) {?>
								<?php if ($i == $pagen) {
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
									<span class="otherPages"><a href="/patient/results/<?php echo $hos_num?>/<?php echo $first_name?>/<?php echo $last_name?>/<?php echo $nhs_num?>/<?php echo $gender?>/<?php echo $dob_day?>/<?php echo $dob_month?>/<?php echo $dob_year?>/<?php echo $i+1?>"><?php echo 1+($i*$items_per_page)?> - <?php echo $to?></a></span>
								<?php }?>
							<?php }?>
						</div>
					
					</div> <!-- .whiteBox -->
				
				</div>	<!-- .wideColumn -->
				
				<div class="narrowColumn">
					<form id="refine-patient-search" action="/patient/results" method="post">
						<input type="hidden" name="Patient[hos_num]" value="<?php echo $_GET['hos_num']?>" />
						<input type="hidden" name="Patient[first_name]" value="<?php echo $_GET['first_name']?>" />
						<input type="hidden" name="Patient[last_name]" value="<?php echo $_GET['last_name']?>" />
						<input type="hidden" name="dob_day" value="<?php echo $_GET['dob_day']?>" />
						<input type="hidden" name="dob_month" value="<?php echo $_GET['dob_month']?>" />
						<input type="hidden" name="dob_year" value="<?php echo $_GET['dob_year']?>" />

						<div id="refine_patient_details" class="form_greyBox clearfix">
							<h3>Refine your search</h3>
							<h4>Add, or modify, the details below to help you find the patient you are looking for.</h4>
							
							<!--<div class="multiInputRight clearfix">
								<label for="dob">Age range:<span class="labelHint">e.g. 20 to 40</span></label>
								<input size="2" maxlength="2" type="text" value="00" name="dob_day" id="dob_day" /><strong style="margin:0 5px 0 8px;"> to </strong>
								<input size="2" maxlength="2" type="text" value="99" name="dob_month" id="dob_month" />
							</div>
							-->
						
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
								<button type="submit" value="submit" class="btn_refineSearch ir" id="refinePatient_details">Find patient</button>	
							</div>
							
						</div>
				</form>
				
				<p><a href="/">Clear this search and <span class="aPush">start a new search</span></a></p>
				
				</div> <!-- .narrowColumn -->
			</div><!-- .wrapTwo -->
			<script type="text/javascript">
				$('table.items tr').click(function() {
					window.location.href = '/patient/viewpas/'+$(this).children(":first").html();
					return false;
				});
			</script>
