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
Yii::app()->clientScript->registerScriptFile("{$this->assetPath}/js/mmc.js");

$layoutColumns = $form->layoutColumns;
$form->layoutColumns = array('label' => 3, 'field' => 3);
?>
<!--
<div class="element-fields full-width">
    <?php $form->dropDownList(
        $element,
        'application_type_id',
        'OphTrOperationnote_Antimetabolite_Application_Type',
        array('empty' => '-- Select --')
    ); ?>
    <?php $form->dropDownList($element, 'concentration_id', 'OphTrOperationnote_Mmc_Concentration'); ?>
  <div id="ophtroperationnote-mmc-sponge" class="ophtroperationnote-mmc-application hidden">
        <?php $form->dropDownList($element, 'duration', array_combine(range(1, 5), range(1, 5))); ?>
        <?php $form->dropDownList($element, 'number', array_combine(range(1, 5), range(1, 5))); ?>
        <?php $form->checkBox($element, 'washed'); ?>
  </div>
  <div id="ophtroperationnote-mmc-injection" class="ophtroperationnote-mmc-application hidden">
    <div class="data-group">
      <div class="<?= $form->columns() ?>"><label><?= CHtml::encode($element->getAttributeLabel('dose')) ?></label>
      </div>
      <div id="ophtroperationnote-mmc-dose" class="data-value <?= $form->columns('field', true) ?>"></div>
    </div>
  </div>
</div>
-->
<?php $form->layoutColumns = $layoutColumns; ?>

<div class="element-fields full-width">
  <div class="cols-7 flex-layout flex-top col-gap">
    <div class="cols-6">
      <table class="last-left">
        <tbody>
        <tr>
          <td>
                <?php echo $element->getAttributeLabel('application_type_id'); ?>
          </td>
          <td>
                <?php $form->dropDownList(
                    $element,
                    'application_type_id',
                    'OphTrOperationnote_Antimetabolite_Application_Type',
                    array('nowrapper' => true, 'empty' => '-- Select --')
                ); ?>
          </td>
        </tr>
        <tr>
          <td>
                <?php echo $element->getAttributeLabel('concentration_id'); ?>
          </td>
          <td>
                <?php $form->dropDownList(
                    $element,
                    'concentration_id',
                    'OphTrOperationnote_Mmc_Concentration',
                    array('nowrapper' => true)
                ); ?>
          </td>
        </tr>
        </tbody>
      </table>
    </div>

    <div id="ophtroperationnote-mmc-sponge" class="cols-7 ophtroperationnote-mmc-application hidden">
      <table>
        <tbody>
        <tr>
          <td>
                <?php echo $element->getAttributeLabel('duration'); ?>
          </td>
          <td>
                <?php $form->dropDownList(
                    $element,
                    'duration',
                    array_combine(range(1, 5), range(1, 5)),
                    array('nowrapper' => true)
                ); ?>
        </tr>
        <tr>
          <td>
                <?php echo $element->getAttributeLabel('number'); ?>
          </td>
          <td>
                <?php $form->dropDownList(
                    $element,
                    'number',
                    array_combine(range(1, 5), range(1, 5)),
                    array('nowrapper' => true)
                ); ?>
          </td>
        </tr>
        <tr>
          <td colspan="2">
                <?php $form->checkBox($element, 'washed', array('nowrapper' => true)); ?>
          </td>
        </tr>
        </tbody>
      </table>
    </div>


    <div id="ophtroperationnote-mmc-injection" class="cols-7 ophtroperationnote-mmc-application hidden">
      <table>
        <tbody>
        <tr>
          <td>
                <?php echo $element->getAttributeLabel('volume_id'); ?>
          </td>
          <td>
                <?php $form->dropDownList(
                    $element,
                    'volume_id',
                    'OphTrOperationnote_Mmc_Volume',
                    array('nowrapper' => true)
                ); ?>
        </tr>
        <tr>
          <td></td>
          <td>
            <label><?= CHtml::encode($element->getAttributeLabel('dose')) ?></label>
            <span id="ophtroperationnote-mmc-dose" class="data-value">
            </span>
          </td>
        </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
