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
<div class="whiteBox patientDetails" id="personal_details">
	<div class="patient_actions">
		<span class="aBtn"><a class="sprite showhide" href="#"><span class="hide"></span></a></span>
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
		<div class="data_value"><?php echo $this->patient->getSummaryAddress()?></div>
	</div>
	<div class="data_row">
		<div class="data_label">Date of Birth:</div>
		<div class="data_value">
			<?php echo ($this->patient->dob) ? $this->patient->NHSDate('dob') : 'Unknown' ?>
		</div>
	</div>
	<div class="data_row">
		<?php if ($this->patient->date_of_death) { ?>
		<div class="data_label">Date of Death:</div>
		<div class="data_value"><?php echo $this->patient->NHSDate('date_of_death') . ' (Age '.$this->patient->getAge().')' ?></div>
		<?php } else {?>
		<div class="data_label">Age:</div>
		<div class="data_value"><?php echo $this->patient->getAge()?></div>
		<?php }?>
	</div>
	<div class="data_row">
		<div class="data_label">Gender:</div>
		<div class="data_value"><?php echo $this->patient->getGenderString() ?></div>
	</div>
	<div class="data_row">
		<div class="data_label">Ethnic Group:</div>
		<div class="data_value"><?php echo $this->patient->getEthnicGroupString() ?></div>
	</div>
</div>
