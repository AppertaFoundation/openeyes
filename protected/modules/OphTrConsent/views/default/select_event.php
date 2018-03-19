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
<?php
$this->beginContent('//patient/event_container');
$assetAliasPath = 'application.modules.OphTrOperationbooking.assets';
$this->moduleNameCssClass .= ' edit';
?>
<section class="element edit full edit-create-consent-form">

    <?php $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
        'id' => 'consent-form',
        'enableAjaxValidation' => false,
        // 'focus'=>'#procedure_id'
    ));
    // Event actions
    $this->event_actions[] = EventAction::button('Create Consent Form', 'save', array('level' => 'secondary'),
        array('class' => 'small', 'form' => 'consent-form'));
    ?>
    <?php $this->displayErrors($errors) ?>

  <header class="element-header">
    <h3 class="element-title">Create Consent Form</h3>
  </header>
  <div class="element-actions">
          <span class="js-remove-element">
            <i class="oe-i remove-circle"></i>
          </span>
  </div>
  <div class="element-fields flex-layout flex-top col-gap full-width">
    <div class="cols-3">
        <?php if (count($bookings) > 0) { ?>
          Please indicate whether this Consent Form is for an existing booking or for unbooked procedures:
        <?php } else { ?>
          There are no open bookings in the current episode so you can only create a consent form for unbooked procedures.
        <?php } ?>
    </div>

    <div class="cols-8">
      <table class="cols-full last-left large-text">
        <tbody>
        <?php if ($bookings) {
            foreach ($bookings as $operation) { ?>
              <tr>
                <td>
                  <input type="radio" value="booking<?php echo $operation->event_id ?>" name="SelectBooking"/>
                </td>
                <td>
                  <i class="oe-i-e i-TrOperation"></i>
                </td>
                <td>
                    <?php echo $operation->booking ? $operation->booking->session->NHSDate('date') : 'UNSCHEDULED' ?>
                </td>
                <td>
                  Operation
                </td>
                <td>
                    <?php foreach ($operation->procedures as $i => $procedure) {
                        if ($i > 0) {
                            echo '<br/>';
                        }
                        echo $operation->eye->name . ' ' . $procedure->term;
                    } ?>
                </td>
              </tr>
                <?php if (Element_OphTrConsent_Procedure::model()->find('booking_event_id=?',
                    array($operation->event_id))) { ?>
                <div class="alert-box alert with-icon">
                  Warning: this booking already has a consent form
                </div>
                <?php } ?>
            <?php }
        } ?>
        <tr>
          <td>
            <input type="radio" value="unbooked" name="SelectBooking"
                   <?php if (count($bookings) == 0) { ?>checked="checked" <?php } ?>/>
          </td>
          <td></td>
          <td>
            Unbooked procedures
          </td>
        </tr>
        </tbody>
      </table>
    </div>
  </div>
    <?php $this->displayErrors($errors, true) ?>
    <?php $this->endWidget(); ?>
</section>
<?php $this->endContent(); ?>
