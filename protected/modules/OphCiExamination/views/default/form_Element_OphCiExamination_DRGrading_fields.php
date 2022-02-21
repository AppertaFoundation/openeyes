<?php
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
 */

/**
 * @var $element Element_OphCiExamination_DRGrading
 * @var $side string
 */

use OEModule\OphCiExamination\models\Element_OphCiExamination_DRGrading;
use OEModule\OphCiExamination\models\OphCiExamination_DRGrading_ClinicalMaculopathy;
use OEModule\OphCiExamination\models\OphCiExamination_DRGrading_ClinicalRetinopathy;
use OEModule\OphCiExamination\models\OphCiExamination_DRGrading_NSCMaculopathy;
use OEModule\OphCiExamination\models\OphCiExamination_DRGrading_NSCRetinopathy;

$clinical_retinopathys = OphCiExamination_DRGrading_ClinicalRetinopathy::model()->activeOrPk($element->{$side . '_clinicalret_id'})->findAll();
?>
<table>
  <tbody>
    <?php if (!is_null($element->{$side . '_clinicalret'})) { ?>
  <tr>
    <td>
      <label for="<?=\CHtml::modelName($element) . '_' . $side . '_clinicalret_id'; ?>">
          <?php echo $element->getAttributeLabel($side . '_clinicalret_id') ?>:
      </label>
    </td>
    <td>
      <div class="wrapper field-highlight inline<?php if ($element->{$side . '_clinicalret'}) {
            ?> <?php echo $element->{$side . '_clinicalret'}->class ?><?php
                                                } else {
                                                    ?> none<?php
                                                } ?>">
          <?php $html_options = array('options' => array());
            foreach ($clinical_retinopathys as $clinical) {
                $html_options['options'][(string)$clinical->id] = array(
                  'class' => $clinical->class,
                  'data-code' => $clinical->code,
                );
            }
            echo CHtml::activeDropDownList(
                $element,
                $side . '_clinicalret_id',
                CHtml::listData($clinical_retinopathys, 'id', 'name'),
                $html_options
            );
            ?>
      </div>
      <span class="grade-info-icon" data-info-type="clinicalret">
        <i class="oe-i info small-icon"></i>
      </span>
      <div class="quicklook grade-info" style="display: none;">
          <?php
            $selected_value = CHtml::resolveValue($element, $side . '_clinicalret_id');
            if (!$selected_value && count($clinical_retinopathys)) {
                $selected_value = $clinical_retinopathys[0]->id;
            }
            foreach ($clinical_retinopathys as $clinical) {
                $show_div = false;
                if ($selected_value == $clinical->id) {
                    $show_div = true;
                }
                echo '<div ' . ($show_div ? ' ' : 'style="display: none;" ') . 'class="' . CHtml::modelName($element) . '_' . $side . '_clinicalret_desc" id="' . CHtml::modelName($element) . '_' . $side . '_clinicalret_desc_' . $clinical->code . '">' . $clinical->description . '</div>';
            } ?>
      </div>
      <div id="<?=\CHtml::modelName($element) . '_' . $side . '_all_clinicalret_desc'; ?>"
           class="grade-info-all"
           style="display: none; padding: 10px"
           data-select-id="<?=\CHtml::modelName($element) . '_' . $side . '_clinicalret_id'; ?>">
            <?php foreach ($clinical_retinopathys as $clinical) {?>
              <div class="status-box <?= getLevelColour($clinical->class)?>">
                <b>
                <a href="#" data-id="<?php echo $clinical->id ?>"><?php echo $clinical->name ?></a>
              </b>
                <br>
                  <?php echo nl2br($clinical->description) ?>
              </div>
            <?php } ?>
      </div>
    </td>
  </tr>
    <?php } ?>
    <?php $nsc_retinopathys = \OEModule\OphCiExamination\models\OphCiExamination_DRGrading_NSCRetinopathy::model()->activeOrPk($element->{$side . '_nscretinopathy_id'})->findAll() ?>
  <tr>
    <td>
      <label for="<?=\CHtml::modelName($element) . '_' . $side . '_nscretinopathy_id'; ?>">
            <?php echo $element->getAttributeLabel($side . '_nscretinopathy_id') ?>:
      </label>
    </td>
    <td>
      <div class="wrapper field-highlight inline<?php if ($element->{$side . '_nscretinopathy'}) {
            ?> <?php echo $element->{$side . '_nscretinopathy'}->class ?><?php
                                                } else {
                                                    ?> none<?php
                                                } ?>">
            <?php
            $nscretinopathy_html_options = array('options' => array());
            foreach ($nsc_retinopathys as $retin) {
                $nscretinopathy_html_options['options'][(string)$retin->id] = array(
                  'data-booking' => $retin->booking_weeks,
                  'class' => $retin->class,
                  'data-code' => $retin->code,
                );
            }
            echo CHtml::activeDropDownList(
                $element,
                $side . '_nscretinopathy_id',
                CHtml::listData($nsc_retinopathys, 'id', 'name'),
                $nscretinopathy_html_options
            );
            ?>
      </div>
      <span class="grade-info-icon" data-info-type="retinopathy">
        <i class="oe-i info small-icon"></i>
      </span>
      <div class="quicklook grade-info" style="display: none;">
            <?php
            $selected_value = CHtml::resolveValue($element, $side . '_nscretinopathy_id');
            if (!$selected_value && count($nsc_retinopathys)) {
                $selected_value = $nsc_retinopathys[0]->id;
            }
            foreach ($nsc_retinopathys as $retin) {
                $show_div = false;
                if ($selected_value == $retin->id) {
                    $show_div = true;
                }
                echo '<div ' . ($show_div ? ' ' : 'style="display: none;" ') . 'class="' . CHtml::modelName($element) . '_' . $side . '_nscretinopathy_desc" id="' . CHtml::modelName($element) . '_' . $side . '_nscretinopathy_desc_' . $retin->code . '">' . $retin->description . '</div>';
            } ?>
      </div>
      <div id="<?=\CHtml::modelName($element) . '_' . $side . '_all_retinopathy_desc'; ?>"
           class="grade-info-all"
           style="display: none; padding:10px;"
           data-select-id="<?=\CHtml::modelName($element) . '_' . $side . '_nscretinopathy_id'; ?>">
            <?php foreach ($nsc_retinopathys as $retin) { ?>
              <div class="status-box <?= getLevelColour($retin->class) ?>">
                <b>
                  <a href="#" data-id="<?php echo $retin->id ?>"><?php echo $retin->name ?></a>
                </b>
                <br>
                  <?php echo nl2br($retin->description) ?>
              </div>
            <?php } ?>
      </div>
    </td>
  </tr>
  <tr>
    <td>
      <label for="<?=\CHtml::modelName($element) . '_' . $side . '_nscretinopathy_photocoagulation'; ?>">
            <?php echo $element->getAttributelabel($side . '_nscretinopathy_photocoagulation') ?>:
      </label>
    </td>
    <td>
        <?php echo $form->radioBoolean($element, $side . '_nscretinopathy_photocoagulation', array('nowrapper'=> true), array());
        $clinical_maculopathys = \OEModule\OphCiExamination\models\OphCiExamination_DRGrading_ClinicalMaculopathy::model()->activeOrPk($element->{$side . '_clinicalmac_id'})->findAll();
        $curr_cm = $element->{$side . '_clinicalmac'} ? $element->{$side . '_clinicalmac'} : @$clinical_maculopathys[0];
        ?>
    </td>
  </tr>
  <tr>
    <td>
      <label for="<?=\CHtml::modelName($element) . '_' . $side . '_clinicalmac_id'; ?>">
            <?php echo $element->getAttributelabel($side . '_clinicalmac_id') ?>:
      </label>
    </td>
    <td>
      <div class="wrapper field-highlight inline<?php if ($curr_cm) {
            ?> <?php echo $curr_cm->class ?><?php
                                                } else {
                                                    ?> none<?php
                                                } ?>">
            <?php
            $html_options = array('options' => array());
            foreach ($clinical_maculopathys as $clinical) {
                $html_options['options'][(string)$clinical->id] = array(
                  'class' => $clinical->class,
                  'data-code' => $clinical->code,
                );
            }
            echo CHtml::activeDropDownList(
                $element,
                $side . '_clinicalmac_id',
                CHtml::listData($clinical_maculopathys, 'id', 'name'),
                $html_options
            );
            ?>
      </div>
      <!-- REMOVED UNTIL WE ARE PROVIDED WITH APPROPRIATE TEXT FOR THE DESCRIPTIONS
        TODO: code to auto detect when there are no descriptions, so that this works dynamically based on the data.
        <span class="grade-info-icon" data-info-type="clinical">
            <img src="<?php echo $this->getAssetPathForElement($element) ?>/img/icon_info.png" style="height:20px" />
        </span>
        <div class="quicklook grade-info" style="display: none;">
            <?php foreach ($clinical_maculopathys as $clinical) {
                    echo '<div style="display: none;" class="' . CHtml::modelName($element) . '_' . $side . '_clinicalmac_desc" id="' . CHtml::modelName($element) . '_' . $side . '_clinicalmac_desc_' . $clinical->code . '">' . $clinical->description . '</div>';
            }
            ?>
        </div>

        <div id="<?=\CHtml::modelName($element) . '_' . $side . '_all_clinicalmac_desc'; ?>" class="grade-info-all" data-select-id="<?=CHtml::modelName($element) . '_' . $side . '_clinicalmac_id' ?>">
            <dl>
                <?php foreach ($clinical_maculopathys as $clinical) {
                    ?>
                    <dt class="<?php echo $clinical->class ?>">
                        <a href="#" data-id="<?php echo $clinical->id ?>"><?php echo $clinical->name ?></a>
                    </dt>
                    <dd class="<?php echo $clinical->class ?>"><?php echo nl2br($clinical->description) ?></dd>
                    <?php
                } ?>
            </dl>
        </div>
        -->
    </td>
  </tr>
    <?php $nsc_maculopathys = \OEModule\OphCiExamination\models\OphCiExamination_DRGrading_NSCMaculopathy::model()->activeOrPk($element->{$side . '_nscmaculopathy_id'})->findAll(); ?>
  <tr>
    <td>
      <label for="<?=\CHtml::modelName($element) . '_' . $side . '_nscmaculopathy_id'; ?>">
            <?php echo $element->getAttributelabel($side . '_nscmaculopathy_id') ?>:
      </label>
    </td>
    <td>
      <div class="wrapper field-highlight inline<?php if ($element->{$side . '_nscmaculopathy'}) {
            ?> <?php echo $element->{$side . '_nscmaculopathy'}->class ?><?php
                                                } else {
                                                    ?> none<?php
                                                } ?>">
            <?php
            $nscmaculopathy_html_options = array('options' => array());
            foreach ($nsc_maculopathys as $macu) {
                $nscmaculopathy_html_options['options'][(string)$macu->id] = array(
                  'data-booking' => $macu->booking_weeks,
                  'class' => $macu->class,
                  'data-code' => $macu->code,
                );
            }
            echo CHtml::activeDropDownList(
                $element,
                $side . '_nscmaculopathy_id',
                CHtml::listData($nsc_maculopathys, 'id', 'name'),
                $nscmaculopathy_html_options
            );
            ?>
      </div>
      <span class="grade-info-icon" data-info-type="maculopathy">
        <i class="oe-i info small-icon"></i>
      </span>
      <div class="quicklook grade-info" style="display: none;">
            <?php
            $selected_value = CHtml::resolveValue($element, $side . '_nscmaculopathy_id');
            if (!$selected_value && count($nsc_maculopathys)) {
                $selected_value = $nsc_maculopathys[0]->id;
            }
            foreach ($nsc_maculopathys as $macu) {
                $show_div = false;

                if ($selected_value === $macu->id) {
                    $show_div = true;
                }
                        echo '<div ' . ($show_div ? ' ' : 'style="display: none;" ') . 'class="' . CHtml::modelName($element) . '_' . $side . '_nscmaculopathy_desc desc" id="' . CHtml::modelName($element) . '_' . $side . '_nscmaculopathy_desc_' . $macu->code . '">' . $macu->description . '</div>';
            }
            ?>
                </div>
                <!-- div containing the full list of descriptions for nsc maculopathy -->
                <div id="<?=CHtml::modelName($element) . '_' . $side . '_all_maculopathy_desc' ?>"
                     class="grade-info-all"
                     style="display: none; padding: 10px;"
                     data-select-id="<?=CHtml::modelName($element) . '_' . $side . '_nscmaculopathy_id' ?>">
                    <dl>
                        <?php foreach ($nsc_maculopathys as $macu) { ?>
                            <div class="status-box <?= getLevelColour($macu->class) ?>">
                                <b>
                                    <a href="#" data-id="<?php echo $macu->id ?>"><?php echo $macu->name ?></a>
                                </b>
                                <br>
                                <?php echo nl2br($macu->description) ?>
                            </div>
                            <?php
                        } ?>
                    </dl>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <label for="<?=CHtml::modelName($element) . '_' . $side . '_nscmaculopathy_photocoagulation' ?>">
                    <?php echo $element->getAttributelabel($side . '_nscmaculopathy_photocoagulation') ?>:
                </label>
            </td>
            <td>
                <?php echo $form->radioBoolean($element, $side . '_nscmaculopathy_photocoagulation', array('nowrapper'=> true)) ?>
            </td>
        </tr>
        </tbody>
    </table>