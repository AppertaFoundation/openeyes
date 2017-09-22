<?php
$jsPath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.assets.js') . '/PCRCalculation.js', false, -1);
?>
<script type="text/javascript">
    $.getScript('<?=$jsPath?>', function(){
        //Map the elements
        mapExaminationToPcr();
        //Make the initial calculations
        var $pcrRiskEl = $('section.OEModule_OphCiExamination_models_Element_OphCiExamination_PcrRisk');
        pcrCalculate($pcrRiskEl.find('.left-eye'), 'left');
        pcrCalculate($pcrRiskEl.find('.right-eye'), 'right');

        $(document.body).on('change', $pcrRiskEl.find('.left-eye'), function () {
            pcrCalculate($pcrRiskEl.find('.left-eye'), 'left');
        });

        $(document.body).on('change', $pcrRiskEl.find('.right-eye'), function () {
            pcrCalculate($pcrRiskEl.find('.right-eye'), 'right');
        });

        $("#OEModule_OphCiExamination_models_Element_OphCiExamination_PcrRisk_right_diabetic").change(function () {
            var $pcrDiabeticRight = $("#OEModule_OphCiExamination_models_Element_OphCiExamination_PcrRisk_right_diabetic").prop('selectedIndex');
            $("select#OEModule_OphCiExamination_models_Element_OphCiExamination_PcrRisk_left_diabetic").prop('selectedIndex', $pcrDiabeticRight);
        });

        $("#OEModule_OphCiExamination_models_Element_OphCiExamination_PcrRisk_left_diabetic").change(function () {
            var $pcrDiabeticLeft = $("#OEModule_OphCiExamination_models_Element_OphCiExamination_PcrRisk_left_diabetic").prop('selectedIndex');
            $("select#OEModule_OphCiExamination_models_Element_OphCiExamination_PcrRisk_right_diabetic").prop('selectedIndex', $pcrDiabeticLeft);
        });

        $("#OEModule_OphCiExamination_models_Element_OphCiExamination_PcrRisk_right_alpha_receptor_blocker").change(function () {
            var $pcrAlphaRight = $("#OEModule_OphCiExamination_models_Element_OphCiExamination_PcrRisk_right_alpha_receptor_blocker").prop('selectedIndex');
            $("select#OEModule_OphCiExamination_models_Element_OphCiExamination_PcrRisk_left_alpha_receptor_blocker").prop('selectedIndex', $pcrAlphaRight);
        });

        $("#OEModule_OphCiExamination_models_Element_OphCiExamination_PcrRisk_left_alpha_receptor_blocker").change(function () {
            var $pcrAlphaLeft = $("#OEModule_OphCiExamination_models_Element_OphCiExamination_PcrRisk_left_alpha_receptor_blocker").prop('selectedIndex');
            $("select#OEModule_OphCiExamination_models_Element_OphCiExamination_PcrRisk_right_alpha_receptor_blocker").prop('selectedIndex', $pcrAlphaLeft);
        });

    });
</script>
<div class="element-eyes element-fields">
<?php
if ($this->patient->getDiabetes()) {
    $diabeticOptions = array('Y' => 'Diabetes present');
}else{
    $diabeticOptions = array('NK' => 'Not Known', 'N' => 'No Diabetes', 'Y' => 'Diabetes present');
}


$criteria = new CDbCriteria();
$criteria->condition = 'has_pcr_risk';
$grades = \DoctorGrade::model()->findAll($criteria, array('order' => 'display_order'));
$dropDowns = array(
    'glaucoma' => array(
        'options' => array('NK' => 'Not Known', 'N' => 'No Glaucoma', 'Y' => 'Glaucoma present'),
        'class' => 'pcrrisk_glaucoma',
    ),
    'pxf' => array(
        'options' => array('NK' => 'Not Known', 'N' => 'No', 'Y' => 'Yes'),
        'class' => 'pcrrisk_pxf_phako',
    ),
    'diabetic' => array(
        'options' => $diabeticOptions,
        'class' => 'pcrrisk_diabetic',
    ),
    'pupil_size' => array(
        'options' => array('Large' => 'Large', 'Medium' => 'Medium', 'Small' => 'Small'),
        'class' => 'pcrrisk_pupil_size',
    ),
    'no_fundal_view' => array(
        'options' => array('NK' => 'Not Known', 'N' => 'No', 'Y' => 'Yes'),
        'class' => 'pcrrisk_no_fundal_view',
    ),
    'axial_length_group' => array(
        'options' => array(0 => 'Not Known', 1 => '< 26', 2 => '> or = 26'),
        'class' => '',
    ),
    'brunescent_white_cataract' => array(
        'options' => array('NK' => 'Not Known', 'N' => 'No', 'Y' => 'Yes'),
        'class' => 'pcrrisk_brunescent_white_cataract',
    ),
    'alpha_receptor_blocker' => array(
        'options' => array('NK' => 'Not Known', 'N' => 'No', 'Y' => 'Yes'),
        'class' => 'pcrrisk_arb',
    ),
    'doctor_grade_id' => array(
        'options' => $grades,
        'class' => 'pcr_doctor_grade',
    ),
    'can_lie_flat' => array(
        'options' => array('N' => 'No', 'Y' => 'Yes'),
        'class' => 'pcr_lie_flat',
    ),
);
echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField'));

foreach (array('right', 'left') as $side):
    $opposite = ($side === 'right') ? 'left' : 'right';
    $pcrRisk = new PcrRisk();
    $activeClass = ($element->{'has'.ucfirst($side)}()) ? 'active' : 'inactive'; ?>
    <div class="element-eye <?=$side?>-eye column <?=$opposite?> side <?=$activeClass?>" data-side="<?=$side?>">
        <?php
        if($this->event){
            $patientId = $this->event->episode->patient->id;
        } else {
            $patientId = Yii::app()->request->getQuery('patient_id');
        }

        $pcr = $pcrRisk->getPCRData($patientId, $side, $element);
        echo CHtml::hiddenField('age', $pcr['age_group']);
        echo CHtml::hiddenField('gender', $pcr['gender']);
        ?>
        <div class="active-form">
            <a href="#" class="icon-remove-side remove-side">Remove side</a>
            <?php foreach ($dropDowns as $key => $data):
                if ($key === 'doctor_grade_id'):?>
                    <div id="div_OEModule_OphCiExamination_models_Element_OphCiExamination_PcrRisk_right_pcr_doctor_grade" class="row field-row">
                        <div class="large-4 column">
                            <label for="<?='pcrrisk_'.$side.'_doctor_grade_id'?>">Surgeon Grade:</label>
                        </div>
                        <div class="large-4 column end">
                            <select id="<?='pcrrisk_'.$side.'_doctor_grade_id'?>"
                                    class="pcr_doctor_grade"
                                    name="OEModule_OphCiExamination_models_Element_OphCiExamination_PcrRisk[<?= $side ?>_doctor_grade_id]">
                                <?php if(is_array($grades)):?>
                                    <?php foreach ($grades as $grade):?>
                                        <?php
                                            if($element->{$side . '_doctor_grade_id'} === $grade->id):
                                                $selected = 'selected';
                                            else:
                                                $selected = '';
                                            endif;
                                        ?>
                                        <option value="<?=$grade->id?>" data-pcr-value="<?=$grade->pcr_risk_value?>" <?=$selected?>><?=$grade->grade?></option>
                                    <?php endforeach;?>
                                <?php endif;?>
                            </select>
                        </div>
                    </div>
                <?php
                else:
                    if($element->{'left_diabetic'} == 'Y' OR $element->{'left_diabetic'} == 'N'){
                        $element->{'right_diabetic'} = $element->{'left_diabetic'};
                    }elseif($element->{'right_diabetic'} == 'Y' OR $element->{'right_diabetic'} == 'N'){
                        $element->{'left_diabetic'} = $element->{'right_diabetic'};
                    }
                    if($element->{'left_alpha_receptor_blocker'} == 'Y' OR $element->{'left_alpha_receptor_blocker'} == 'N'){
                        $element->{'right_alpha_receptor_blocker'} = $element->{'left_alpha_receptor_blocker'};
                    }elseif($element->{'right_alpha_receptor_blocker'} == 'Y' OR $element->{'right_alpha_receptor_blocker'} == 'N'){
                        $element->{'left_alpha_receptor_blocker'} = $element->{'right_alpha_receptor_blocker'};
                    }
                    echo $form->dropDownList(
                        $element,
                        $side.'_'.$key,
                        $data['options'],
                        array('class' => $data['class']),
                        false,
                        array('label' => 4, 'field' => 4)
                    );
                endif;
            endforeach;?>
            <div class="row field-row">
                <div class="large-4 column pcr-risk-div">
                    <label>
                        PCR Risk <span class="pcr-span">&nbsp;</span> %
                        <?php $form->hiddenInput($element, $side.'_pcr_risk', false, array('class' => 'pcr-input'));?>
                    </label>
                </div>
                <div class="large-8 column end">
                    <label>
                        Excess risk compared to average eye <span class="pcr-erisk">&nbsp;</span> times
                        <?php $form->hiddenInput($element, $side.'_excess_risk', false, array('class' => 'pcr-erisk-input'));?>
                    </label>
                </div>
            </div>
        </div>
        <div class="inactive-form">
            <div class="add-side">
                <a href="#">
                    Add <?=$side?> side <span class="icon-add-side"></span>
                </a>
            </div>
        </div>
    </div>

<?php endforeach; ?>
</div>
<div class="large-6 column pcr-link">
    Calculation data derived from
    <a href="http://www.researchgate.net/publication/5525424_The_Cataract_National_Dataset_electronic_multicentre_audit_of_55_567_operations_Risk_stratification_for_posterior_capsule_rupture_and_vitreous_loss"
       target="_blank">
        Narendran et al. The Cataract National Dataset electronic multicentre audit of 55,567 operations
    </a>
</div>
