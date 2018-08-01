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
    <?php }
    $criteria = new CDbCriteria(); ?>

<div class="sub-element-fields element" id="div_<?php echo CHtml::modelName($element) ?>_pcr_risk">
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

      if (isset($patientId)):
      $pcrRisk = new PcrRisk();
      $pcr = $pcrRisk->getPCRData($patientId, $side, $element, $_POST);
      echo CHtml::hiddenField('age', $pcr['age_group']);
      echo CHtml::hiddenField('gender', $pcr['gender']);
      ?>
    <div id="<?= $side ?>_eye_pcr" class="cols-11">
      <div class="cols-full flex-layout flex-top col-gap">
        <div class="cols-6">
          <table class="last-left cols-full">
            <tbody>
            <tr>
              <td>Glaucoma</td>
              <td>
                  <?php
                  echo CHtml::dropDownList('PcrRisk[' . $side . '][glaucoma]', $pcr['glaucoma'],
                      array('NK' => 'Not Known', 'N' => 'No Glaucoma', 'Y' => 'Glaucoma present'),
                      array('id' => 'pcrrisk_' . $side . '_glaucoma', 'class' => 'pcrrisk_glaucoma cols-full'));
                  ?>
              </td>
            </tr>
            <tr>
              <td>Diabetic</td>
              <td>
                  <?php
                  echo CHtml::dropDownList('PcrRisk[' . $side . '][diabetic]', $pcr['diabetic'],
                      array('NK' => 'Not Known', 'N' => 'No Diabetes', 'Y' => 'Diabetes present'),
                      array('id' => 'pcrrisk_' . $side . '_diabetic', 'class' => 'pcrrisk_diabetic cols-full')
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
                      array(
                          'id' => 'pcrrisk_' . $side . '_no_fundal_view',
                          'class' => 'pcrrisk_no_fundal_view cols-full',
                      ));
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
                          'class' => 'pcrrisk_brunescent_white_cataract cols-full',
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
                  <?php
                      $grades = DoctorGrade::model()->findAll($criteria->condition = 'has_pcr_risk', array('order' => 'display_order'));
                      $pcr_data_attributes = [];
                      foreach ($grades as $grade) {
                          $pcr_data_attributes[$grade->id] = ['data-pcr-value' => $grade->pcr_risk_value];
                      }

                      echo CHtml::dropDownList("PcrRisk[$side][pcr_doctor_grade]", $pcr['doctor_grade_id'],
                          CHtml::listData($grades, 'id', 'grade'),['id' => "pcrrisk_{$side}_doctor_grade_id",  'options' => $pcr_data_attributes ]);
                  ?>
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
                  <?php
                  echo CHtml::dropDownList('PcrRisk[' . $side . '][pxf_phako]', $pcr['anteriorsegment']['pxf_phako'],
                      array('NK' => 'Not Known', 'N' => 'No', 'Y' => 'Yes'),
                      array('id' => 'pcrrisk_' . $side . '_pxf_phako', 'class' => 'pcrrisk_pxf_phako cols-full'));
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
                      array('id' => 'pcrrisk_' . $side . '_pupil_size', 'class' => 'pcrrisk_pupil_size cols-full'));
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
                      array('id' => 'pcrrisk_' . $side . '_axial_length', 'class' => 'pcrrisk_axial_length cols-full'));
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
                      array('id' => 'pcrrisk_' . $side . '_arb', 'class' => 'pcrrisk_arb cols-full')
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
                          'class' => 'pcrrisk_lie_flat cols-full',
                      ));
                  ?>
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
      <button class="button hint green js-add-select-search" id="add-pcr-risk-btn-<?= $side ?>" type="button">
        <i class="oe-i plus pro-theme"></i>
      </button><!-- popup to add data to element -->
    </div>
  </div>
    <?php endif; ?>
</div>

<script type="text/javascript">
  $(document).ready(function () {
    var drop_glaucoma = [
      {'id':'NK', 'value':'Not Known', 'option-label':'glaucoma'},
        {'id':'N', 'value':'No Glaucoma', 'option-label':'glaucoma'},
        {'id':'Y', 'value':'Glaucoma present', 'option-label':'glaucoma'}
        ],
      drop_diabetic = [
        {'id':'NK', 'value':'Not Known', 'option-label':'diabetic'},
        {'id':'N', 'value':'No Diabetes', 'option-label':'diabetic'},
        {'id':'Y', 'value':'Diabetes present', 'option-label':'diabetic'}
      ],
      drop_lie_flat = [
        {'id':'N', 'value':'No', 'option-label':'abletolieflat'},
        {'id':'Y', 'value':'Yes', 'option-label':'abletolieflat'}
        ],
      drop_axial_length = [
        {'id':'NK', 'value':'Not Known', 'option-label':'axial_length'},
        {'id': 1, 'value':'< 26', 'option-label':'axial_length'},
        {'id': 2, 'value':'> or = 26', 'option-label':'axial_length'}
        ],
      drop_pupil_size = [
        {'id':'Large', 'value':'Large', 'option-label':'pupil_size' },
        {'id':'Medium', 'value':'Medium', 'option-label':'pupil_size'},
        {'id':'Small', 'value':'Small', 'option-label':'pupil_size'}
        ],
      drop_item1 = [
        {'id':'NK', 'value':'Not Known'},
        {'id':'N', 'value':'No'},
        {'id':'Y', 'value':'Yes'}
      ];
    var drop_fundus = JSON.parse(JSON.stringify(drop_item1));
    var drop_brunescent = JSON.parse(JSON.stringify(drop_item1));
    var drop_pxf = JSON.parse(JSON.stringify(drop_item1));
    var drop_arb = JSON.parse(JSON.stringify(drop_item1));

    new OpenEyes.UI.AdderDialog({
      openButton: $('#add-pcr-risk-btn-<?= $side ?>'),
      itemSets: [
        new OpenEyes.UI.AdderDialog.ItemSet(
          drop_glaucoma, {'header':'Glaucoma'}
        ),
        new OpenEyes.UI.AdderDialog.ItemSet(
          drop_diabetic, {'header':'Diabetic'}
        ),
        new OpenEyes.UI.AdderDialog.ItemSet(
          drop_fundus.map(x=>{x['option-label']='no_fundal_view'; return x}), {'header':'Fundus Obscured'}
        ),
        new OpenEyes.UI.AdderDialog.ItemSet(
          drop_brunescent.map(x=>{x['option-label']='brunescent_white_cataract'; return x}), {'header':'Brunescent/ White Cataract'}
        ),
        new OpenEyes.UI.AdderDialog.ItemSet(
            <?= CJSON::encode(
                array_map(function ($item) {
                    return ['value' =>$item->grade,
                        'risk-value'=>$item->pcr_risk_value,
                        'id' => $item->id,
                        'option-label'=>'_doctor_grade_id'];
                }, $grades) ) ?>, {'header':'Surgeon Grade'}
        ),
        new OpenEyes.UI.AdderDialog.ItemSet(
          drop_pxf.map(x=>{x['option-label'] = 'pxf_phako'; return x}), {'header':'PXF/ Phacodonesis'}
        ),
        new OpenEyes.UI.AdderDialog.ItemSet(
          drop_pupil_size, {'header':'Pupil Size'}
        ),
        new OpenEyes.UI.AdderDialog.ItemSet(
          drop_axial_length, {'header':'Axial Length (mm)'}
        ),
        new OpenEyes.UI.AdderDialog.ItemSet(
          drop_arb.map(x=>{x['option-label'] = 'arb'; return x}), {'header':'Alpha receptor blocker '}
        ),
        new OpenEyes.UI.AdderDialog.ItemSet(
          drop_lie_flat, {'header':'Alpha receptor blocker '}
        )
      ],
      onReturn: function(adderDialog, selectedItems) {
        for (i in selectedItems) {
          var label = selectedItems[i]['optionLabel'];
          var id = selectedItems[i]['id'];
          var $selector = $('#pcrrisk_<?= $side ?>_'+label);
          $selector.val(id);
          $selector.trigger('change');
        }
        return true;
      }
    });
  });
</script>
