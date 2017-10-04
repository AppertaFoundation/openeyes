<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<section class="box patient-info js-toggle-container">
	<h3 class="box-title">General Practitioner:</h3>
	<a href="#" class="toggle-trigger toggle-hide js-toggle">
		<span class="icon-showhide">
			Show/hide this section
		</span>
	</a>
	<div class="js-toggle-body">
		<div class="row data-row">
			<div class="large-4 column">
				<div class="data-label">Name:</div>
			</div>
			<div class="large-8 column">
				<div class="data-value"><?php echo ($this->patient->gp) ? $this->patient->gp->contact->fullName : 'Unknown'; ?></div>
			</div>
		</div>
		<?php if (Yii::app()->user->checkAccess('admin')) { ?>
		<div class="row data-row highlight">
			<div class="large-4 column">
				<div class="data-label">GP Address:</div>
			</div>
			<div class="large-8 column">
				<div class="data-value"><?php echo ($this->patient->gp && $this->patient->gp->contact->address) ? $this->patient->gp->contact->address->letterLine : 'Unknown'; ?></div>
			</div>
		</div>
		<div class="row data-row highlight">
			<div class="large-4 column">
				<div class="data-label">GP Telephone:</div>
			</div>
			<div class="large-8 column">
				<div class="data-value"><?php echo ($this->patient->gp && $this->patient->gp->contact->primary_phone) ? $this->patient->gp->contact->primary_phone : 'Unknown'; ?></div>
			</div>
		</div>
		<?php } ?>
		<div class="row data-row">
			<div class="large-4 column">
				<div class="data-label">Practice Address:</div>
			</div>
			<div class="large-8 column">
				<div class="data-value"><?php echo ($this->patient->practice && $this->patient->practice->contact->address) ? $this->patient->practice->contact->address->letterLine : 'Unknown'; ?></div>
			</div>
		</div>
		<div class="row data-row">
			<div class="large-4 column">
				<div class="data-label">Practice Telephone:</div>
			</div>
			<div class="large-8 column">
				<div class="data-value"><?php echo ($this->patient->practice && $this->patient->practice->phone) ? $this->patient->practice->phone : 'Unknown'; ?></div>
			</div>
		</div>
	</div>
</section>
