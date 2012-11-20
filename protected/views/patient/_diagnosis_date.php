<?php 
	if (!@$diagnosis_date) 
	{
		// default to today
		$diagnosis_date = date('Y-m-d');
	}
	$_year = (integer)substr($diagnosis_date,0,4);
	$_month = (integer)substr($diagnosis_date,5,2);
	$_day = (integer)substr($diagnosis_date,8,2);
	?>
							<div class="diagnosis_date">
								<span class="diagnosis_date_label">
									Date:
								</span>
								<select name="diagnosis_day" class="diagnosis_date_field">
									<option value="">Day (optional)</option>
									<?php for ($i=1;$i<=31;$i++) {?>
										<option value="<?php echo $i?>"<?php if ($_day == $i) {?> selected="selected"<?php }?>><?php echo $i?></option>
									<?php }?>
								</select>
								<select name="diagnosis_month" class="diagnosis_date_field">
									<option value="">Month (optional)</option>
									<?php foreach (array('January','February','March','April','May','June','July','August','September','October','November','December') as $i => $month) {?>
										<option value="<?php echo $i+1?>"<?php if ($_month == $i+1) {?> selected="selected"<?php }?>><?php echo $month?></option>
									<?php }?>
								</select>
								<select name="diagnosis_year" class="diagnosis_date_field">
									<?php for ($i=date('Y')-50;$i<=date('Y');$i++) {?>
										<option value="<?php echo $i?>"<?php if ($_year == $i) {?> selected="selected"<?php }?>><?php echo $i?></option>
									<?php }?>
								</select>
							</div>
