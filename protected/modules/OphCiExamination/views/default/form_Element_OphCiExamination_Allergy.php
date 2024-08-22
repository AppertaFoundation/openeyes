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
<div class="element-fields flex-layout full-width ">
  <div class="cols-12 column">
        <?php if ($this->checkAccess('OprnEditAllergy')) { ?>
          <input type="hidden" name="OEModule_OphCiExamination_models_Element_OphCiExamination_Allergy[allergy_loaded]" value="1">
          <div id="add_allergy">
            <div class="allergies_confirm_no data-group" style="display: none;">
              <div class="allergies">
                <div class="<?php echo $form->columns('label'); ?>">
                  <label for="no_allergies">Confirm patient has no allergies:</label>
                </div>
                <div class="<?php echo $form->columns('field'); ?>">
                    <?=\CHtml::checkBox('no_allergies', $this->patient->no_allergies_date ? true : false); ?>
                </div>
              </div>
            </div>

            <input type="hidden" name="edit_allergy_id" id="edit_allergy_id" value=""/>
            <input type="hidden" name="patient_id" value="<?php echo $this->patient->id ?>"/>

            <div class="data-group allergy_field">
              <div class="<?php echo $form->columns('label'); ?>">
                <label for="allergy_id">Add allergy:</label>
              </div>
              <div class="<?php echo $form->columns('field'); ?>">
                  <?php
                    $allAllergies = \Allergy::model()->findAll(array('order' => 'display_order', 'condition' => 'active=1'));
                    echo CHtml::dropDownList(
                        'allergy_id',
                        null,
                        CHtml::listData($allAllergies, 'id', 'name'),
                        array('empty' => '-- Select --')
                    ); ?>
              </div>
            </div>
            <div id="allergy_other" class="data-group hidden">
              <div class="<?php echo $form->columns('label'); ?>">
                <label for="allergy_id">Other allergy:</label>
              </div>
              <div class="<?php echo $form->columns('field'); ?>">
                  <?= CHtml::textField('other_allergy', '', array('autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'))); ?>
              </div>
            </div>
            <div class="data-group allergy_field">
              <div class="<?php echo $form->columns('label'); ?>">
                <label for="comments">Comments:</label>
              </div>
              <div class="<?php echo $form->columns('field'); ?>">
                  <?=\CHtml::textField('comments', '', array('autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'))) ?>
              </div>
            </div>

            <div class="buttons cols-12 column">
              <img src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif') ?>" class="add_allergy_loader" style="display: none;"/>
              <button type="button" class="secondary small btn_save_allergy right">Add</button>
            </div>
          </div>
        <?php } ?>
    </div>
  <div class="cols-12 data-group column">
      <table>
        <thead>
        <tr>
          <th>Allergies</th>
          <th>Comments</th>
          <th>Actions</th>
        </tr>
        </thead>
        <tbody id="OphCiExamination_allergy">
        <?php foreach ($this->allergies as $aa) { ?>
          <script type="text/javascript">
            removeAllergyFromSelect(<?= $aa->allergy->id?>, '<?= $aa->allergy->name ?>');
          </script>
          <tr data-assignment-id="<?= $aa->id ?>" data-allergy-id="<?= $aa->allergy->id ?>" data-allergy-name="<?= $aa->allergy->name ?>">
            <td><?= CHtml::encode($aa->name) ?>
            </td>
            <td><?= CHtml::encode($aa->comments) ?></td>
              <?php if ($this->checkAccess('OprnEditAllergy')) { ?>
                <td>
                  <a rel="<?php echo $aa->id ?>" class="small removeAllergy">
                    Remove
                  </a>
                </td>
                    <?php if (!isset($aa->id)) { ?>
                  <input type="hidden" value="<?php echo $aa->allergy->id;
                    ?>" name="selected_allergies[]">
                  <input type="hidden" value="<?php echo $aa->comments;
                    ?>" name="allergy_comments[]">
                  <input type="hidden" value="<?php echo $aa->other;
                    ?>" name="other_names[]">
                    <?php }
              } ?>
          </tr>
            <?php
        }
        foreach ($this->deletedAllergies as $deletedId) {
            echo '<input type="hidden" value="' . $deletedId . '" name="deleted_allergies[]">';
        } ?>
        </tbody>
      </table>
    </div>
</div>
