<?php

    use OEModule\OphCiExamination\models\Element_OphCiExamination_DRGrading;
    use OEModule\OphCiExamination\models\OphCiExamination_DRGrading_Feature;

    /**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 *
 * @var $element Element_OphCiExamination_DRGrading
 */

/**
 * @param $risk_level
 * @return string
 */
function getLevelColour($risk_level)
{
    switch ($risk_level) {
        case '':
        case 'none':
            return 'green';
            break;
        case 'pre-prolif':
        case 'moderate':
            return 'amber';
            break;
        case 'proliferative':
        case 'maculopathy':
        case 'severe':
        case 'high-risk':
            return 'red';
            break;
        case 'ungradable':
        case 'mild':
        case 'early':
            return 'blue';
        default:
            return 'blue';
    }
}

$r1_retinopathy_features = OphCiExamination_DRGrading_Feature::model()->findAllByAttributes(array('grade' => 'R1', 'active' => 1));
$r2_retinopathy_features = OphCiExamination_DRGrading_Feature::model()->findAllByAttributes(array('grade' => 'R2', 'active' => 1));
$r3s_retinopathy_features = OphCiExamination_DRGrading_Feature::model()->findAllByAttributes(array('grade' => 'R3s', 'active' => 1));
$r3a_retinopathy_features = OphCiExamination_DRGrading_Feature::model()->findAllByAttributes(array('grade' => 'R3a', 'active' => 1));
?>

<?php $this->beginClip('element-title-additional');?>
<div class="info">
    <?php if (file_exists(Yii::getPathOfAlias('application.modules.'.$this->getModule()->name.'.assets').'/img/drgrading.jpg')) {
        ?>
            <a href="#" class="drgrading_images_link"><img src="<?php echo $this->assetPath ?>/img/photo_sm.png" /></a>
            <div class="drgrading_images_dialog" title="DR Grading Images">
                <img src="<?php echo $this->assetPath ?>/img/drgrading.jpg">
            </div>
        <?php
    } ?>
</div>
<?php $this->endClip('element-title-additional');?>

<div class="element-both-eyes flex-layout full-width ">
    <?php echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField'))?>
    <fieldset class="data-group">
                <?php echo $element->getAttributeLabel('secondarydiagnosis_disorder_id')?>:
            <?php
            $diabetes = $this->patient->getDiabetesType();
            if ($diabetes) {
                echo '<span class="data-value">'.$diabetes->term.'</span>';
                echo \CHtml::hiddenField(CHtml::modelName($element).'[secondarydiagnosis_disorder_id]', $diabetes->id);
            } else {
                $form->radioButtons(
                    $element,
                    'secondarydiagnosis_disorder_id',
                    $element->getDiabetesTypes(),
                    null,
                    false,
                    false,
                    false,
                    false,
                    array('nowrapper' => true)
                );
            } ?>
    </fieldset>
</div>
<div class="element-fields element-eyes">
    <?php foreach (['left' => 'right', 'right' => 'left'] as $page_side => $eye_side) : ?>
  <div class="js-element-eye <?= $eye_side ?>-eye column <?= $page_side ?> <?php if ($element->id || !empty($_POST)) {
        ?> uninitialised<?php
                             }?>" data-side="<?= $eye_side ?>">
    <div class="active-form" style="<?= !$element->hasEye($eye_side) ? 'display: none;' : '' ?>">
      <a class="remove-side"><i class="oe-i remove-circle small"></i></a>
        <?php
            $this->renderPartial(
                $element->form_view.'_fields',
                array(
                    'side' =>$eye_side,
                    'element' => $element,
                    'form' => $form,
                )
            )
        ?>
    </div>
    <div class="inactive-form" style="<?= $element->hasEye($eye_side) ? 'display: none;' : '' ?>">
      <div class="add-side">
        <a href="#">
          Add <?= $eye_side ?> DR Grading
          <span class="icon-add-side"></span>
        </a>
      </div>
    </div>
    <?php endforeach; ?>
</div>

<script type="text/javascript">
    OphCiExamination_DRGrading_init();
</script>
