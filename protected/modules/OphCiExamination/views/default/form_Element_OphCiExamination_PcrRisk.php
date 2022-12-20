<?php
$jsPath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.assets.js') . '/PCRCalculation.js', true, -1);
Yii::app()->clientScript->registerScriptFile($jsPath, CClientScript::POS_HEAD);
?>
<script type="text/javascript">
    function pcr_init() {
        //Map the elements
        mapExaminationToPcr();
        //Make the initial calculations
        var $pcrRiskEl = $('section.OEModule_OphCiExamination_models_Element_OphCiExamination_PcrRisk');
        //this needs when the PcrRisk is open and the AntSeg opened later, called when eyedraw is ready
        loadFromHiddenFieds($pcrRiskEl);
        pcrCalculate($pcrRiskEl.find('.left-eye'), 'left');
        pcrCalculate($pcrRiskEl.find('.right-eye'), 'right');

        $(document.body).on('change', $pcrRiskEl.find('.left-eye'), function () {
            pcrCalculate($pcrRiskEl.find('.left-eye'), 'left');
        });

        $(document.body).on('change', $pcrRiskEl.find('.right-eye'), function () {
            pcrCalculate($pcrRiskEl.find('.right-eye'), 'right');
        });

        $('.pcrrisk_diabetic').change(function () {
            let pcrrisk_diabetic_value = $(this).prop('selectedIndex');
            $('.pcrrisk_diabetic').prop('selectedIndex', pcrrisk_diabetic_value);
        });

        $('.pcrrisk_arb').change(function () {
            let pcrrisk_arb_value = $(this).prop('selectedIndex');
            $('.pcrrisk_arb').prop('selectedIndex', pcrrisk_arb_value);
        });

        $('.pcr_lie_flat').change(function () {
            let pcr_lie_flat_value = $(this).prop('selectedIndex');
            $('.pcr_lie_flat').prop('selectedIndex', pcr_lie_flat_value);
        });
    }

    $(document).ready(function () {
        pcr_init();
    });
</script>
<div class="element-eyes element-fields flex-layout full-width">
    <?php
    if ($this->patient->getDiabetes()) {
        $diabeticOptions = ['Y' => 'Diabetes present'];
    } else {
        $diabeticOptions = ['NK' => 'Not Known', 'N' => 'No Diabetes', 'Y' => 'Diabetes present'];
    }

    $criteria = new CDbCriteria();
    $criteria->condition = 'has_pcr_risk';
    $grades = \DoctorGrade::model()->findAll($criteria, ['order' => 'display_order']);
    $dropDowns = [
        'glaucoma' => [
            'options' => ['NK' => 'Not Known', 'N' => 'No Glaucoma', 'Y' => 'Glaucoma present'],
            'class' => 'pcrrisk_glaucoma',
        ],
        'pxf' => [
            'options' => ['NK' => 'Not Known', 'N' => 'No', 'Y' => 'Yes'],
            'class' => 'pcrrisk_pxf_phako',
        ],
        'diabetic' => [
            'options' => $diabeticOptions,
            'class' => 'pcrrisk_diabetic',
        ],
        'pupil_size' => [
            'options' => ['Large' => 'Large', 'Medium' => 'Medium', 'Small' => 'Small'],
            'class' => 'pcrrisk_pupil_size',
        ],
        'no_fundal_view' => [
            'options' => ['NK' => 'Not Known', 'N' => 'No', 'Y' => 'Yes'],
            'class' => 'pcrrisk_no_fundal_view',
        ],
        'axial_length_group' => [
            'options' => [0 => 'Not Known', 1 => '< 26', 2 => '> or = 26'],
            'class' => '',
        ],
        'brunescent_white_cataract' => [
            'options' => ['NK' => 'Not Known', 'N' => 'No', 'Y' => 'Yes'],
            'class' => 'pcrrisk_brunescent_white_cataract',
        ],
        'alpha_receptor_blocker' => [
            'options' => ['NK' => 'Not Known', 'N' => 'No', 'Y' => 'Yes'],
            'class' => 'pcrrisk_arb',
        ],
        'doctor_grade_id' => [
            'options' => $grades,
            'class' => 'pcr_doctor_grade',
        ],
        'can_lie_flat' => [
            'options' => ['NK' => 'Not Known', 'N' => 'No', 'Y' => 'Yes'],
            'class' => 'pcr_lie_flat',
        ],
    ];
    echo $form->hiddenInput($element, 'eye_id', false, ['class' => 'sideField']);

    foreach (['right' => 'left','left' => 'right'] as $side => $eye) :
        $opposite = ($side === 'right') ? 'left' : 'right';
        $pcrRisk = new PcrRisk();
        $activeClass = ($element->{'has' . ucfirst($side)}()) ? 'active' : 'inactive'; ?>
    <div class="js-element-eye <?= $side ?>-eye column <?= $opposite ?> side<?= $activeClass ?>"
        data-side="<?= $side ?>">
        <?php
        if ($this->event) {
            $patientId = $this->event->episode->patient->id;
        } else {
            $patientId = Yii::app()->request->getQuery('patient_id');
        }

            $pcr = $pcrRisk->getPCRData($patientId, $side, $element);
            echo CHtml::hiddenField('age', $pcr['age_group']);
            echo CHtml::hiddenField('gender', $pcr['gender']);
        ?>
        <div class="active-form js-pcr-<?= $side ?>"
            style="display: <?= !($element->{'has' . ucfirst($side)}()) ? 'none' : 'block'; ?>;">
            <a class="remove-side"><i class="oe-i remove-circle small"></i></a>

            <table class="cols-full last-left">
                <colgroup>
                    <col class="cols-6">
                    <col class="cols-6">
                </colgroup>
                <tbody>

                    <?php foreach ($dropDowns as $key => $data) : ?>
                    <tr class="col-gap">
                        <?php if ($key === 'doctor_grade_id') : ?>
                        <div id="div_OEModule_OphCiExamination_models_Element_OphCiExamination_PcrRisk_right_pcr_doctor_grade"
                            class="cols-full">
                            <td class="cols-4 column">
                                <label for="<?= 'pcrrisk_' . $side . '_doctor_grade_id' ?>">Surgeon
                                    Grade:</label>
                            </td>
                            <td class="cols-4 column">
                                <select id="<?= 'pcrrisk_' . $side . '_doctor_grade_id' ?>"
                                    class="pcr_doctor_grade cols-full"
                                    name="OEModule_OphCiExamination_models_Element_OphCiExamination_PcrRisk[<?= $side ?>_doctor_grade_id]">
                                    <?php if (is_array($grades)) : ?>
                                        <?php foreach ($grades as $grade) : ?>
                                            <?php
                                            if ($element->{$side . '_doctor_grade_id'} === $grade->id) :
                                                $selected = 'selected';
                                            else :
                                                        $selected = '';
                                            endif;
                                            ?>
                                    <option value="<?= $grade->id ?>" data-pcr-value="<?= $grade->pcr_risk_value ?>"
                                            <?= $selected ?>><?= $grade->grade ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </td>
                        </div>
                        <?php else :
                            if ($element->{'left_diabetic'} == 'Y' or $element->{'left_diabetic'} == 'N') {
                                $element->{'right_diabetic'} = $element->{'left_diabetic'};
                            } elseif ($element->{'right_diabetic'} == 'Y' or $element->{'right_diabetic'} == 'N') {
                                $element->{'left_diabetic'} = $element->{'right_diabetic'};
                            }
                            if ($element->{'left_alpha_receptor_blocker'} == 'Y' or $element->{'left_alpha_receptor_blocker'} == 'N') {
                                $element->{'right_alpha_receptor_blocker'} = $element->{'left_alpha_receptor_blocker'};
                            } elseif ($element->{'right_alpha_receptor_blocker'} == 'Y' or $element->{'right_alpha_receptor_blocker'} == 'N') {
                                $element->{'left_alpha_receptor_blocker'} = $element->{'right_alpha_receptor_blocker'};
                            } ?>
                        <td>
                            <?= $element->getAttributeLabel($side . '_' . $key) ?>
                        </td>
                        <td>
                            <?= CHtml::activeDropDownList($element, $side . '_' . $key, $data['options'], ['class' => $data['class'] . ' cols-full']); ?>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="data-group inline">
                <div id="pcr-risk-div" class="highlighter large-text">
                    PCR Risk <span class="pcr-span"> 6.1 </span> %
                </div>
            </div>
            <div class="data-group">
                <span>
                    <label> Excess risk compared to average eye <span class="pcr-erisk highlighter">&nbsp;</span> times
                        <?php $form->hiddenInput($element, $side . '_excess_risk', false, ['class' => 'pcr-erisk-input']); ?>
                    </label>
                    <a href="https://www.nature.com/articles/6703049" target="_blank">
                        <i class="oe-i info small pad js-has-tooltip"
                            data-tooltip-content="Calculation data derived from Narendran et al. The Cataract National Dataset electronic multicentre audit of 55,567 operations (click for more information)"></i>
                    </a>
                </span>
            </div>
        </div>
        <div class="inactive-form" style="display: <?= ($element->{'has' . ucfirst($side)}()) ? 'none' : 'flex'; ?>;">
            <div class="add-side">
                <a href="#">
                    Add <?= $side ?> side <span class="icon-add-side"></span>
                </a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
