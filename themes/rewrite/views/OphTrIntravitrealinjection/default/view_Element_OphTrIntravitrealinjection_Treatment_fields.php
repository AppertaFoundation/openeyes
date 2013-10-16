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

<div class="eventDetail aligned">
	<div class="label"><?php echo $element->getAttributeLabel($side . '_pre_antisept_drug_id') ?>:</div>
	<div class="data"><?php echo $element->{$side . '_pre_antisept_drug'}->name ?></div>
</div>

<div class="eventDetail aligned">
	<div class="label"><?php echo $element->getAttributeLabel($side . '_pre_skin_drug_id') ?>:</div>
	<div class="data"><?php echo $element->{$side . '_pre_skin_drug'}->name ?></div>
</div>

<div class="eventDetail aligned">
	<div class="label"><?php echo $element->getAttributeLabel($side . '_pre_ioplowering_required') ?>:</div>
	<div class="data"><?php echo $element->{$side . '_pre_ioplowering_required'} ? 'Yes' : 'No' ?></div>
</div>

<?php if ($element->{$side . '_pre_ioploweringdrugs'}) { ?>
	<div class="eventDetail aligned">
		<div class="label"><?php echo $element->getAttributeLabel($side . '_pre_ioploweringdrugs') ?>:</div>
		<div class="data" style="display: inline-block;">
			<?php 
			foreach ($element->{$side . '_pre_ioploweringdrugs'} as $item) {
				echo $item->name . "<br />";
			}
			?>
		</div>
	</div>
<?php } ?>

<div class="eventDetail aligned">
	<div class="label"><?php echo $element->getAttributeLabel($side . '_drug_id') ?>:</div>
	<div class="data"><?php echo $element->{$side . '_drug'}->name ?></div>
</div>

<div class="eventDetail aligned">
	<div class="label"><?php echo $element->getAttributeLabel($side . '_number') ?>:</div>
	<div class="data"><?php echo $element->{$side . '_number'} ?></div>
</div>

<div class="eventDetail aligned">
	<div class="label"><?php echo $element->getAttributeLabel($side . '_batch_number') ?>:</div>
	<div class="data"><?php echo $element->{$side . '_batch_number'} ?></div>
</div>

<div class="eventDetail aligned">
	<div class="label"><?php echo $element->getAttributeLabel($side . '_batch_expiry_date') ?>:</div>
	<div class="data"><?php echo $element->NHSDate($side . '_batch_expiry_date') ?></div>
</div>

<div class="eventDetail aligned">
	<div class="label"><?php echo $element->getAttributeLabel($side . '_injection_given_by_id') ?>:</div>
	<div class="data"><?php echo $element->{$side . '_injection_given_by'}->ReversedFullName ?></div>
</div>

<div class="eventDetail aligned">
	<div class="label"><?php echo $element->getAttributeLabel($side . '_injection_time') ?>:</div>
	<div class="data"><?php echo date('g:ia',strtotime($element->{$side . '_injection_time'})); ?></div>
</div>

<div class="eventDetail aligned">
	<div class="label"><?php echo $element->getAttributeLabel($side . '_post_ioplowering_required') ?>:</div>
	<div class="data"><?php echo $element->{$side . '_post_ioplowering_required'} ? 'Yes' : 'No' ?></div>
</div>

<?php if ($element->{$side . '_post_ioploweringdrugs'}) { ?>
	<div class="eventDetail aligned">
		<div class="label"><?php echo $element->getAttributeLabel($side . '_post_ioploweringdrugs') ?>:</div>
		<div class="data" style="display: inline-block;">
			<?php 
			foreach ($element->{$side . '_post_ioploweringdrugs'} as $item) {
				echo $item->name . "<br />";
			}
			?>
		</div>
	</div>

<?php } ?>
