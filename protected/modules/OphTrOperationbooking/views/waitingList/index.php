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
<div class="oe-full-header flex-layout">
  <div class="title wordcaps">Partial bookings waiting list</div>
  <div>
        <?php if ($this->checkAccess('OprnPrint')) { ?>
        <button id="btn_print_all" class="button header-tab">Print all</button>
        <button id="btn_print" class="button-header-tab">Print selected</button>
        <?php } ?>
  </div>
</div>

<div class="oe-full-content subgrid oe-partial-waiting">

  <nav class="oe-full-side-panel partial-waiting-filters">
    <div class="row divider">
      <form class="data-group search-filters waiting-list" method="post"
            action="<?= Yii::app()->createUrl('/OphTrOperationbooking/waitingList/search') ?>"
            id="waitingList-filter">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken ?>"/>
        <h4>Subspecialty</h4>
            <?= CHtml::dropDownList(
                'subspecialty-id',
                @$_POST['subspecialty-id'],
                Subspecialty::model()->getList(),
                array(
                  'class' => 'cols-full',
                  'empty' => 'All specialties',
                  'ajax' => array(
                      'type' => 'POST',
                      'data' => array(
                          'subspecialty_id' => 'js:this.value',
                          'YII_CSRF_TOKEN' => Yii::app()->request->csrfToken,
                      ),
                      'url' => Yii::app()->createUrl('/OphTrOperationbooking/waitingList/filterFirms'),
                      'success' => "js:function(data) { $('#firm-id').html(data); }",
                  ),
                )
            ) ?>

        <h4><?= ucfirst(Yii::app()->params['service_firm_label']); ?></h4>

            <?php
            $filtered_firms = $this->getFilteredFirms(@$_POST['subspecialty-id']);
            $selected = (count($filtered_firms) == 1 ? array_keys($filtered_firms)[0] : @$_POST['firm-id']);
            $options = array(
              'disabled' => !@$_POST['firm-id'],
              'class' => 'cols-full',
              'empty' => "All " . Yii::app()->params['service_firm_label'] . "s",
            );

            echo CHtml::dropDownList('firm-id', $selected, $filtered_firms, $options) ?>


        <h4>Next letter due</h4>
            <?=\CHtml::dropDownList(
                'status',
                @$_POST['status'],
                Element_OphTrOperationbooking_Operation::getLetterOptions(),
                array(
                  'class' => 'cols-full',
                )
            ) ?>

        <h4>Site</h4>
            <?=\CHtml::dropDownList(
                'site_id',
                @$_POST['site_id'],
                CHtml::listData(OphTrOperationbooking_Operation_Theatre::getSiteList(), 'id', 'short_name'),
                array('empty' => 'All sites', 'class' => 'cols-full')
            ); ?>

        <h4>Patient Identifier</h4>
            <?=\CHtml::textField('patient_identifier_value', @$_POST['patient_identifier_value'],
                array(
                  'autocomplete' => Yii::app()->params['html_autocomplete'],
                  'size' => 12,
                  'class' => 'search cols-full',
                  'placeholder' => 'Enter Patient Identifier',
              )) ?>

          <h4>Status</h4>
                <?=\CHtml::dropDownList(
                    'booking_status',
                    \Yii::app()->request->getParam('booking_status', ''),
                    \CHtml::listData(OphTrOperationbooking_Operation_Status::model()->findAllByAttributes(
                        [ 'name' => [ 'On-Hold', 'Requires scheduling', 'Requires rescheduling', ], ]
                    ), 'id', 'name'),
                    array(
                        'empty' => 'All',
                        'class' => 'cols-full',
                    )
                ) ?>

          <h4>PAC Outcome</h4>
          <label for="fit-for-surgery-checkbox">
              <span class="pac-state-icon fit js-has-tooltip" data-tooltip-content="PAC<br/>Patient is fit for surgery" >PAC</span>
          </label>
          <input id="fit-for-surgery-checkbox" name="patient-is-fit-for-surgery" type="checkbox" class="fit-for-surgery-checkbox pac-outcome-checkbox">

          <label for="reschedule-surgery-checkbox">
              <span class="pac-state-icon reschedule js-has-tooltip" data-tooltip-content="PAC<br/>Re-schedule date - patient is fit for surgery">PAC</span>
          </label>
          <input id="reschedule-surgery-checkbox" name="reschedule-surgery-date" type="checkbox" class="reschedule-surgery-checkbox pac-outcome-checkbox">

          <label for="hold-for-outstanding">
              <span class="pac-state-icon hold js-has-tooltip" data-tooltip-content="PAC<br/>Hold for outstanding actions">PAC</span>
          </label>
          <input id="hold-for-outstanding" name="hold-for-outstanding-actions" type="checkbox" class="hold-for-outstanding-checkbox pac-outcome-checkbox">

          <label for="the-patient-is-not-fit">
              <span class="pac-state-icon not-fit js-has-tooltip" data-tooltip-content="PAC<br/>Patient is not fit/ready for surgery">PAC</span>
          </label>
          <input id="the-patient-is-not-fit" name="the-patient-is-not-fit" type="checkbox" class="the-patient-is-not-fit-checkbox pac-outcome-checkbox">
        <div class="row">
          <button class="green hint cols-full" type="submit">Search Waiting List</button>
        </div>
      </form>
    </div>

        <?php if ($this->checkAccess('OprnOperationBookingLetterSend')) { ?>
            <div class="row divider">
              <h3>Letters sent</h3>
                  <table class="standard last-right no-padding">
                    <colgroup>
                      <col class="cols-8">
                    </colgroup>
                    <tbody>
                    <tr>
                      <td>Set latest letter sent to be:</td>
                      <td>
                        <input id="adminconfirmdate" name="adminconfirmdate" class="datepicker1 date" placeholder="from"
                               type="text" value="<?= date('j M Y') ?>">
                      </td>
                    </tr>
                    <tr>
                      <td>Letter filter</td>
                      <td>
                        <select name="adminconfirmto" id="adminconfirmto" class="cols-full">
                          <option value="OFF">Off</option>
                          <option value="noletters">No letters sent</option>
                          <option value="0">Invitation letter</option>
                          <option value="1">1st reminder letter</option>
                          <option value="2">2nd reminder letter</option>
                          <option value="3"><?php echo \SettingMetadata::model()->getSetting('gp_label')?> letter</option>
                        </select>
                      </td>
                    </tr>
                    </tbody>
                  </table>
                <?php if ($this->checkAccess('OprnConfirmBookingLetterPrinted')) { ?>
                  <div class="row">
                    <button type="submit" class="green hint cols-full" id="btn_confirm_selected">
                      Confirm selected
                    </button>
                  </div>
                <?php } ?>
            </div>
        <?php } ?>
  </nav>

  <main class="oe-full-main partial-waiting-main" id="searchResults">
  </main>

  <i id="search-loading-msg" class="spinner" style="display: none;"></i>
</div>

<script type="text/javascript">
  $(function () {
    pickmeup('#adminconfirmdate', {
      format: 'd b Y',
      hide_on_select: true,
      max: 'today',
    });
  });
</script>
