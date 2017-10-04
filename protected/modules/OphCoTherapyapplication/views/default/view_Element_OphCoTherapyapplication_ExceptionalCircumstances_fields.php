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

<div class="row data-row">
	<div class="large-4 column">
		<div class="data-label"><?php echo $element->getAttributeLabel($side.'_standard_intervention_exists') ?>:</div>
	</div>
	<div class="large-8 column">
		<div class="data-value"><?php echo $element->{$side.'_standard_intervention_exists'} ? 'Yes' : 'No'?></div>
	</div>
</div>

<?php if ($element->{$side.'_standard_intervention_exists'}) { ?>
	<div class="row data-row">
		<div class="large-4 column">
			<div class="data-label"><?php echo $element->getAttributeLabel($side.'_standard_intervention_id') ?>:</div>
		</div>
		<div class="large-8 column">
			<div class="data-value"><?php echo $element->{$side.'_standard_intervention'}->name ?></div>
		</div>
	</div>

	<div class="row data-row">
		<div class="large-4 column">
			<div class="data-label"><?php echo $element->getAttributeLabel($side.'_standard_previous') ?>:</div>
		</div>
		<div class="large-8 column">
			<div class="data-value"><?php echo $element->{$side.'_standard_previous'} ? 'Yes' : 'No' ?></div>
		</div>
	</div>

	<div class="row data-row">
		<div class="large-4 column">
			<div class="data-label"><?php echo $element->getAttributeLabel($side.'_intervention_id') ?>:</div>
		</div>
		<div class="large-8 column">
			<div class="data-value"><?php echo $element->{$side.'_intervention'}->name ?></div>
		</div>
	</div>

	<div class="row data-row">
		<div class="large-4 column">
			<div class="data-label"><?php echo $element->getAttributeLabel($side.'_description') ?>:</div>
		</div>
		<div class="large-8 column">
			<div class="data-value"><?php echo Yii::app()->format->Ntext($element->{$side.'_description'}) ?></div>
		</div>
	</div>

	<?php if ($element->needDeviationReasonForSide($side)) { ?>
		<div class="row data-row">
			<div class="large-4 column">
				<div class="data-label"><?php echo $element->getAttributeLabel($side.'_deviationreasons') ?>:</div>
			</div>
			<div class="large-8 column">
				<div class="data-value">
					<ul>
						<?php foreach ($element->{$side.'_deviationreasons'} as $dr) {
                            echo '<li>'.$dr->name.'</li>';
                        }?>
					</ul>
				</div>
			</div>
		</div>
	<?php }?>
<?php } else { ?>
	<div class="row data-row">
		<div class="large-4 column">
			<div class="data-label"><?php echo $element->getAttributeLabel($side.'_condition_rare') ?>:</div>
		</div>
		<div class="large-8 column">
			<div class="data-value"><?php echo $element->{$side.'_condition_rare'} ? 'Yes' : 'No' ?></div>
		</div>
	</div>

	<div class="row data-row">
		<div class="large-4 column">
			<div class="data-label"><?php echo $element->getAttributeLabel($side.'_incidence') ?>:</div>
		</div>
		<div class="large-8 column">
			<div class="data-value"><?php echo Yii::app()->format->Ntext($element->{$side.'_incidence'}) ?></div>
		</div>
	</div>
<?php }?>

<div class="row data-row">
	<div class="large-4 column">
		<div class="data-label"><?php echo $element->getAttributeLabel($side.'_patient_different') ?>:</div>
	</div>
	<div class="large-8 column">
		<div class="data-value"><?php echo Yii::app()->format->Ntext($element->{$side.'_patient_different'}) ?></div>
	</div>
</div>

<div class="row data-row">
	<div class="large-4 column">
		<div class="data-label"><?php echo $element->getAttributeLabel($side.'_patient_gain') ?>:</div>
	</div>
	<div class="large-8 column">
		<div class="data-value"><?php echo Yii::app()->format->Ntext($element->{$side.'_patient_gain'}) ?></div>
	</div>
</div>

<?php if ($element->{$side.'_previnterventions'}) { ?>
	<div class="row data-row">
		<div class="large-4 column">
			<div class="data-label"><?php echo $element->getAttributeLabel($side.'_previnterventions') ?>:</div>
		</div>
		<div class="large-8 column">
			<?php
                foreach ($element->{$side.'_previnterventions'} as $previntervention) {
                    $this->renderPartial('view_OphCoTherapyapplication_ExceptionalCircumstances_PastIntervention', array(
                        'pastintervention' => $previntervention,
                    ));
                }
            ?>
		</div>
	</div>
<?php } ?>

<?php if ($element->{$side.'_relevantinterventions'}) { ?>
	<div class="row data-row">
		<div class="large-4 column">
			<div class="data-label"><?php echo $element->getAttributeLabel($side.'_relevantinterventions') ?>:</div>
		</div>
		<div class="large-8 column">
			<?php
            foreach ($element->{$side.'_relevantinterventions'} as $relevantintervention) {
                $this->renderPartial('view_OphCoTherapyapplication_ExceptionalCircumstances_PastIntervention', array(
                        'pastintervention' => $relevantintervention,
                    ));
            }
            ?>
		</div>
	</div>
<?php } ?>

<div class="row data-row">
	<div class="large-4 column">
		<div class="data-label"><?php echo $element->getAttributeLabel($side.'_patient_factors') ?>:</div>
	</div>
	<div class="large-8 column">
		<div class="data-value"><?php echo $element->{$side.'_patient_factors'} ? 'Yes' : 'No'?></div>
	</div>
</div>

<?php if ($element->{$side.'_patient_factors'}) { ?>
	<div class="row data-row">
		<div class="large-4 column">
			<div class="data-label"><?php echo $element->getAttributeLabel($side.'_patient_factor_details') ?>:</div>
		</div>
		<div class="large-8 column">
			<div class="data-value"><?php echo Yii::app()->format->Ntext($element->{$side.'_patient_factor_details'}) ?></div>
		</div>
	</div>
<?php } ?>

<div class="row data-row">
	<div class="large-4 column">
		<div class="data-label"><?php echo $element->getAttributeLabel($side.'_patient_expectations') ?>:</div>
	</div>
	<div class="large-8 column">
		<div class="data-value"><?php echo Yii::app()->format->Ntext($element->{$side.'_patient_expectations'}) ?></div>
	</div>
</div>

<div class="row data-row">
	<div class="large-4 column">
		<div class="data-label"><?php echo $element->getAttributeLabel($side.'_start_period_id') ?>:</div>
	</div>
	<div class="large-8 column">
		<div class="data-value"><?php echo $element->{$side.'_start_period'}->name ?></div>
	</div>
</div>

<?php if ($element->{$side.'_start_period'}->urgent) { ?>
	<div class="row data-row">
		<div class="large-4 column">
			<div class="data-label"><?php echo $element->getAttributeLabel($side.'_urgency_reason') ?>:</div>
		</div>
		<div class="large-8 column">
			<div class="data-value"><?php echo Yii::app()->format->Ntext($element->{$side.'_urgency_reason'}) ?></div>
		</div>
	</div>
<?php } ?>

<?php if ($element->{$side.'_filecollections'} && (isset($status) && ($status != OphCoTherapyapplication_Processor::STATUS_SENT))) { ?>
	<div class="row data-row">
		<div class="large-4 column">
			<div class="data-label"><?php echo $element->getAttributeLabel($side.'_filecollections') ?>:</div>
		</div>
		<div class="large-8 column">
			<div class="data-value">
				<ul style="display: inline-block">
		<?php foreach ($element->{$side.'_filecollections'} as $filecoll) { ?>
		<li><a href="<?php echo $filecoll->getDownloadURL() ?>"><?php echo $filecoll->name ?></a></li>
		<?php } ?>
		</ul></div>
		</div>
	</div>
<?php } ?>
