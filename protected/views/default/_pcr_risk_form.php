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
if (!isset($side)) {
    $side = 'left';
}
if ($side === 'left') {
    $jsPath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.assets.js') . '/PCRCalculation.js',
        false, -1);
    ?>
  <script type="text/javascript">

    function callCalculate() {

      if ($('#Element_OphTrOperationnote_ProcedureList_eye_id_2').length == 0 && $('#Element_OphTrOperationnote_ProcedureList_eye_id_2').length == 0) {
        pcrCalculate($('#ophCiExaminationPCRRiskLeftEye'), 'left');
        pcrCalculate($('#ophCiExaminationPCRRiskRightEye'), 'right');
      } else {
        if ($('#Element_OphTrOperationnote_ProcedureList_eye_id_2').is(':checked')) {
          pcrCalculate($('#ophCiExaminationPCRRiskRightEye'), 'right');
        }

        if ($('#Element_OphTrOperationnote_ProcedureList_eye_id_1').is(':checked')) {
          pcrCalculate($('#ophCiExaminationPCRRiskLeftEye'), 'left');
        }
      }

    }

    $.getScript('<?=$jsPath?>', function () {
      //Map the elements
      mapExaminationToPcr();
      //Make the initial calculations
      pcrCalculate($('#ophCiExaminationPCRRiskLeftEye'), 'left');
      pcrCalculate($('#ophCiExaminationPCRRiskRightEye'), 'right');

      $(document.body).on('change', '#ophCiExaminationPCRRiskLeftEye, #ophCiExaminationPCRRiskRightEye', function () {
        callCalculate();

      });

      callCalculate();

    });
  </script>
    <?php

}
$criteria = new CDbCriteria();
?>

<div class="sub-element-fields element" id="div_<?php echo CHtml::modelName($element) ?>_pcr_risk">
  <div>
    <header class="element-header">
      <h4 class="element-title"> PCR Risk (<?php echo $side; ?>) </h4>
    </header>
  </div>
  <div class="cols-11 flex-layout flex-top col-gap">

      <?php
      $patientId = Yii::app()->request->getParam('patient_id');

      if ($patientId == '') {
          $patientId = Yii::app()->request->getParam('patientId');
      }
      if ($patientId == '') {
          $patientId = $this->patient->id;
      }

      if (isset($patientId)):
      $pcrRisk = new PcrRisk();
      $pcr = $pcrRisk->getPCRData($patientId, $side, $element);
      echo CHtml::hiddenField('age', $pcr['age_group']);
      echo CHtml::hiddenField('gender', $pcr['gender']);
      ?>
    <div id="left_eye_pcr">
      <div class="cols-11 flex-layout flex-top col-gap">
        <div class="cols-6">
          <table class="last-left">
            <tbody>
            <tr>
              <td>Glaucoma</td>
              <td>
                  <?php
                  echo CHtml::dropDownList('PcrRisk[' . $side . '][glaucoma]', $pcr['glaucoma'],
                      array('NK' => 'Not Known', 'N' => 'No Glaucoma', 'Y' => 'Glaucoma present'),
                      array('id' => 'pcrrisk_' . $side . '_glaucoma', 'class' => 'pcrrisk_glaucoma'));
                  ?>
              </td>
            </tr>
            <tr>
              <td>Diabetic</td>
              <td>
                  <?php
                  echo CHtml::dropDownList('PcrRisk[' . $side . '][diabetic]', $pcr['diabetic'],
                      array('NK' => 'Not Known', 'N' => 'No Diabetes', 'Y' => 'Diabetes present'),
                      array('id' => 'pcrrisk_' . $side . '_diabetic', 'class' => 'pcrrisk_diabetic')
                  );
                  ?>
              </td>
            </tr>
            <tr>
              <td>Fundus Obscured</td>
              <td>
                  <?php
                  echo CHtml::dropDownList('PcrRisk[' . $side . '][no_fundal_view]', $pcr['noview'],
                      array('NK' => 'Not Known', 'N' => 'No', 'Y' => 'Yes'),
                      array('id' => 'pcrrisk_' . $side . '_no_fundal_view', 'class' => 'pcrrisk_no_fundal_view'));
                  ?>
              </td>
            </tr>
            <tr>
              <td>
                <label>
                  Brunescent/ White Cataract
                </label>
              </td>
              <td>
                  <?php
                  echo CHtml::dropDownList('PcrRisk[' . $side . '][brunescent_white_cataract]',
                      $pcr['anteriorsegment']['brunescent_white_cataract'],
                      array('NK' => 'Not Known', 'N' => 'No', 'Y' => 'Yes'),
                      array(
                          'id' => 'pcrrisk_' . $side . '_brunescent_white_cataract',
                          'class' => 'pcrrisk_brunescent_white_cataract',
                      ));
                  ?>
              </td>
            </tr>
            <tr>
              <td>
                <label>
                  Surgeon Grade
                </label>
              </td>
              <td>
                  <?php $grades = DoctorGrade::model()->findAll($criteria->condition = 'has_pcr_risk',
                      array('order' => 'display_order')); ?>
                <select id="<?= 'pcrrisk_' . $side . '_doctor_grade_id' ?>" class="pcr_doctor_grade"
                        name="PcrRisk[<?= $side ?>][pcr_doctor_grade]">
                    <?php if (is_array($grades)): ?>
                        <?php foreach ($grades as $grade): ?>
                        <option value="<?= $grade->id ?>"
                                data-pcr-value="<?= $grade->pcr_risk_value ?>"><?= $grade->grade ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
        <div class="cols-6">
          <table class="last-left">
            <tbody>
            <tr>
              <td>
                <label>
                  PXF/ Phacodonesis
                </label>
              </td>
              <td>
                  <?php
                  echo CHtml::dropDownList('PcrRisk[' . $side . '][pxf_phako]', $pcr['anteriorsegment']['pxf_phako'],
                      array('NK' => 'Not Known', 'N' => 'No', 'Y' => 'Yes'),
                      array('id' => 'pcrrisk_' . $side . '_pxf_phako', 'class' => 'pcrrisk_pxf_phako'));
                  ?>
              </td>
            </tr>
            <tr>
              <td>
                Pupil Size
              </td>
              <td>
                  <?php
                  if (trim($pcr['anteriorsegment']['pupil_size']) == '') {
                      $pcr['anteriorsegment']['pupil_size'] = 'Medium';
                  }
                  echo CHtml::dropDownList('PcrRisk[' . $side . '][pupil_size]', $pcr['anteriorsegment']['pupil_size'],
                      array('Large' => 'Large', 'Medium' => 'Medium', 'Small' => 'Small'),
                      array('id' => 'pcrrisk_' . $side . '_pupil_size', 'class' => 'pcrrisk_pupil_size'));
                  ?>
              </td>
            </tr>
            <tr>
              <td>
                <label>
                  Axial Length (mm)
                </label>
              </td>
              <td>
                  <?php
                  echo CHtml::dropDownList('PcrRisk[' . $side . '][axial_length]', $pcr['axial_length_group'],
                      array('NK' => 'Not Known', 1 => '< 26', 2 => '> or = 26'),
                      array('id' => 'pcrrisk_' . $side . '_axial_length', 'class' => 'pcrrisk_axial_length'));
                  ?>
              </td>
            </tr>
            <tr>
              <td>
                <label>
                  Alpha receptor blocker
                </label>
              </td>
              <td>
                  <?php
                  echo CHtml::dropDownList('PcrRisk[' . $side . '][arb]', $pcr['arb'],
                      array('NK' => 'Not Known', 'N' => 'No', 'Y' => 'Yes'),
                      array('id' => 'pcrrisk_' . $side . '_arb', 'class' => 'pcrrisk_arb')
                  );
                  ?>
              </td>
            </tr>
            <tr>
              <td>
                <label>
                  Can lie flat
                </label>
              </td>
              <td>
                  <?php
                  echo CHtml::dropDownList('PcrRisk[' . $side . '][abletolieflat]', $pcr['lie_flat'],
                      array('N' => 'No', 'Y' => 'Yes'),
                      array(
                          'id' => 'pcrrisk_' . $side . '_abletolieflat',
                          'class' => 'pcrrisk_lie_flat',
                      ));
                  ?>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="cols-full">
        <span id="pcr-risk-div">
          <label>
            PCR Risk <span class="pcr-span"> 6.1 </span> %
          </label>
        </span>
        <div>
          <label>
            Excess risk compared to average eye <span class="pcr-erisk"><strong><span> 3  </span></strong></span> times
          </label>
        </div>
        <label>
          Calculation data derived from
          <a href="http://www.researchgate.net/publication/5525424_The_Cataract_National_Dataset_electronic_multicentre_audit_of_55_567_operations_Risk_stratification_for_posterior_capsule_rupture_and_vitreous_loss"
             target="_blank">
            Narendran et al. The Cataract National Dataset electronic multicentre audit of 55,567 operations
          </a>
        </label>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>
</div>
