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

<div class=" furtherfindings-multi-select cols-full ">
  <div id="div_GeneticsPatient_Pedigree" class="eventDetail row widget flex-layout cols-full ">
    <div class="cols-7">
      <label for="GeneticsPatient[pedigrees]">
        Pedigree:
      </label>
    </div>
    <div class="cols-4">
      <div class="multi-select">

            <?php // ok, so this is here because this is saved through the Admin() class and we need to mimic the behavior and the html for the MultiSelectList ?>
        <select class="hidden"></select>

            <input type="hidden" name="GeneticsPatient[MultiSelectList_GeneticsPatient[pedigrees]]" class="multi-select-list-name">
          <?php
            $this->widget('application.widgets.AutoCompleteSearch',
              [
                  'field_name' => "GeneticsPatient[pedigree]",
                  'htmlOptions' =>
                      [
                          'placeholder' => 'Search for pedigree id',
                      ],
                  'layoutColumns' => ['field' => '4'],
                  'hide_no_result_msg' => true
              ]);
            ?>

        <ul class="MultiSelectList pedigree-list multi-select-selections">

            <?php foreach (GeneticsPatientPedigree::model()->findAllByAttributes(array('patient_id' => $genetics_patient->id)) as $pedigree) : ?>
              <li>
                <a href="/Genetics/pedigree/edit/<?= $pedigree->pedigree_id; ?>">
                  <span class="text">
                      <?= $pedigree->pedigree_id; ?>
                      <?php if ($pedigree->pedigree->gene) : ?>
                        (<?= $pedigree->pedigree->gene->name ?>)
                      <?php endif; ?>
                  </span>
                </a>

                <span class="multi-select-remove remove-one" data-text="<?= $pedigree->pedigree_id; ?>">
                              <i class="oe-i remove-circle small"></i>
                            </span>
                <input type="hidden" name="GeneticsPatient[pedigrees][]" value="<?= $pedigree->pedigree_id; ?>">

                <select name="GeneticsPatient[pedigrees_through][<?= $pedigree->pedigree_id; ?>][status_id]">
                    <?php foreach (PedigreeStatus::model()->findAll() as $pedigree_status) : ?>
                                <option value="<?=$pedigree_status->id;?>" <?php echo $pedigree_status->id == $pedigree->status_id ? 'selected=""' : '';?>><?=$pedigree_status->name;?></option>
                    <?php endforeach; ?>
                </select>
              </li>
            <?php endforeach; ?>

        </ul>
      </div>
    </div>
    <div class="cols-1 column end">
            <img class="loader-pedigree hidden" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif');?>" alt="loading..." style="margin-right:10px" />
    </div>
  </div>


  <script>

    pedigree_status_template = '<li><a href="/Genetics/pedigree/edit/{{pedigreeId}}"><span class="text">{{label}}</span></a>' +
      '<span data-text="{{pedigreeId}}" class="multi-select-remove remove-one"><i class="oe-i remove-circle small"></i></span>' +
      '<input type="hidden" name="GeneticsPatient[pedigrees][]" value="{{pedigreeId}}">' +

      '<select name="GeneticsPatient[pedigrees_through][{{pedigreeId}}][status_id]">' +
        <?php foreach (PedigreeStatus::model()->findAll() as $pedigree_status) : ?>
      '<option value="<?=$pedigree_status->id;?>" <?php echo $pedigree_status->name == 'Unknown' ? 'selected=""' : ''?> ><?=$pedigree_status->name;?></option>' +

        <?php endforeach; ?>

      '</select></li>';
  </script>

</div>

<script type="text/javascript">
    $(document).ready(function() {
        OpenEyes.UI.AutoCompleteSearch.init({
            input: $(`[id="GeneticsPatient\\[pedigree\\]"]`),
            url: '/Genetics/pedigree/search',
            params: {
            },
            maxHeight: '200px',
            minimumCharacterLength: 1,
            onSelect: function () {
                let response = OpenEyes.UI.AutoCompleteSearch.getResponse();
                let input = OpenEyes.UI.AutoCompleteSearch.getInput();

                if (response.value){
                   $('ul.pedigree-list').append(
                       Mustache.render(pedigree_status_template, {
                           pedigreeId: response.value,
                           label: response.label
                       })
                   ).show();
                   toggleNoPedigreeCheckbox();
               }
            }
        });
    });
</script>