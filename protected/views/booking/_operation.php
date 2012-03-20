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
<div class="view">
	<strong>Eye(s) to be operated on:</strong>
	<?php echo CHtml::encode($operation->eye->name)?>
</div>
<div class="view">
	<strong>Procedure(s) entered:</strong>
<?php
	$procedures = '';
	if (!empty($operation->procedures)) {
		foreach ($operation->procedures as $procedure) {
			$procedures .= $procedure->term;
			$procedures .= ', ';
		}
		$procedures = substr($procedures, 0, strlen($procedures) - 2);
	}
	echo $procedures; ?>
</div>
<div class="view">
	<strong>Consultant required?</strong>
	<?php echo CHtml::encode($operation->getBooleanText('consultant_required')); ?>
</div>
<div class="view">
	<strong>Anaesthetic required:</strong>
	<?php echo CHtml::encode($operation->anaesthetic_type->name)?>
</div>
<div class="view">
	<strong>Overnight stay required?</strong>
	<?php echo CHtml::encode($operation->getBooleanText('overnight_stay')); ?>
</div>
<div class="view">
	<strong>Comments:</strong>
	<?php echo CHtml::encode($operation->comments); ?>
</div>
