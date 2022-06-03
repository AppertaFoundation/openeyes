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
//$clinical = $this->checkAccess('OprnViewClinical');
$warnings = $this->patient->getWarnings($allow_clinical);
$navIconsUrl = Yii::app()->assetManager->getPublishedUrl(Yii::getPathOfAlias('application.assets.newblue'), true) . '/dist/svg/oe-nav-icons.svg';
/***
 * @var $trialContext TrialContext
 */
$trialContext = null;
if ($this->trial) {
    $trialContext = $this->createWidget(
        'application.modules.OETrial.widgets.TrialContext',
        array('patient' => $this->patient, 'trial' => $this->trial)
    );
}
$controllerID = (isset($this->controller->id) ? $this->controller->id : $this->id);

$deceased = $this->patient->isDeceased();
$institution = Institution::model()->getCurrent();

$display_primary_number_usage_code = SettingMetadata::model()->getSetting('display_primary_number_usage_code');
$display_secondary_number_usage_code = SettingMetadata::model()->getSetting('display_secondary_number_usage_code');
$primary_identifier = PatientIdentifierHelper::getIdentifierForPatient($display_primary_number_usage_code, $this->patient->id, $institution->id, $this->selected_site_id);
$secondary_identifier = PatientIdentifierHelper::getIdentifierForPatient($display_secondary_number_usage_code, $this->patient->id, $institution->id, $this->selected_site_id);
?>

<?php
if ($summary) {
    $this->render('application.widgets.views.PatientPanelSummary', array('deceased' => $deceased,'trialContext' => $trialContext,'navIconsUrl' => $navIconsUrl));

    $assetManager = Yii::app()->getAssetManager();
    $widgetPath = $assetManager->publish('protected/widgets/js', true);
    Yii::app()->clientScript->registerScriptFile($widgetPath . '/PatientPanelPopup.js');
} else { ?>
    <td>
        <?php
            $this->render('application.widgets.views.PatientMeta');
        ?>
    </td>
    <td id="oe-patient-details" class="js-oe-patient <?= $this->list_mode ? '' : 'oe-patient' ?> <?= $deceased ? 'deceased' : '' ?>"
        data-patient-id="<?= $this->patient->id ?>" style="text-align: left">
        <?php if (!$deceased) { ?>
            <?php if ($this->patient->allergyAssignments || $this->patient->risks || $this->patient->getDiabetes()) { ?>
            <i class="<?= in_array($controllerID, ['caseSearch','trial','worklist']) ? 'oe-i warning' : 'patient-allergies-risks' ?> medium pad js-allergies-risks-btn">
            </i>
            <?php } elseif (!$this->patient->hasAllergyStatus() && !$this->patient->hasRiskStatus()) { ?>
            <i class="unknown <?= in_array($controllerID, ['caseSearch','trial','worklist']) ? 'oe-i triangle' : 'patient-allergies-risks' ?> medium pad js-allergies-risks-btn"></i>
            <?php } elseif ($this->patient->no_risks_date && $this->patient->no_allergies_date) { ?>
            <i class="no-risk <?= in_array($controllerID, ['caseSearch','trial','worklist']) ? 'oe-i triangle' : 'patient-allergies-risks' ?> medium pad js-allergies-risks-btn"></i>
            <?php } else { /*either risk or allergy status in unknown*/ ?>
            <i class="unknown <?= in_array($controllerID, ['caseSearch','trial','worklist']) ? 'oe-i triangle' : 'patient-allergies-risks' ?> medium pad js-allergies-risks-btn"></i>
            <?php }
        } ?>
            <i class=" js-patient-quick-overview oe-i info medium pad patient-demographics js-demographics-btn" id="js-demographics-btn"></i>
        <?php
        if (Yii::app()->user->checkAccess('OprnViewClinical')) {?>
                <i class="oe-i patient medium pad patient-management js-management-btn" id="js-management-btn"></i>
                <i class="oe-i eye medium pad patient-quicklook js-quicklook-btn" id="js-quicklook-btn"></i>
        <?php }?>
            <?php $has_trial_user_role = Yii::app()->user->checkAccess('Trial User'); ?>
            <?php if ($this->patient->isEditable() && !$this->patient->isDeleted()) : ?>
              <a href="<?php echo $this->controller->createUrl('/patient/update/', array('id' => $this->patient->id, 'prevUrl' => Yii::app()->request->url)); ?>" >
                  <i class="patient-local-edit js-patient-local-edit-btn oe-i medium pad pencil cc_pointer"
                    <?php if (Yii::app()->moduleAPI->get('OETrial') && $has_trial_user_role) {
                        echo 'style ="top: 35px; right: 0px"';
                    }?>
                  ></i>
              </a>
            <?php endif; ?>
            <?php if ((Yii::app()->moduleAPI->get('OETrial')) && $has_trial_user_role) { ?>
                <i class="oe-i trials medium pad patient-extra js-trials-btn" id="js-trials-btn"></i>
            <?php } ?>
        <div class="patient-details">
            <?php if ($trialContext) {
                echo $trialContext->renderPatientTrialStatus();
                echo $trialContext->renderAddToTrial();
            } ?>
        </div>
    </td>
    <?php
      $assetManager = Yii::app()->getAssetManager();
      $widgetPath = $assetManager->publish('protected/widgets/js');
      Yii::app()->clientScript->registerScriptFile($widgetPath . '/PatientPanelPopupMulti.js');
    ?>
<?php } ?>
