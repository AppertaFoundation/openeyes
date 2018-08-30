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
$navIconsUrl = Yii::app()->assetManager->getPublishedUrl(Yii::getPathOfAlias('application.assets.newblue')) . '/svg/oe-nav-icons.svg';
/***
 * @var $trialContext TrialContext
 */
$trialContext = null;
if ($this->trial){
    $trialContext = $this->createWidget(
            'application.modules.OETrial.widgets.TrialContext',
            array('patient' => $this->patient, 'trial' => $this->trial)
    );
}
?>

<div id="oe-patient-details"
     class="oe-patient"
     data-patient-id="<?= $this->patient->id ?>"
     style="position: unset; background: unset; width: unset; background-color: rgb(20, 30, 43);"
>
    <div class="patient-name" style="position: unset">
        <a href="<?= (new CoreAPI())->generateEpisodeLink($this->patient); ?>">
            <span class="patient-surname"><?php echo $this->patient->getLast_name(); ?></span>,
            <span class="patient-firstname">
              <?php echo $this->patient->getFirst_name(); ?>
              <?php echo $this->patient->getTitle() ? "({$this->patient->getTitle()})" : ''; ?>
            </span>
        </a>
    </div>

    <div class="flex-layout">
        <div class="patient-details" style="position: unset">
            <div class="hospital-number">
                <span>No. </span>
                <?php echo $this->patient->hos_num ?>
            </div>
            <div class="nhs-number">
                <span><?php echo Yii::app()->params['nhs_num_label'] ?></span>
                <?php echo $this->patient->nhsnum ?>
                <?php if ($this->patient->nhsNumberStatus && $this->patient->nhsNumberStatus->isAnnotatedStatus()): ?>
                    <i class="oe-i asterisk small" aria-hidden="true"></i><span
                            class="messages"><?= $this->patient->nhsNumberStatus->description; ?></span>
                <?php endif; ?>
            </div>

            <div class="patient-gender">
                <em>Gender</em>
                <?php echo $this->patient->getGenderString() ?>
            </div>

            <div class="patient-age">
                <em>Age</em>
                <?php echo $this->patient->getAge(); ?>
            </div>
            <?php if ($trialContext) {
                echo $trialContext->renderPatientTrialStatus();
                echo $trialContext->renderAddToTrial();
            }?>

        </div>
        <div class="flex-layout flex-left">
            <?php if ($this->patient->allergyAssignments || $this->patient->risks || $this->patient->getDiabetes()) { ?>
                <div class="patient-allergies-risks risk-warning"
                     id="js-allergies-risks-btn"
                     style="position: unset; padding-right: 24px"
                >
                    <?= $this->patient->allergyAssignments ? 'Allergies' : ''; ?>
                    <?= $this->patient->allergyAssignments && $this->patient->risks ? ', ' : ''; ?>
                    <?= $this->patient->risks || $this->patient->getDiabetes() ? 'Alerts' : ''; ?>
                </div>
            <?php } ?>
            <div class="patient-demographics" id="js-demographics-btn" style="position: unset">
                <svg viewBox="0 0 60 60" class="icon" style="pointer-events: none">
                    <use xlink:href="<?php echo $navIconsUrl; ?>#info-icon"></use>
                </svg>
            </div>
            <div class="patient-management" id="js-management-btn" style="position: unset">
                <svg viewBox="0 0 30 30" class="icon" style="pointer-events: none">
                    <use xlink:href="<?php echo $navIconsUrl; ?>#patient-icon"></use>
                </svg>
            </div>
            <div class="patient-quicklook" id="js-quicklook-btn" style="position: unset">
                <svg viewBox="0 0 30 30" class="icon" style="pointer-events: none">
                    <use xlink:href="<?php echo $navIconsUrl; ?>#quicklook-icon"></use>
                </svg>
            </div>
        </div>
    </div>
    <!-- Widgets (extra icons, links etc) -->
    <ul class="patient-widgets">
        <?php foreach ($this->widgets as $widget) {
            echo "<li>{$widget}</li>";
        } ?>
    </ul>
    <style>
        .patient-widgets .oe-patient-popup {
            top: unset;
        }
    </style>
</div>
</div>
<?php
$assetManager = Yii::app()->getAssetManager();
$widgetPath = $assetManager->publish('protected/widgets/js');
Yii::app()->clientScript->registerScriptFile($widgetPath . '/PatientPanelPopup.js');
?>
<script type="text/javascript">
    $(function () {
        //console.log($('[id=oe-patient-details][data-patient-id=<?//= $this->patient->id?>//]'));
        PatientPanel.patientPopups.init($('[id=oe-patient-details][data-patient-id=<?= $this->patient->id?>]'));
        // PatientPanel.patientPopups.init();

        $('.js-patient-expand-btn').each(function () {
            $(this).click(function () {
                $(this).toggleClass('collapse expand');
                $(this).parents('table').find('tbody').toggle();
            });
        });
    });
</script>
