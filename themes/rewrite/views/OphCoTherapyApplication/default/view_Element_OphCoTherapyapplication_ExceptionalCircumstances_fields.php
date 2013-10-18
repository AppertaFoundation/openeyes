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
	<div class="label"><?php echo $element->getAttributeLabel($side . '_standard_intervention_exists') ?>:</div>
	<div class="data"><?php echo $element->{$side . '_standard_intervention_exists'} ? 'Yes' : 'No'?></div>
</div>

<?php if ($element->{$side . '_standard_intervention_exists'}) { ?>
	<div class="eventDetail aligned">
		<div class="label"><?php echo $element->getAttributeLabel($side . '_standard_intervention_id') ?>:</div>
		<div class="data"><?php echo $element->{$side . '_standard_intervention'}->name ?></div>
	</div>

	<div class="eventDetail aligned">
		<div class="label"><?php echo $element->getAttributeLabel($side . '_standard_previous') ?>:</div>
		<div class="data"><?php echo $element->{$side . '_standard_previous'} ? 'Yes' : 'No' ?></div>
	</div>
	<div class="eventDetail aligned">
		<div class="label"><?php echo $element->getAttributeLabel($side . '_intervention_id') ?>:</div>
		<div class="data"><?php echo $element->{$side . '_intervention'}->name ?></div>
	</div>
	<div class="eventDetail aligned">
		<div class="label"><?php echo $element->getAttributeLabel($side . '_description') ?>:</div>
		<div class="data"><?php echo $element->{$side . '_description'} ?></div>
	</div>
	<?php if ($element->needDeviationReasonForSide($side)) { ?>
		<div class="eventDetail aligned">
			<div class="label"><?php echo $element->getAttributeLabel($side . '_deviationreasons') ?>:</div>
			<div class="data">
				<ul>
					<?php foreach ($element->{$side . '_deviationreasons'} as $dr) {
						echo "<li>" . $dr->name . "</li>";
					}?>
				</ul>
			</div>
		</div>
	<?php }?>
<?php } else { ?>
	<div class="eventDetail aligned">
		<div class="label"><?php echo $element->getAttributeLabel($side . '_condition_rare') ?>:</div>
		<div class="data"><?php echo $element->{$side . '_condition_rare'} ? 'Yes' : 'No' ?></div>
	</div>
	<div class="eventDetail aligned">
		<div class="label"><?php echo $element->getAttributeLabel($side . '_incidence') ?>:</div>
		<div class="data"><?php echo $element->{$side . '_incidence'} ?></div>
	</div>
<?php }?>

<div class="eventDetail aligned">
	<div class="label"><?php echo $element->getAttributeLabel($side . '_patient_different') ?>:</div>
	<div class="data"><?php echo $element->{$side . '_patient_different'} ?></div>
</div>
<div class="eventDetail aligned">
	<div class="label"><?php echo $element->getAttributeLabel($side . '_patient_gain') ?>:</div>
	<div class="data"><?php echo $element->{$side . '_patient_gain'} ?></div>
</div>

<?php if ($element->{$side . '_previnterventions'}) { ?>
	<div class="eventDetail aligned">
		<div class="label"><?php echo $element->getAttributeLabel($side . '_previnterventions') ?>:</div>
		<div class="data">
		<?php
			foreach ($element->{$side . '_previnterventions'} as $previntervention) {
				$this->renderPartial('view_OphCoTherapyapplication_ExceptionalCircumstances_PastIntervention', array(
					'pastintervention' => $previntervention,
				));
			}
		?>
		</div>
	</div>
<?php } ?>

<?php if ($element->{$side . '_relevantinterventions'}) { ?>
	<div class="eventDetail aligned">
		<div class="label"><?php echo $element->getAttributeLabel($side . '_relevantinterventions') ?>:</div>
		<div class="data">
			<?php
			foreach ($element->{$side . '_relevantinterventions'} as $relevantintervention) {
				$this->renderPartial('view_OphCoTherapyapplication_ExceptionalCircumstances_PastIntervention', array(
						'pastintervention' => $relevantintervention,
					));
			}
			?>
		</div>
	</div>
<?php } ?>

<div class="eventDetail aligned">
	<div class="label"><?php echo $element->getAttributeLabel($side . '_patient_factors') ?>:</div>
	<div class="data"><?php echo $element->{$side . '_patient_factors'} ? 'Yes' : 'No'?></div>
</div>
<?php if ($element->{$side . '_patient_factors'}) { ?>
	<div class="eventDetail aligned">
		<div class="label"><?php echo $element->getAttributeLabel($side . '_patient_factor_details') ?>:</div>
		<div class="data"><?php echo $element->{$side . '_patient_factor_details'} ?></div>
	</div>
<?php } ?>

<div class="eventDetail aligned">
	<div class="label"><?php echo $element->getAttributeLabel($side . '_patient_expectations') ?>:</div>
	<div class="data"><?php echo $element->{$side . '_patient_expectations'} ?></div>
</div>

<div class="eventDetail aligned">
	<div class="label"><?php echo $element->getAttributeLabel($side . '_start_period_id') ?>:</div>
	<div class="data"><?php echo $element->{$side . '_start_period'}->name ?></div>
</div>

<?php if ($element->{$side . "_start_period"}->urgent) { ?>
	<div class="eventDetail aligned">
		<div class="label"><?php echo $element->getAttributeLabel($side . '_urgency_reason') ?>:</div>
		<div class="data"><?php echo $element->{$side . '_urgency_reason'} ?></div>
	</div>
<?php } ?>

<?php if ($element->{$side . '_filecollections'} && !$element->isSubmitted()) { ?>
<div class="eventDetail aligned">
		<div class="label"><?php echo $element->getAttributeLabel($side . '_filecollections') ?>:</div>
		<div class="data"><ul style="display: inline-block">
		<?php foreach ($element->{$side . '_filecollections'} as $filecoll) { ?>
		<li><a href="<?php echo $filecoll->getDownloadURL() ?>"><?php echo $filecoll->name ?></a></li>
		<?php } ?>
		</ul></div>
	</div>
<?php } ?>
