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
$exam_api = Yii::app()->moduleAPI->get('OphCiExamination');
$correspondence_api = Yii::app()->moduleAPI->get('OphCoCorrespondence');
//echo "<pr>".print_r($exam_api,1)."</pr>";
//echo "<br>";
//echo "<pr>".print_r($correspondence_api,1)."</pr>";
//die;

?>

<?php
$this->beginContent('//patient/episodes_container', array(
    'cssClass' => isset($cssClass) ? $cssClass : '',
    'episode' => isset($current_episode) ? $current_episode : ''
));


























?>
    <div class="flex-layout flex-top">
        <div class="patient-overview">
            <!-- Patient Quicklook popup. Show Risks, Medical Data, Management Summary and Problem and Plans -->
            <?php
            $visualAcuityRight = $exam_api->getLetterVisualAcuityRight($patient);
            $visualAcuityLeft = $exam_api->getLetterVisualAcuityLeft($patient); ?>

            <!-- situational awareness -->
            <table class="standard last-right">
                <tbody>

                <?php if ($visualAcuityLeft || $visualAcuityRight) {
                    $lDate = $exam_api->getLetterVisualAcuityDate($patient, 'left');
                    $rDate = $exam_api->getLetterVisualAcuityDate($patient, 'right');

                    if ($lDate == $rDate) { ?>
                        <tr>
                            <td>
                                R <?= $visualAcuityRight ?: 'NA'; ?>
                                <?= $visualAcuityRight ? $exam_api->getLetterVAMethodName($patient, 'right') : '' ?>
                                L <?= $visualAcuityLeft ?: 'NA' ?>
                                <?= $visualAcuityLeft ? $exam_api->getLetterVAMethodName($patient, 'left') : '' ?>
                            </td>
                            <td>
                                <small class="fade"><span class="oe-date"><?= Helper::convertDate2NHS($rDate); ?></span>
                                </small>
                            </td>
                        </tr>
                    <?php } else { ?>
                        <tr>
                            <td>
                                R <?= $visualAcuityRight ?: 'NA'; ?>
                                <?= $visualAcuityRight ? $exam_api->getLetterVAMethodName($patient, 'right') : '' ?>
                            </td>
                            <td>
                                <small class="fade"><span
                                            class="oe-date"><?= $visualAcuityRight ? Helper::convertDate2NHS($rDate) : '' ?></span>
                                </small>
                            </td>
                        <tr>
                            <td>
                                L <?= $visualAcuityLeft ?: 'NA' ?>
                                <?= $visualAcuityLeft ? $exam_api->getLetterVAMethodName($patient, 'left') : '' ?>
                            </td>
                            <td>
                                <small class="fade"><span
                                            class="oe-date"><?= $visualAcuityLeft ? Helper::convertDate2NHS($lDate) : '' ?></span>
                                </small>
                            </td>
                        </tr>
                    <?php }
                } else { ?>
                    <tr>
                        <td>VA: NA</td>
                    </tr>
                <?php } ?>

                <tr>
                    <?php
                    $leftRefraction = $correspondence_api->getLastRefraction($patient, 'left');
                    $rightRefraction = $correspondence_api->getLastRefraction($patient, 'right');
                    if ($leftRefraction !== null || $rightRefraction !== null) { ?>
                        <td>
                            <ul class="inline-list">
                                <li>R <?= $rightRefraction ?: 'NA' ?></li>
                                <li>L <?= $leftRefraction ?: 'NA' ?></li>
                            </ul>
                        </td>
                        <td>
                            <small class="fade"><span
                                        class="oe-date"><?= Helper::convertDate2NHS($correspondence_api->getLastRefractionDate($patient)) ?></span>
                            </small>
                        </td>
                    <?php } else { ?>
                        <td>Refraction: NA</td>
                    <?php } ?>
                </tr>
                <tr>
                    <?php if ($patient->getCviSummary()[0] !== 'Unknown') { ?>
                        <td> CVI Status: <?= $patient->getCviSummary()[0]; ?> </td>
                        <td>
                            <small class="fade"><span
                                        class="oe-date"><?= $patient->getCviSummary()[1] && $patient->getCviSummary()[1] !== '0000-00-00' ? \Helper::convertDate2HTML($patient->getCviSummary()[1]) : 'N/A' ?></span>
                            </small>
                        </td>
                    <?php } else { ?>
                        <td>CVI Status:</td>
                        <td>
                            <small class="fade"><span class="oe-date">NA</span></small>
                        </td>

                    <?php } ?>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="patient-overview">
            <table class="standard">
                <tbody>
                <tr>
                    <td><i class="oe-i-e i-CiExamination"></i></td>
                    <td><a href="#">Laser (WHERE FROM)</a></td>
                    <td>Prof. James Morgan</td>
                    <td>
                        <small class="fade">Updated: Today</small>
                    </td>
                </tr>
                <tr>
                    <td><i class="oe-i-e i-DrPrescription"></i></td>
                    <td><a href="#">Prescription</a></td>
                    <td>Prof. James Morgan</td>
                    <td>
                        <small class="fade">Created: Yesterday</small>
                    </td>
                </tr>
                <tr>
                    <td><i class="oe-i-e i-Message"></i></td>
                    <td><a href="#">Message</a></td>
                    <td>David Haider</td>
                    <td>
                        <small class="fade">Created: <span class="oe-date"><span class="day">14</span><span class="mth">Jun</span><span
                                        class="yr">2018</span></span></small>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

<!--TODO: continue from here-->

  <div class="flex-layout flex-top">
    <!-- oe-popup-overflow handles scrolling if data overflow height -->
    <div class="oe-popup-overflow quicklook-data-groups">
      <div class="group">
        <div class="label">Eye diagnoses</div>
        <div class="data">
          <table>
            <tbody>
            <?php
            $ophthalmic_diagnoses = $this->patient->getOphthalmicDiagnosesSummary();
            if (count($ophthalmic_diagnoses) === 0) { ?>
              <tr>
                <td>
                  <div class="nil-recorded">Nil recorded</div>
                </td>
              </tr>
            <?php } ?>

            <?php foreach ($ophthalmic_diagnoses as $ophthalmic_diagnosis) {
                list($side, $name, $date) = explode('~', $ophthalmic_diagnosis, 3); ?>
              <tr>
                <td><?= $name ?></td>
                <td>
                    <?php $this->widget('EyeLateralityWidget', array('laterality' => $side)) ?>
                </td>
                <td>
                  <span class="oe-date"><?= $date ?></span>
                </td>
              </tr>
            <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
      <!-- group-->
      <div class="group">
        <div class="label">Systemic Diagnoses</div>
        <div class="data">
          <table>
            <tbody>
            <?php if (count($this->patient->systemicDiagnoses) === 0) { ?>
              <tr>
                <td>
                  <div class="nil-recorded">Nil recorded</div>
                </td>
              </tr>
            <?php } ?>
            <?php foreach ($this->patient->systemicDiagnoses as $diagnosis) { ?>
              <tr>
                <td> <?= $diagnosis->disorder->term ?></td>
                <td>
                    <?php $this->widget('EyeLateralityWidget', array('eye' => $diagnosis->eye)) ?>
                </td>
                <td><span class="oe-date"><?= $diagnosis->getHTMLformatedDate() ?></span></td>
              </tr>
            <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- oe-popup-overflow handles scrolling if data overflow height -->
    <div class="oe-popup-overflow quicklook-data-groups">
      <!-- Data -->
      <div class="group">
        <div class="label">Surgical History</div>
        <div class="data">
            <?php $this->widget(\OEModule\OphCiExamination\widgets\PastSurgery::class, array(
                'patient' => $this->patient,
                'mode' => BaseEventElementWidget::$PATIENT_SUMMARY_MODE,
            )); ?>
        </div>
      </div>
        <?php $this->widget(\OEModule\OphCiExamination\widgets\HistoryMedications::class, array(
            'patient' => $this->patient,
            'mode' => BaseEventElementWidget::$PATIENT_SUMMARY_MODE,
        )); ?>


      <div class="group">
        <div class="label">Family</div>
        <div class="data">
            <?php $this->widget(\OEModule\OphCiExamination\widgets\FamilyHistory::class, array(
                'patient' => $this->patient,
                'mode' => BaseEventElementWidget::$PATIENT_SUMMARY_MODE,
            )); ?>
        </div>
      </div>
      <!-- group-->

      <div class="group">
        <div class="label">Social</div>
        <div class="data">
            <?php $this->widget(\OEModule\OphCiExamination\widgets\SocialHistory::class, array(
                'patient' => $this->patient,
                'mode' => BaseEventElementWidget::$PATIENT_SUMMARY_MODE,
            )); ?>
        </div>
      </div>
    </div><!-- 	.oe-popup-overflow -->





<?php
echo "<pr>".print_r("STEFAN2",1)."</pr>";




























$this->endContent();