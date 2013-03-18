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
<div id="patientID">
	<div class="patientReminder clearfix">
		<div class="patientName">
			<?php echo CHtml::link($this->patient->getDisplayName(),array('/patient/view/'.$this->patient->id)) ?>
			(<?php if($this->patient->isDeceased()) { ?>Deceased<?php } else { echo $this->patient->getAge(); } ?>)
		</div>
		<div class="hospitalNumber">No. <?php echo $this->patient->hos_num?></div>
		<div class="nhsNumber"><span class="hide">NHS number:</span><?php echo $this->patient->nhsnum?></div>
		<ul class="icons">
			<li class="gender <?php echo strtolower($this->patient->getGenderString()) ?>"><?php echo $this->patient->getGenderString() ?></li>
		</ul>
		<div class="i_patient">
			<?php echo CHtml::link('Patient Summary',array('/patient/view/'.$this->patient->id)); ?>
		</div>
	</div>
</div><!-- #patientID -->
