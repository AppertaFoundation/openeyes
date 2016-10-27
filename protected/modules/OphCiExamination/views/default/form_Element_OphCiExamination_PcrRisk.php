<div class="element-eyes element-fields">
<?php
$criteria = new CDbCriteria();
$criteria->condition = 'has_pcr_risk';
$grades = \DoctorGrade::model()->findAll($criteria, array('order' => 'display_order'));
$dropDowns = array(
    'glaucoma' => array('NK' => 'Not Known', 'N' => 'No Glaucoma', 'Y' => 'Glaucoma present'),
    'pxf' => array('NK' => 'Not Known', 'N' => 'No', 'Y' => 'Yes'),
    'diabetic' => array('NK' => 'Not Known', 'N' => 'No Diabetes', 'Y' => 'Diabetes present'),
    'pupil_size' => array('Large' => 'Large', 'Medium' => 'Medium', 'Small' => 'Small'),
    'no_fundal_view' => array('NK' => 'Not Known', 'N' => 'No', 'Y' => 'Yes'),
    'axial_length_group' => array('NK' => 'Not Known', 1 => '< 26', 2 => '> or = 26'),
    'brunescent_white_cataract' => array('NK' => 'Not Known', 'N' => 'No', 'Y' => 'Yes'),
    'alpha_receptor_blocker' => array('NK' => 'Not Known', 'N' => 'No', 'Y' => 'Yes'),
    'doctor_grade_id' => CHtml::listData($grades, 'pcr_risk_value', 'grade'),
    'can_lie_flat' => array('N' => 'No', 'Y' => 'Yes'),
);
foreach (array('right', 'left') as $side):
    $opposite = ($side === 'right') ? 'left' : 'right'
?>
    <?php echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField')) ?>
    <?php $activeClass = ($element->{'has'.ucfirst($side)}()) ? 'active' : 'inactive'; ?>
    <div class="element-eye <?=$side?>-eye column <?=$opposite?> side <?=$activeClass?>" data-side="<?=$side?>">
        <div class="active-form">
            <a href="#" class="icon-remove-side remove-side">Remove side</a>
            <?php foreach ($dropDowns as $key => $data):
                echo $form->dropDownList(
                    $element,
                    $side.'_'.$key,
                    $data,
                    array(),
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

