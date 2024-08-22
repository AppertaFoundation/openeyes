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
    $jsPath = Yii::app()->getAssetManager()->publish(
        Yii::getPathOfAlias('application.assets.js') . '/PCRCalculation.js',
        true,
        -1
    );
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
<?php } ?>

<div class="sub-element-fields element" id="div_<?=\CHtml::modelName($element) ?>_pcr_risk">
  <div>
    <header class="element-header">
      <h4 class="element-title"> PCR Risk (<?php echo $side; ?>) </h4>
    </header>
  </div>
  <div class="cols-full flex-layout flex-top col-gap">
    <?php
        $patientId = Yii::app()->request->getParam('patient_id');

    if ($patientId == '') {
        $patientId = Yii::app()->request->getParam('patientId');
    }
    if ($patientId == '') {
        $patientId = $this->patient->id;
    }

    $grades = DoctorGrade::model()->findAll(array('order' => 'display_order'));
    $pcr_grades = [];

    foreach ($grades as $grade) {
        $pcr_grades['display'][$grade->id] = $grade->grade;
        $pcr_grades['risk'][$grade->id] = $grade->pcr_risk_value;
    }

        $default_display_label = "None selected.";

        $display_labels = array(
            'glaucoma' => array('NK' => 'Not Known', 'N' => 'No Glaucoma', 'Y' => 'Glaucoma present'),
            'diabetic' => array('NK' => 'Not Known', 'N' => 'No Diabetes', 'Y' => 'Diabetes present'),
            'noview' => array('NK' => 'Not Known', 'N' => 'No', 'Y' => 'Yes'),
            'brunescent_white_cataract' => array('NK' => 'Not Known', 'N' => 'No', 'Y' => 'Yes'),
            'doctor_grade_id' => $pcr_grades['display'],
            'pxf_phako' => array('NK' => 'Not Known', 'N' => 'No', 'Y' => 'Yes'),
            'pupil_size' => array('Large' => 'Large', 'Medium' => 'Medium', 'Small' => 'Small'),
            'axial_length_group' => array('0' => 'Not Known', '1' => '< 26', '2' => '> or = 26'),
            'arb' => array('NK' => 'Not Known', 'N' => 'No', 'Y' => 'Yes'),
            'lie_flat' => array('N' => 'No', 'Y' => 'Yes'),
        );

        if (isset($patientId)) :
            $pcrRisk = new PcrRisk();
            $pcr = $pcrRisk->getPCRData($patientId, $side, $element, $_POST);
            $pcr_doctor_grade = $pcr_grades['risk'][$pcr['doctor_grade_id']] ?? null;

            echo CHtml::hiddenField('age', $pcr['age_group'], array('id' => 'pcrrisk_' . $side . '_age', 'class' => 'pcrrisk_age'));
            echo CHtml::hiddenField('gender', $pcr['gender'], array('id' => 'pcrrisk_' . $side . '_gender', 'class' => 'pcrrisk_gender'));
            echo CHtml::hiddenField('PcrRisk[' . $side . '][glaucoma]', $pcr['glaucoma'], array('id' => 'pcrrisk_' . $side . '_glaucoma', 'class' => 'pcrrisk_glaucoma'));
            echo CHtml::hiddenField('PcrRisk[' . $side . '][diabetic]', $pcr['diabetic'], array('id' => 'pcrrisk_' . $side . '_diabetic', 'class' => 'pcrrisk_diabetic'));
            echo CHtml::hiddenField('PcrRisk[' . $side . '][no_fundal_view]', $pcr['noview'], array('id' => 'pcrrisk_' . $side . '_no_fundal_view', 'class' => 'pcrrisk_no_fundal_view'));
            echo CHtml::hiddenField('PcrRisk[' . $side . '][brunescent_white_cataract]', $pcr['anteriorsegment']['brunescent_white_cataract'], array('id' => 'pcrrisk_' . $side . '_brunescent_white_cataract', 'class' => 'pcrrisk_brunescent_white_cataract'));
            echo CHtml::hiddenField('PcrRisk[' . $side . '][doctor_grade_id]', $pcr['doctor_grade_id'], array('id' => 'pcrrisk_' . $side . '_doctor_grade_id', 'data-pcr-risk' => $pcr_doctor_grade, 'class' => 'pcr_doctor_grade'));
            echo CHtml::hiddenField('PcrRisk[' . $side . '][pxf_phako]', $pcr['anteriorsegment']['pxf_phako'], array('id' => 'pcrrisk_' . $side . '_pxf_phako', 'class' => 'pcrrisk_pxf_phako'));
            echo CHtml::hiddenField('PcrRisk[' . $side . '][pupil_size]', $pcr['anteriorsegment']['pupil_size'], array('id' => 'pcrrisk_' . $side . '_pupil_size', 'class' => 'pcrrisk_pupil_size'));
            echo CHtml::hiddenField('PcrRisk[' . $side . '][axial_length]', $pcr['axial_length_group'], array('id' => 'pcrrisk_' . $side . '_axial_length', 'class' => 'pcrrisk_axial_length'));
            echo CHtml::hiddenField('PcrRisk[' . $side . '][arb]', $pcr['arb'], array('id' => 'pcrrisk_' . $side . '_arb', 'class' => 'pcrrisk_arb'));
            echo CHtml::hiddenField('PcrRisk[' . $side . '][abletolieflat]', $pcr['lie_flat'], array('id' => 'pcrrisk_' . $side . '_abletolieflat', 'class' => 'pcrrisk_abletolieflat'));
            ?>
    <div id="<?= $side ?>_eye_pcr" class="cols-11">
      <div class="cols-full flex-layout flex-top col-gap">
        <div class="cols-6">
          <table class="last-left cols-full">
            <tbody>
            <tr>
              <td>Glaucoma</td>
              <td>
                <label id="<?= 'pcrrisk_' . $side . '_glaucoma_display'?>">
                    <?php
                    $value = $pcr['glaucoma'];
                    if (isset($value)) {
                            echo $display_labels['glaucoma'][$value];
                    } else {
                            echo $default_display_label;
                    } ?>
                </label>
              </td>
            </tr>
            <tr>
              <td>Diabetic</td>
              <td>
                <label id="<?= 'pcrrisk_' . $side . '_diabetic_display'?>">
                    <?php
                    $value = $pcr['diabetic'];
                    if (isset($value)) {
                            echo $display_labels['diabetic'][$value];
                    } else {
                            echo $default_display_label;
                    } ?>
                </label>
              </td>
            </tr>
            <tr>
              <td>Fundus Obscured</td>
              <td>
                <label id="<?= 'pcrrisk_' . $side . '_no_fundal_view_display'?>">
                    <?php
                    $value = $pcr['noview'];
                    if (isset($value)) {
                            echo $display_labels['noview'][$value];
                    } else {
                            echo $default_display_label;
                    } ?>
                </label>
              </td>
            </tr>
            <tr>
              <td>
                <label>
                  Brunescent/ White Cataract
                </label>
              </td>
              <td>
                <label id="<?= 'pcrrisk_' . $side . '_brunescent_white_cataract_display'?>">
                    <?php
                    $value = $pcr['anteriorsegment']['brunescent_white_cataract'];
                    if (isset($value)) {
                            echo $display_labels['brunescent_white_cataract'][$value];
                    } else {
                            echo $default_display_label;
                    } ?>
                </label>
              </td>
            </tr>
            <tr>
              <td>
                <label>
                  Surgeon Grade
                </label>
              </td>
              <td>
                <label id="<?= 'pcrrisk_' . $side . '_doctor_grade_id_display'?>" class="pcr_doctor_grade" >
                    <?php
                    $value = $pcr['doctor_grade_id'];
                    if (isset($value)) {
                            echo $display_labels['doctor_grade_id'][$value];
                    } else {
                            echo $default_display_label;
                    } ?>
                </label>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
        <div class="cols-6">
          <table class="last-left cols-full">
            <tbody>
            <tr>
              <td>
                <label>
                  PXF/ Phacodonesis
                </label>
              </td>
              <td>
                <label id="<?= 'pcrrisk_' . $side . '_pxf_phako_display'?>" class="<?= 'pcrrisk_pxf_phako' ?>">
                    <?php
                    $value = $pcr['anteriorsegment']['pxf_phako'];
                    if (isset($value)) {
                            echo $display_labels['pxf_phako'][$value];
                    } else {
                            echo $default_display_label;
                    } ?>
                </label>
              </td>
            </tr>
            <tr>
              <td>
                Pupil Size
              </td>
              <td>
                <label id="<?= 'pcrrisk_' . $side . '_pupil_size_display'?>" class="<?= 'pcrrisk_pupil_size' ?>">
                    <?php
                    $value = $pcr['anteriorsegment']['pupil_size'];
                    if (isset($value)) {
                            echo $display_labels['pupil_size'][$value];
                    } else {
                            echo $default_display_label;
                    } ?>
                </label>
              </td>
            </tr>
            <tr>
              <td>
                <label>
                  Axial Length (mm)
                </label>
              </td>
              <td>
                <label id="<?= 'pcrrisk_' . $side . '_axial_length_display'?>">
                    <?php
                    $value = $pcr['axial_length_group'];
                    if (isset($value)) {
                            echo $display_labels['axial_length_group'][$value];
                    } else {
                            echo $default_display_label;
                    } ?>
                </label>
              </td>
            </tr>
            <tr>
              <td>
                <label>
                  Alpha receptor blocker
                </label>
              </td>
              <td>
                <label id="<?= 'pcrrisk_' . $side . '_arb_display'?>">
                    <?php
                    $value = $pcr['arb'];
                    if (isset($value)) {
                            echo $display_labels['arb'][$value];
                    } else {
                            echo $default_display_label;
                    } ?>
                </label>
              </td>
            </tr>
            <tr>
              <td>
                <label>
                  Can lie flat
                </label>
              </td>
              <td>
                <label id="<?= 'pcrrisk_' . $side . '_abletolieflat_display'?>">
                    <?php
                    $value = $pcr['lie_flat'];
                    if (isset($value)) {
                            echo $display_labels['lie_flat'][$value];
                    } else {
                            echo $default_display_label;
                    } ?>
                </label>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="data-group-pad-top flex-layout">
        <div>
            Excess risk compared to average eye <span class="pcr-erisk highlighter"><strong><span> 3  </span></strong></span> times
          <a href="https://www.nature.com/articles/6703049"
             target="_blank">
            <i class="oe-i info small pad js-has-tooltip"
               data-tooltip-content="Calculation data derived from Narendran et al. The Cataract National Dataset electronic multicentre audit of 55,567 operations (click for more information)"></i>
          </a>
        </div>
        <div id="pcr-risk-div" class="highlighter large-text">
            PCR Risk <span class="pcr-span"> 6.1 </span> %
        </div>
      </div>
    </div>
    <div class="add-data-actions flex-item-bottom " id="add-pcr-risk-popup-<?= $side ?>">
      <button class="button hint green js-add-select-search" id="add-pcr-risk-btn-<?= $side ?>" type="button" data-test="add-pcr-risk-btn">
        <i class="oe-i plus pro-theme"></i>
      </button><!-- popup to add data to element -->
    </div>
  </div>
  <script type="text/javascript">
    $(document).ready(function () {

      function selectItemsetOption(options, id) {
        options.forEach((option) => {
          //id will always be a string, option.id may be a number so we convert it and avoid automatic type coercion
          if (option.id.toString() === id) {
            option.selected = true;
          }
        });
        return options;
      }

      var drop_glaucoma = selectItemsetOption([
          {'id':'NK', 'label':'Not Known'},
          {'id':'N', 'label':'No Glaucoma'},
          {'id':'Y', 'label':'Glaucoma present'}
        ], '<?= $pcr['glaucoma'] ?>'),
        drop_diabetic = selectItemsetOption([
          {'id':'NK', 'label':'Not Known'},
          {'id':'N', 'label':'No Diabetes'},
          {'id':'Y', 'label':'Diabetes present'}
        ], '<?= $pcr['diabetic'] ?>'),
        drop_lie_flat = selectItemsetOption([
          {'id':'N', 'label':'No'},
          {'id':'Y', 'label':'Yes'}
        ], '<?= $pcr['lie_flat'] ?>'),
        drop_axial_length = selectItemsetOption([
          {'id': 0, 'label':'Not Known'},
          {'id': 1, 'label':'< 26'},
          {'id': 2, 'label':'> or = 26'}
        ], '<?= $pcr['axial_length_group'] ?>'),
        drop_fundus = selectItemsetOption([
          {'id':'NK', 'label':'Not Known'},
          {'id':'N', 'label':'No'},
          {'id':'Y', 'label':'Yes'}
        ], '<?= $pcr['noview'] ?>'),
        drop_brunescent = selectItemsetOption([
          {'id':'NK', 'label':'Not Known'},
          {'id':'N', 'label':'No'},
          {'id':'Y', 'label':'Yes'}
        ], '<?= $pcr['anteriorsegment']['brunescent_white_cataract'] ?>'),
        drop_arb = selectItemsetOption([
          {'id':'NK', 'label':'Not Known'},
          {'id':'N', 'label':'No'},
          {'id':'Y', 'label':'Yes'}
        ], '<?= $pcr['arb'] ?>');

            <?php
            $grade_to_risk = array();
            foreach ($grades as $grade) {
                $grade_to_risk[$grade->id] = $grade->pcr_risk_value;
            }
            ?>

      let doctor_risks = <?= json_encode($grade_to_risk) ?>;

      new OpenEyes.UI.AdderDialog({
        openButton: $('#add-pcr-risk-btn-<?= $side ?>'),
        itemSets: [
          new OpenEyes.UI.AdderDialog.ItemSet(
            drop_glaucoma, {'header':'Glaucoma', 'id':'glaucoma'}
          ),
          new OpenEyes.UI.AdderDialog.ItemSet(
            drop_diabetic, {'header':'Diabetic', 'id':'diabetic'}
          ),
          new OpenEyes.UI.AdderDialog.ItemSet(
            drop_fundus, {'header':'Fundus Obscured', 'id':'no_fundal_view'}),
          new OpenEyes.UI.AdderDialog.ItemSet(
            drop_brunescent, {'header':'Brunescent/ White Cataract', 'id':'brunescent_white_cataract'}
          ),
          new OpenEyes.UI.AdderDialog.ItemSet(
            drop_axial_length, {'header':'Axial Length (mm)', 'id':'axial_length'}
          ),
          new OpenEyes.UI.AdderDialog.ItemSet(
            drop_arb, {'header':'Alpha receptor blocker', 'id':'arb'}
          ),
          new OpenEyes.UI.AdderDialog.ItemSet(
            drop_lie_flat, {'header':'Can lie flat', 'id':'abletolieflat'}
          )
        ],
        deselectOnReturn: false,
        onReturn: function(adderDialog, selectedItems) {
          for (i in selectedItems) {
            var label = selectedItems[i]['itemSet'].options['id'];
            var displayLabel = selectedItems[i]['label'];
            var id = selectedItems[i]['id'];
            var $dataselector = $('#pcrrisk_<?= $side ?>_'+label);
            var $displayselector = $('#pcrrisk_<?= $side ?>_'+label+'_display');

            $dataselector.val(id);

            if(label === "doctor_grade_id") {
              $dataselector.attr("data-pcr-risk", doctor_risks[id]);
            }

            $dataselector.trigger('change');
            $displayselector.text(displayLabel);
          }
          return true;
        }
      });
    });
  </script>
        <?php endif; ?>
</div>
