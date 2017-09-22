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
    $jsPath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.assets.js'), false, -1);
    ?>
    <script type="text/javascript">

        function callCalculate(){

            if( $('#Element_OphTrOperationnote_ProcedureList_eye_id_2').length == 0 && $('#Element_OphTrOperationnote_ProcedureList_eye_id_2').length == 0 ){
                pcrCalculate($('#ophCiExaminationPCRRiskLeftEye'), 'left');
                pcrCalculate($('#ophCiExaminationPCRRiskRightEye'), 'right');
            } else {
                if( $('#Element_OphTrOperationnote_ProcedureList_eye_id_2').is(':checked') ){
                    pcrCalculate($('#ophCiExaminationPCRRiskRightEye'), 'right');
                }

                if( $('#Element_OphTrOperationnote_ProcedureList_eye_id_1').is(':checked') ){
                    pcrCalculate($('#ophCiExaminationPCRRiskLeftEye'), 'left');
                }
            }

        }

        $.getScript('<?=$jsPath?>/PCRCalculation.js', function(){
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

<div class="sub-element-fields" id="div_<?php echo CHtml::modelName($element) ?>_pcr_risk">
    <div>
        <header class="sub-element-header">
            <h4 class="sub-element-title"> PCR Risk (<?php echo $side; ?>) </h4>
        </header>
        <div class="row field-row"></div>
    </div>
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
            <div class="row field-row">
                <div class="large-2 column">
                    <label>
                        Glaucoma
                    </label>
                </div>
                <div class="large-2 column">
                    <?php
                    echo CHtml::dropDownList('PcrRisk[' . $side . '][glaucoma]', $pcr['glaucoma'],
                        array('NK' => 'Not Known', 'N' => 'No Glaucoma', 'Y' => 'Glaucoma present'),
                        array('id' => 'pcrrisk_' . $side . '_glaucoma', 'class' => 'pcrrisk_glaucoma'));
                    ?>
                </div>
                <div class="large-2 column pcr-nkr">
                    &nbsp;
                </div>
                <div class="large-2 column">
                    <label>
                        PXF/ Phacodonesis
                    </label>
                </div>
                <div class="large-2 column">
                    <?php
                    echo CHtml::dropDownList('PcrRisk[' . $side . '][pxf_phako]', $pcr['anteriorsegment']['pxf_phako'], array('NK' => 'Not Known', 'N' => 'No', 'Y' => 'Yes'),
                        array('id' => 'pcrrisk_' . $side . '_pxf_phako', 'class' => 'pcrrisk_pxf_phako'));
                    ?>
                </div>
                <div class="large-2 column pcr-nkr">
                    &nbsp;
                </div>
            </div>
            <div class="row field-row">
                <div class="large-2 column">
                    <label>
                        Diabetic
                    </label>
                </div>
                <div class="large-2 column">
                    <?php
                    echo CHtml::dropDownList('PcrRisk[' . $side . '][diabetic]', $pcr['diabetic'],
                        array('NK' => 'Not Known', 'N' => 'No Diabetes', 'Y' => 'Diabetes present'),
                        array('id' => 'pcrrisk_' . $side . '_diabetic', 'class' => 'pcrrisk_diabetic')
                    );
                    ?>
                </div>
                <div class="large-2 column pcr-nkr">
                    &nbsp;
                </div>
                <div class="large-2 column">
                    <label>
                        Pupil Size
                    </label>
                </div>

                <div class="large-2 column">
                    <?php
                    if (trim($pcr['anteriorsegment']['pupil_size']) == '') {
                        $pcr['anteriorsegment']['pupil_size'] = 'Medium';
                    }
                    echo CHtml::dropDownList('PcrRisk[' . $side . '][pupil_size]', $pcr['anteriorsegment']['pupil_size'],
                        array('Large' => 'Large', 'Medium' => 'Medium', 'Small' => 'Small'),
                        array('id' => 'pcrrisk_' . $side . '_pupil_size', 'class' => 'pcrrisk_pupil_size'));
                    ?>
                </div>
                <div class="large-2 column pcr-nkr">
                    &nbsp;
                </div>
            </div>

            <div class="row field-row">
                <div class="large-2 column">
                    <label>
                        Fundus Obscured
                    </label>
                </div>
                <div class="large-2 column">
                    <?php
                    echo CHtml::dropDownList('PcrRisk[' . $side . '][no_fundal_view]', $pcr['noview'],
                        array('NK' => 'Not Known', 'N' => 'No', 'Y' => 'Yes'),
                        array('id' => 'pcrrisk_' . $side . '_no_fundal_view', 'class' => 'pcrrisk_no_fundal_view'));
                    ?>
                </div>
                <div class="large-2 column pcr-nkr">
                    &nbsp;
                </div>
                <div class="large-2 column">
                    <label>
                        Axial Length (mm)
                    </label>
                </div>
                <div class="large-2 column">
                    <?php
                    echo CHtml::dropDownList('PcrRisk[' . $side . '][axial_length]', $pcr['axial_length_group'],
                        array('NK' => 'Not Known', 1 => '< 26', 2 => '> or = 26'),
                        array('id' => 'pcrrisk_' . $side . '_axial_length', 'class' => 'pcrrisk_axial_length'));
                    ?>
                </div>
                <div class="large-2 column pcr-nkr">
                    &nbsp;
                </div>
            </div>

            <div class="row field-row">
                <div class="large-2 column">
                    <label>
                        Brunescent/ White Cataract
                    </label>
                </div>
                <div class="large-2 column">
                    <?php
                    echo CHtml::dropDownList('PcrRisk[' . $side . '][brunescent_white_cataract]', $pcr['anteriorsegment']['brunescent_white_cataract'],
                        array('NK' => 'Not Known', 'N' => 'No', 'Y' => 'Yes'),
                        array('id' => 'pcrrisk_' . $side . '_brunescent_white_cataract', 'class' => 'pcrrisk_brunescent_white_cataract'));
                    ?>
                </div>
                <div class="large-2 column pcr-nkr">
                    &nbsp;
                </div>
                <div class="large-2 column">
                    <label>
                        Alpha receptor blocker
                    </label>
                </div>
                <div class="large-2 column">
                    <?php
                    echo CHtml::dropDownList('PcrRisk[' . $side . '][arb]', $pcr['arb'],
                        array('NK' => 'Not Known', 'N' => 'No', 'Y' => 'Yes'),
                        array('id' => 'pcrrisk_' . $side . '_arb', 'class' => 'pcrrisk_arb')
                    );
                    ?>
                </div>
                <div class="large-2 column pcr-nkr">
                </div>
            </div>

            <div class="row field-row">
                <div class="large-2 column">
                    <label>
                        Surgeon Grade
                    </label>
                </div>
                <div class="large-2 column">

                    <?php $grades = DoctorGrade::model()->findAll($criteria->condition = 'has_pcr_risk', array('order' => 'display_order'));?>
                    <select id="<?='pcrrisk_'.$side.'_doctor_grade_id'?>" class="pcr_doctor_grade" name="PcrRisk[<?= $side ?>][pcr_doctor_grade]">
                        <?php if(is_array($grades)):?>
                            <?php foreach ($grades as $grade):?>
                                <option value="<?=$grade->id?>" data-pcr-value="<?=$grade->pcr_risk_value?>"><?=$grade->grade?></option>
                            <?php endforeach;?>
                        <?php endif;?>
                    </select>
                </div>
                <div class="large-2 column pcr-nkr">
                    &nbsp;
                </div>
                <div class="large-2 column">
                    <label>
                        Can lie flat
                    </label>
                </div>
                <div class="large-2 column">
                    <?php
                    echo CHtml::dropDownList('PcrRisk[' . $side . '][abletolieflat]', $pcr['lie_flat'], array('N' => 'No', 'Y' => 'Yes'),
                        array(
                            'id' => 'pcrrisk_' . $side . '_abletolieflat',
                            'class' => 'pcrrisk_lie_flat',
                        ));
                    ?>
                </div>
                <div class="large-2 column pcr-nkr">
                    &nbsp;
                </div>
            </div>

            <div class="row field-row">
                <div class="large-1 column">
                    &nbsp;
                </div>
                <div class="large-2 column" id="pcr-risk-div">
                    <label>
                        PCR Risk <span class="pcr-span"> 6.1 </span> %
                    </label>
                </div>
                <div class="large-3 column">
                    &nbsp;
                </div>

                <div class="large-6 column">
                    <label>
                        Excess risk compared to average eye <span class="pcr-erisk"> <strong>
							<span> 3  </span></strong> </span> times
                    </label>
                </div>
            </div>
            <div class="large-8 column pcr-risk-data-link">
                <label>
                    Calculation data derived from
                    <a href="http://www.researchgate.net/publication/5525424_The_Cataract_National_Dataset_electronic_multicentre_audit_of_55_567_operations_Risk_stratification_for_posterior_capsule_rupture_and_vitreous_loss"
                       target="_blank">
                        Narendran et al. The Cataract National Dataset electronic multicentre audit of 55,567 operations
                    </a>

                </label>
            </div>
        </div>
    <?php endif; ?>
</div>
