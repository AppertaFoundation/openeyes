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

<div id="oe-patient-details"
     class="js-oe-patient <?= $this->list_mode ? 'oe-list-patient' : 'oe-patient' ?> <?= $deceased ? 'deceased' : '' ?>"
     data-patient-id="<?= $this->patient->id ?>"
>
    <div class="patient-name">
        <?php if (!$this->patient->isDeleted()) : ?>
        <a href="<?= (new CoreAPI())->generatePatientLandingPageLink($this->patient); ?>">
        <?php else : ?>
            <a>
        <?php endif; ?>
                <span class="patient-surname"><?php echo $this->patient->getLast_name(); ?></span>,
                <span class="patient-firstname">
                <?php echo $this->patient->getFirst_name(); ?>
                    <?php echo $this->patient->getTitle() ? "({$this->patient->getTitle()})" : ''; ?>
                    <?php echo $this->patient->isDeleted() ? "(Deleted)" : ''; ?>
            </span>
            </a>
    </div>

    <div class="flex-layout">
        <div class="patient-details">
            <div class="hospital-number">
                <!--                Displaying only ID label (instead of CERA ID) to avoid overlapping issue for CERA, it should not affect UK's implementation-->
                <span><?php echo ( Yii::app()->params['institution_code'] === 'CERA' ? explode(" ", \SettingMetadata::model()->getSetting('hos_num_label'))[1] : \SettingMetadata::model()->getSetting('hos_num_label') ) ?> </span>
                <div class="js-copy-to-clipboard hospital-number" style="cursor: pointer;"> <?php echo $this->patient->hos_num ?></div>
            </div>
            <div class="nhs-number">
                <!--                Displaying only Medicare label (instead of Medicare ID) to avoid overlapping issue for CERA, it should not affect UK's implementation-->
                <span><?php echo ( Yii::app()->params['institution_code'] === 'CERA' ? explode(" ", \SettingMetadata::model()->getSetting('nhs_num_label'))[0] : \SettingMetadata::model()->getSetting('nhs_num_label') ) ?></span>
                    <?php echo $this->patient->nhsnum ?>
                    <?php if ($this->patient->nhsNumberStatus) : ?>
                  <i class="oe-i <?= isset($this->patient->nhsNumberStatus->icon->class_name) ? $this->patient->nhsNumberStatus->icon->class_name : 'exclamation' ?> small"></i>
                    <?php endif; ?>
            </div>

            <div class="patient-gender">
                <!--                Displaying Gen. (instead of Gender) to avoid overlapping issue for CERA, it should not affect UK's implementation-->
                <em><?php echo (Yii::app()->params['institution_code'] === 'CERA' ? 'Gen.' : 'Gender') ?></em>
                    <?php echo $this->patient->getGenderString() ?>
            </div>
            <div class="patient-<?= $deceased ? 'died' : 'age' ?>">
               <?php if ($deceased) : ?>
                   <em>Died</em> <?= Helper::convertDate2NHS($this->patient->date_of_death); ?>
               <?php endif; ?>
                <em>Age<?= $deceased ? 'd' : '' ?></em> <?= $this->patient->getAge(); ?>
            </div>
                <?php if ($trialContext) {
                    echo $trialContext->renderPatientTrialStatus();
                    echo $trialContext->renderAddToTrial();
                } ?>
        </div>
        <div class="flex-layout flex-right">
                    <?php if (!$deceased) { ?>
                        <?php if ($this->patient->allergyAssignments || $this->patient->risks || $this->patient->getDiabetes()) { ?>
                  <div class="patient-allergies-risks risk-warning js-allergies-risks-btn">
                                        <?= $this->patient->allergyAssignments ? 'Allergies' : ''; ?>
                                        <?= $this->patient->allergyAssignments && $this->patient->risks ? ', ' : ''; ?>
                                        <?= $this->patient->risks || $this->patient->getDiabetes() ? 'Alerts' : ''; ?>
                  </div>
                        <?php } elseif (!$this->patient->hasAllergyStatus() && !$this->patient->hasRiskStatus()) { ?>
                  <div class="patient-allergies-risks unknown js-allergies-risks-btn">
                      Allergies, Alerts
                  </div>
                        <?php } elseif ($this->patient->no_risks_date && $this->patient->no_allergies_date) { ?>
                  <div class="patient-allergies-risks no-risk js-allergies-risks-btn">
                      Allergies, Alerts
                  </div>
                        <?php } else { /*either risk or allergy status in unknown*/ ?>
                  <div class="patient-allergies-risks unknown js-allergies-risks-btn">
                      Allergies, Alerts
                  </div>
                        <?php }
                    } ?>
            <div class="patient-demographics js-demographics-btn" id="js-demographics-btn">
                <svg viewBox="0 0 60 60" class="icon">
                    <use xlink:href="<?php echo $navIconsUrl; ?>#info-icon"></use>
                </svg>
            </div>

                    <?php
                    if (Yii::app()->user->checkAccess('OprnViewClinical')) {?>
              <div class="patient-management js-management-btn">
                  <svg viewBox="0 0 30 30" class="icon">
                      <use xlink:href="<?php echo $navIconsUrl; ?>#patient-icon"></use>
                  </svg>
              </div>
              <div class="patient-quicklook js-quicklook-btn" id="js-quicklook-btn">
                  <svg viewBox="0 0 30 30" class="icon">
                      <use xlink:href="<?php echo $navIconsUrl; ?>#quicklook-icon"></use>
                  </svg>
              </div>
                    <?php }?>

                    <?php if ($this->patient->isEditable() && !$this->patient->isDeleted()) : ?>
              <div class="patient-local-edit js-patient-local-edit-btn"
                                <?php if (Yii::app()->moduleAPI->get('OETrial') && count($this->patient->trials)) {
                                    echo 'style ="top: 35px; right: 0px"';
                                }?>
              >
                  <a href="<?php echo $this->controller->createUrl('/patient/update/', array('id' => $this->patient->id, 'prevUrl' => Yii::app()->request->url)); ?>" >
                      <svg viewBox="0 0 30 30" class="icon">
                          <use xlink:href="<?php echo $navIconsUrl; ?>#local-edit-icon"></use>
                      </svg>
                  </a>
              </div>
                    <?php endif; ?>
                    <?php if ((Yii::app()->moduleAPI->get('OETrial')) && (count($this->patient->trials) !== 0)) { ?>
              <div class="patient-trials js-trials-btn">
                  <svg viewBox="0 0 30 30" class="icon">
                      <use xlink:href="<?php echo $navIconsUrl; ?>#trials-icon"></use>
                  </svg>
              </div>
                    <?php } ?>
        </div>
    </div>
      <?php if (Yii::app()->getAuthManager()->checkAccess('OprnViewClinical', Yii::app()->user->id)) : ?>
        <ul class="patient-widgets" >
            <?php foreach ($this->widgets as $widget) {
                echo "<li>{$widget}</li>";
            } ?>
    </ul>
      <?php endif; ?>
</div>
