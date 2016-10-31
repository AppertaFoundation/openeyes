<?php
Yii::app()->getAssetManager()->registerScriptFile('js/PCRCalculation.js')
?>

<div class="element-eyes element-fields">
<?php
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
        'options' => array('NK' => 'Not Known', 'N' => 'No Diabetes', 'Y' => 'Diabetes present'),
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
        'class' => '',
    ),
    'doctor_grade_id' => array(
        'options' => CHtml::listData($grades, 'pcr_risk_value', 'grade'),
        'class' => 'pcr_doctor_grade',
    ),
    'can_lie_flat' => array(
        'options' => array('N' => 'No', 'Y' => 'Yes'),
        'class' => '',
    ),
);
echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField'));

foreach (array('right', 'left') as $side):
    $opposite = ($side === 'right') ? 'left' : 'right';
    $pcrRisk = new PcrRisk();
    $activeClass = ($element->{'has'.ucfirst($side)}()) ? 'active' : 'inactive'; ?>
    <div class="element-eye <?=$side?>-eye column <?=$opposite?> side <?=$activeClass?>" data-side="<?=$side?>">
        <?php
        $pcr = $pcrRisk->getPCRData($this->event->episode->patient->id, $side, $element);
        echo CHtml::hiddenField('age', $pcr['age_group']);
        echo CHtml::hiddenField('gender', $pcr['gender']);
        ?>
        <div class="active-form">
            <a href="#" class="icon-remove-side remove-side">Remove side</a>
            <?php foreach ($dropDowns as $key => $data):
                echo $form->dropDownList(
                    $element,
                    $side.'_'.$key,
                    $data['options'],
                    array('class' => $data['class']),
                    false,
                    array('label' => 4, 'field' => 4)
                );
            endforeach;?>
            <div class="row field-row">
                <div class="large-4 column pcr-risk-div">
                    <label>
                        PCR Risk <span class="pcr-span">&nbsp;</span> %
                    </label>
                </div>
                <div class="large-8 column end">
                    <label>
                        Excess risk compared to average eye <span class="pcr-erisk">&nbsp;</span> times
                    </label>
                </div>
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
<?php endforeach; ?>
    <div class="large-6 column pcr-link">
        Calculation data derived from
        <a href="http://www.researchgate.net/publication/5525424_The_Cataract_National_Dataset_electronic_multicentre_audit_of_55_567_operations_Risk_stratification_for_posterior_capsule_rupture_and_vitreous_loss"
           target="_blank">
            Narendran et al. The Cataract National Dataset electronic multicentre audit of 55,567 operations
        </a>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        //Map the elements
        mapExaminationToPcr();
        //Make the initial calculations
        var $pcrRiskEl = $('section.Element_OphCiExamination_PcrRisk');
        pcrCalculate($pcrRiskEl.find('.left-eye'), 'left');
        pcrCalculate($pcrRiskEl.find('.right-eye'), 'right');

        $(document.body).on('change', $pcrRiskEl.find('.left-eye'), function () {
            pcrCalculate($pcrRiskEl.find('.left-eye'), 'left');
        });

        $(document.body).on('change', $pcrRiskEl.find('.right-eye'), function () {
            pcrCalculate($pcrRiskEl.find('.right-eye'), 'right');
        });
    });
</script>

