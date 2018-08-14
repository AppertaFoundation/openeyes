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

?>

<div id="oe-patient-details" class="oe-patient">
  <div class="patient-name">
    <span class="patient-surname"><?php echo $this->patient->getLast_name(); ?></span>,
    <span class="patient-firstname">
      <?php echo $this->patient->getFirst_name(); ?>
      <?php echo $this->patient->getTitle() ? "({$this->patient->getTitle()})" : ''; ?>
    </span>
  </div>

  <div class="patient-details">
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

  </div>
    <?php if ($this->patient->allergyAssignments || $this->patient->risks || $this->patient->getDiabetes()) { ?>
      <div class="patient-allergies-risks risk-warning" id="js-allergies-risks-btn">
          <?= $this->patient->allergyAssignments ? 'Allergies' : ''; ?>
          <?= $this->patient->allergyAssignments && $this->patient->risks ? ', ' : ''; ?>
          <?= $this->patient->risks || $this->patient->getDiabetes() ? 'Alerts' : ''; ?>
      </div>
    <?php } elseif (!$this->patient->hasAllergyStatus() || !$this->patient->hasRiskStatus()) { ?>
      <div class="patient-allergies-risks no-risk" id="js-allergies-risks-btn">
        Allergies, Alerts
      </div>
    <?php } elseif ($this->patient->no_risks_date && $this->patient->no_allergies_date) { ?>
      <div class="patient-allergies-risks unknown" id="js-allergies-risks-btn">
        Allergies, Alerts
      </div>
    <?php } ?>
  <div class="patient-demographics" id="js-demographics-btn">
    <svg viewBox="0 0 60 60" class="icon" style="pointer-events: none">
      <use xlink:href="<?php echo $navIconsUrl; ?>#info-icon"></use>
    </svg>
  </div>
  <div class="patient-management" id="js-management-btn">
    <svg viewBox="0 0 30 30" class="icon" style="pointer-events: none">
      <use xlink:href="<?php echo $navIconsUrl; ?>#patient-icon"></use>
    </svg>
  </div>
  <div class="patient-quicklook" id="js-quicklook-btn">
    <svg viewBox="0 0 30 30" class="icon" style="pointer-events: none">
      <use xlink:href="<?php echo $navIconsUrl; ?>#quicklook-icon"></use>
    </svg>
  </div>

    <?php if ($this->patient->isEditable()): ?>
      <div class="patient-local-edit" id="js-patient-local-edit-btn">
        <svg viewBox="0 0 30 30" class="icon" style="pointer-events: none">
          <use xlink:href="<?php echo $navIconsUrl; ?>#local-edit-icon"></use>
        </svg>
      </div>
    <?php endif; ?>

     <!-- Widgets (extra icons, links etc) -->
  <ul class="patient-widgets">
      <?php foreach ($this->widgets as $widget) {
        echo "<li>{$widget}</li>";
        }?>
        </ul>
  </div>
</div>
<?php
$assetManager = Yii::app()->getAssetManager();
$widgetPath = $assetManager->publish('protected/widgets/js');
Yii::app()->clientScript->registerScriptFile($widgetPath . '/PatientPanelPopup.js');
?>
<script type="text/javascript">
  $(function () {

    PatientPanel.patientPopups.init();

    $('#js-patient-local-edit-btn').click(function (e) {
      e.preventDefault();
      location.href = "<?php echo $this->controller->createUrl('/patient/update/' . $this->patient->id); ?>";
      return false;
    });
    $('.js-patient-expand-btn').each(function () {
      $(this).click(function () {
        $(this).toggleClass('collapse expand');
        $(this).parents('table').find('tbody').toggle();
      });
    });
  });
</script>
