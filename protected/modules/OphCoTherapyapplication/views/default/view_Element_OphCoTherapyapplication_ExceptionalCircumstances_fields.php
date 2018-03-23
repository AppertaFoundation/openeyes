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
<table>
  <tbody>
  <tr>
    <td>
        <?php echo $element->getAttributeLabel($side . '_standard_intervention_exists') ?>:
    </td>
    <td>
        <?php echo $element->{$side . '_standard_intervention_exists'} ? 'Yes' : 'No' ?>
    </td>
  </tr>

  <?php if ($element->{$side . '_standard_intervention_exists'}) { ?>
    <tr>
      <td>
          <?php echo $element->getAttributeLabel($side . '_standard_intervention_id') ?>:
      </td>
      <td>
          <?php echo $element->{$side . '_standard_intervention'}->name ?>
      </td>
    </tr>

    <tr>
      <td>
          <?php echo $element->getAttributeLabel($side . '_standard_previous') ?>:
      </td>
      <td>
          <?php echo $element->{$side . '_standard_previous'} ? 'Yes' : 'No' ?>
      </td>
    </tr>

    <tr>
      <td>
          <?php echo $element->getAttributeLabel($side . '_intervention_id') ?>:
      </td>
      <td>
          <?php echo $element->{$side . '_intervention'}->name ?>
      </td>
    </tr>

    <tr>
      <td>
          <?php echo $element->getAttributeLabel($side . '_description') ?>:
      </td>
      <td>
          <?php echo Yii::app()->format->Ntext($element->{$side . '_description'}) ?>
      </td>
    </tr>

      <?php if ($element->needDeviationReasonForSide($side)) { ?>
      <tr>
        <td>
            <?php echo $element->getAttributeLabel($side . '_deviationreasons') ?>:
        </td>
        <td>
          <ul>
              <?php foreach ($element->{$side . '_deviationreasons'} as $dr) {
                  echo '<li>' . $dr->name . '</li>';
              } ?>
          </ul>
        </td>
      </tr>
      <?php } ?>
  <?php } else { ?>
    <tr>
      <td>
          <?php echo $element->getAttributeLabel($side . '_condition_rare') ?>:
      </td>
      <td>
          <?php echo $element->{$side . '_condition_rare'} ? 'Yes' : 'No' ?>
      </td>
    </tr>

    <tr>
      <td>
          <?php echo $element->getAttributeLabel($side . '_incidence') ?>:
      </td>
      <td>
          <?php echo Yii::app()->format->Ntext($element->{$side . '_incidence'}) ?>
      </td>
    </tr>
  <?php } ?>

  <tr>
    <td>
        <?php echo $element->getAttributeLabel($side . '_patient_different') ?>:
    </td>
    <td>
        <?php echo Yii::app()->format->Ntext($element->{$side . '_patient_different'}) ?>
    </td>
  </tr>

  <tr>
    <td>
        <?php echo $element->getAttributeLabel($side . '_patient_gain') ?>:
    </td>
    <td>
        <?php echo Yii::app()->format->Ntext($element->{$side . '_patient_gain'}) ?>
    </td>
  </tr>

  <?php if ($element->{$side . '_previnterventions'}) { ?>
    <tr>
      <td>
          <?php echo $element->getAttributeLabel($side . '_previnterventions') ?>:
      </td>
      <td>
          <?php foreach ($element->{$side . '_previnterventions'} as $previntervention) {
              $this->renderPartial('view_OphCoTherapyapplication_ExceptionalCircumstances_PastIntervention', array(
                  'pastintervention' => $previntervention,
              ));
          }
          ?>
      </td>
    </tr>
  <?php } ?>

  <?php if ($element->{$side . '_relevantinterventions'}) { ?>
    <tr>
      <td>
          <?php echo $element->getAttributeLabel($side . '_relevantinterventions') ?>:
      </td>
      <td>
          <?php
          foreach ($element->{$side . '_relevantinterventions'} as $relevantintervention) {
              $this->renderPartial('view_OphCoTherapyapplication_ExceptionalCircumstances_PastIntervention', array(
                  'pastintervention' => $relevantintervention,
              ));
          }
          ?>
      </td>
    </tr>
  <?php } ?>

  <tr>
    <td>
        <?php echo $element->getAttributeLabel($side . '_patient_factors') ?>:
    </td>
    <td>
        <?php echo $element->{$side . '_patient_factors'} ? 'Yes' : 'No' ?>
    </td>
  </tr>

  <?php if ($element->{$side . '_patient_factors'}) { ?>
    <tr>
      <td>
          <?php echo $element->getAttributeLabel($side . '_patient_factor_details') ?>:
      </td>
      <td>
          <?php echo Yii::app()->format->Ntext($element->{$side . '_patient_factor_details'}) ?>
      </td>
    </tr>
  <?php } ?>

  <tr>
    <td>
        <?php echo $element->getAttributeLabel($side . '_patient_expectations') ?>:
    </td>
    <td>
        <?php echo Yii::app()->format->Ntext($element->{$side . '_patient_expectations'}) ?>
    </td>
  </tr>

  <tr>
    <td>
        <?php echo $element->getAttributeLabel($side . '_start_period_id') ?>:
    </td>
    <td>
        <?php echo $element->{$side . '_start_period'}->name ?>
    </td>
  </tr>

  <?php if ($element->{$side . '_start_period'}->urgent) { ?>
    <tr>
      <td>
          <?php echo $element->getAttributeLabel($side . '_urgency_reason') ?>:
      </td>
      <td>
          <?php echo Yii::app()->format->Ntext($element->{$side . '_urgency_reason'}) ?>
      </td>
    </tr>
  <?php } ?>

  <?php if ($element->{$side . '_filecollections'} && (isset($status) && ($status != OphCoTherapyapplication_Processor::STATUS_SENT))) { ?>
    <tr>
      <td>
          <?php echo $element->getAttributeLabel($side . '_filecollections') ?>:
      </td>
      <td>
        <ul style="display: inline-block">
            <?php foreach ($element->{$side . '_filecollections'} as $filecoll) { ?>
              <li><a href="<?php echo $filecoll->getDownloadURL() ?>"><?php echo $filecoll->name ?></a></li>
            <?php } ?>
        </ul>
      </td>
    </tr>
  <?php } ?>
  </tbody>
</table>