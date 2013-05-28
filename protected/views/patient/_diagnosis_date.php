<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>
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
