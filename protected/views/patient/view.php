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
$clinical = $clinical = $this->checkAccess('OprnViewClinical');

$warnings = $this->patient->getWarnings($clinical);
?>
<main class="main-event edit" id="event-content">
    <div class="messages patient">
        <?php $this->renderPartial('//base/_messages'); ?>
        <?php if ($this->patient->contact->address && !$this->patient->contact->address->isCurrent()) {?>
      <div id="no-current-address-error" class="alert-box alert with-icon">
        Warning: The patient has no current address. The address shown is their last known address.
      </div>
        <?php }?>

        <?php if ($this->patient->isDeceased()) {?>
      <div id="deceased-notice" class="alert-box alert with-icon">
        This patient is deceased (<?= $this->patient->NHSDate('date_of_death'); ?>)
      </div>
        <?php }?>

        <?php if (!$this->patient->practice || !$this->patient->practice->contact->address) {?>
      <div id="no-practice-address" class="alert-box alert with-icon">
        Patient has no <?php echo \SettingMetadata::model()->getSetting('gp_label') ?> practice address, please correct in PAS before printing <?php echo \SettingMetadata::model()->getSetting('gp_label') ?> letter.
      </div>
        <?php }?>

        <?php if ($warnings) { ?>
      <div class="alert-box patient with-icon">
            <?php foreach ($warnings as $warn) {?>
            <strong><?= $warn['long_msg']; ?></strong>
            - <?= $warn['details'];
            }?>
      </div>
        <?php }?>

        <?php $this->renderPartial('//patient/_patient_alerts')?>
        </div>
    <div class="patient-content flex-layout flex-top col-gap-small">
    <div class="cols-half">
            <?php if (($refresh_url = Yii::app()->params['patient_refresh_url'])) : ?>
                <section class="element patient-info">
                    <div class="data-group">
                        <?php $last_updated = strtotime($this->patient->last_modified_date) ?>
                        <div class="cols-4 column data-label">Last updated:</div>
                        <div class="cols-5 column data-value"><?= date(Helper::NHS_DATE_FORMAT.' H:i', $last_updated) ?></div>
                        <div class="cols-3 column">
                            <?= CHtml::beginForm($refresh_url) ?>
                                <input type="hidden" name="patient_id" value="<?= $this->patient->id ?>">
                                <button class="small <?php if ($last_updated > (time() - 300)) echo ' disabled' ?>">Refresh</button>
                            <?= CHtml::endForm() ?>
                        </div>
                    </div>
                </section>
            <?php endif ?>
            <?php $this->renderPartial('_patient_details')?>
            <?php $this->renderPartial('_patient_contact_details')?>
            <?php $this->renderPartial('_patient_gp')?>
            <?php $this->renderPartial('_patient_commissioningbodies')?>
            <?php $this->renderPartial('_patient_contacts')?>
            <?php $this->renderModulePartials('patient_summary_column1')?>
    </div>
    <div class="cols-half" id="patient-summary-form-container">
        <?php if ($component = $this->getApp()->getComponent('internalReferralIntegration')) : ?>
          <section class="element view full patient-info internalreferral internalreferral-doclist">
              <?=\CHtml::link('View patient referrals', $component->generateUrlForDocumentList($this->patient)); ?>
            <i class="spinner" title="Loading..." style="display: none;"></i>
            <span>e-WinDIP</span>
          </section>
        <?php endif; ?>

            <?php if ($this->checkAccess('OprnViewClinical')) {?>
                <?php $this->renderPartial('_patient_episodes', array(
                    'episodes' => $episodes,
                    'ordered_episodes' => $ordered_episodes,
                    'legacyepisodes' => $legacyepisodes,
                    'episodes_open' => $episodes_open,
                    'episodes_closed' => $episodes_closed,
                    'firm' => $firm,
                ))?>
            <?php }?>
        </div>
    </div>
</main>
