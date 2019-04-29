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
?>
<?php
$html_options = array();
foreach (OEModule\OphCiExamination\models\OphCiExamination_Gonioscopy_Description::model()->findAll() as $option) {
    $html_options[(string)$option->id] = array('data-value' => $option->name);
}
?>
<div class="eyedraw-fields">

  <div class="data-group">
    <label for="<?php echo $side . '_gonioscopy_mode'; ?>">
      Mode:
    </label>
      <?=\CHtml::dropDownList($side . '_gonioscopy_mode', 'Basic',
          array('Basic' => 'Basic', 'Expert' => 'Expert'), array(
              'class' => 'gonioscopy-mode',
              'options' => array(
                  'Basic' => array('data-value' => 'Basic'),
                  'Expert' => array('data-value' => 'Expert'),
              ),
          )) ?>
  </div>
  <div style="display: none;" class="shaffer-grade expert-mode">
    <div class="field-label">Shaffer grade:</div>
    <div class="gonio-cross">
      <div class="gonio-sup">
          <?=\CHtml::activeDropDownList($element, $side . '_gonio_sup_id', $element->getGonioscopyOptions(),
              array(
                  'class' => 'inline gonioGrade gonioExpert',
                  'data-position' => 'sup',
                  'options' => $html_options,
              )) ?>
      </div>
      <div class="gonio-tem">
          <?=\CHtml::activeDropDownList($element, $side . '_gonio_tem_id', $element->getGonioscopyOptions(),
              array(
                  'class' => 'inline gonioGrade gonioExpert',
                  'data-position' => 'tem',
                  'options' => $html_options,
              )) ?>
      </div>
      <div class="gonio-nas">
          <?=\CHtml::activeDropDownList($element, $side . '_gonio_nas_id', $element->getGonioscopyOptions(),
              array(
                  'class' => 'inline gonioGrade gonioExpert',
                  'data-position' => 'nas',
                  'options' => $html_options,
              )) ?>
      </div>
      <div class="gonio-inf">
          <?=\CHtml::activeDropDownList($element, $side . '_gonio_inf_id', $element->getGonioscopyOptions(),
              array(
                  'class' => 'inline gonioGrade gonioExpert',
                  'data-position' => 'inf',
                  'options' => $html_options,
              )) ?>
      </div>
    </div>
  </div>

  <div style="display:none;" class="basic-mode">
    <div class="field-label">Angle Open?:</div>
      <?php
      $basic_options = array('0' => 'No', '1' => 'Yes');
      $html_options = array('1' => array('data-value' => 'Yes'), '0' => array('data-value' => 'No'));
      ?>
    <div class="gonio-cross">
      <div class="gonio-sup">
          <?=\CHtml::dropDownList($side . '_gonio_sup_basic',
              ($element->{$side . '_gonio_sup'}) ? $element->{$side . '_gonio_sup'}->seen : true, $basic_options,
              array('class' => 'inline gonioGrade gonioBasic', 'data-position' => 'sup', 'options' => $html_options)) ?>
      </div>
      <div class="gonio-tem">
          <?=\CHtml::dropDownList($side . '_gonio_tem_basic',
              ($element->{$side . '_gonio_tem'}) ? $element->{$side . '_gonio_tem'}->seen : true, $basic_options,
              array('class' => 'inline gonioGrade gonioBasic', 'data-position' => 'tem', 'options' => $html_options)) ?>
      </div>
      <div class="gonio-nas">
          <?=\CHtml::dropDownList($side . '_gonio_nas_basic',
              ($element->{$side . '_gonio_nas'}) ? $element->{$side . '_gonio_nas'}->seen : true, $basic_options,
              array('class' => 'inline gonioGrade gonioBasic', 'data-position' => 'nas', 'options' => $html_options)) ?>
      </div>
      <div class="gonio-inf">
          <?=\CHtml::dropDownList($side . '_gonio_inf_basic',
              ($element->{$side . '_gonio_inf'}) ? $element->{$side . '_gonio_inf'}->seen : true, $basic_options,
              array('class' => 'inline gonioGrade gonioBasic', 'data-position' => 'inf', 'options' => $html_options)) ?>
      </div>
    </div>
  </div>
    <?=\CHtml::activeHiddenField($element, $side . '_ed_report'); ?>
  <div class="data-group">
    <div class="cols-6 column end">
      <label for="<?= CHtml::modelName($element) . '_' . $side . '_ed_report'; ?>">
          <?= $element->getAttributeLabel($side . '_ed_report') ?>:
      </label>
    </div>
    <div class="cols-10 column end autoreport-display">
      <span class="data-value" id="<?= CHtml::modelName($element) . '_' . $side . '_ed_report_display' ?>"></span>
    </div>
  </div>

  <div class="data-group">
    <label for="<?= CHtml::modelName($element) . '_' . $side . '_iris_id'; ?>">
      <?= $element->getAttributeLabel($side . '_iris_id') ?>:
    </label>
    <?=\CHtml::activeDropDownList($element, $side . '_iris_id',
            \CHtml::listData($element->getIrisOptions(), 'id', 'name'),
            [
              'empty' => 'Not recorded',
              'class' => 'iris',
              'options' => $html_options,
            ]) ?>
  </div>

  <div class="data-group">
    <label for="<?= CHtml::modelName($element) . '_' . $side . '_description'; ?>">
        <?= $element->getAttributeLabel($side . '_description'); ?>:
    </label>
      <?= CHtml::activeTextArea($element, $side . '_description',
          array('rows' => '2', 'class' => 'autosize clearWithEyedraw')) ?>
  </div>
</div>
