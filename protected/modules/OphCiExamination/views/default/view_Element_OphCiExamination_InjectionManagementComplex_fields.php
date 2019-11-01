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
<table class="cols-full">
  <tbody>
    <?php if ($element->{$side.'_no_treatment'}) :?>
      <tr class="flex-layout">
        <td class="cols-4">
            <?= $element->getAttributeLabel($side.'_no_treatment_reason_id')?>:
        </td>
        <td class="cols-8">
            <?= Yii::app()->format->Ntext($element->{"{$side}NoTreatmentReasonName"}) ?>
        </td>
      </tr>
    <?php else: ?>
      <tr>
        <td class="cols-4">
            <?= $element->getAttributeLabel($side.'_diagnosis1_id')?>:
        </td>
        <td class="cols-8">
            <?= $element->{$side.'_diagnosis1'}->term?>
        </td>
      </tr>
      <?php if ($element->{$side.'_diagnosis2_id'}) {?>
        <tr>
          <td class="cols-4">
              <?= $element->getAttributeLabel($side.'_diagnosis2_id')?>:
          </td>
          <td class="cols-8">
              <?php echo $element->{$side.'_diagnosis2'}->term?>
          </td>
        </tr>
      <?php }
      foreach ($element->{$side.'_answers'} as $answer) {?>
        <tr>
          <td class="cols-4">
              <?php echo $answer->question->question?>
          </td>
          <td class="cols-8">
              <?php echo ($answer->answer) ? 'Yes' : 'No'?>
          </td>
        </tr>
      <?php }?>
      <?php if ($element->{$side.'_treatment'}) {?>
        <tr>
          <td class="cols-4">
              <?php echo $element->getAttributeLabel($side.'_treatment_id')?>:
          </td>
          <td class="cols-8">
              <?php echo $element->{$side.'_treatment'}->name?>
          </td>
        </tr>
      <?php }?>
      <tr>
        <td class="cols-4">
            <?php echo $element->getAttributeLabel($side.'_risks')?>:
        </td>
        <td class="cols-8">
            <?php
            if (!$element->{$side.'_risks'}) {
              echo 'None';
              } else {
                foreach ($element->{$side.'_risks'} as $item) {
                    echo $item->name.'<br />';
                }
            }
            ?>
        </td>
      </tr>
      <tr>
        <td class="cols-4">
            <?php echo $element->getAttributeLabel($side.'_comments')?>:
        </td>
        <td class="cols-8">
            <div class="comment-block" >
                <?= Yii::app()->format->Ntext($element->{"{$side}_comments"}) ?>
            </div>
        </td>
      </tr>
    <?php endif;?>
  </tbody>
</table>
