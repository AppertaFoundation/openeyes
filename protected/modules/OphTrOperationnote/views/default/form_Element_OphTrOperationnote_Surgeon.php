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
 *
 * @var Element_OphTrOperationnote_Surgeon $element
 */

?>
<div class="element-fields full-width flex-layout">
  <table class="cols-10 last-left">
    <colgroup>
      <col class="cols-4">
      <col class="cols-4">
      <col class="cols-4">
    </colgroup>
      <thead>
      <tr>
          <th><?=\CHtml::encode($element->getAttributeLabel('surgeon_id'));?></th>
          <th><?=\CHtml::encode($element->getAttributeLabel('assistant_id'));?></th>
          <th><?=\CHtml::encode($element->getAttributeLabel('supervising_surgeon_id'));?></th>
      </tr>
      </thead>
    <tbody>
    <tr class="col-gap">
      <td>
            <?php echo $form->dropDownList(
                $element,
                'surgeon_id',
                CHtml::listData($element->surgeons, 'id', 'ReversedFullName'),
                array(
                    'empty' => '- Please select -',
                    'class' => 'cols-full',
                    'nowrapper' => true,
                    'data-prefilled-value' => $template_data['surgeon_id'] ?? '',
                ),
                false,
                array('field' => 8)
            ); ?>
      </td>
      <td>
            <?php echo $form->dropDownList(
                $element,
                'assistant_id',
                CHtml::listData($element->surgeons, 'id', 'ReversedFullName'),
                array(
                    'empty' => '- None -',
                    'class' => 'cols-full',
                    'nowrapper' => true,
                    'data-prefilled-value' => $template_data['assistant_id'] ?? '',
                ),
                false,
                array('field' => 8)
            ); ?>
      </td>
      <td>
            <?php echo $form->dropDownList(
                $element,
                'supervising_surgeon_id',
                CHtml::listData($element->surgeons, 'id', 'ReversedFullName'),
                array(
                    'empty' => '- None -',
                    'class' => 'cols-full',
                    'nowrapper' => true,
                    'data-prefilled-value' => $template_data['supervising_surgeon_id'] ?? '',
                ),
                false,
                array('field' => 8)
            ); ?>
      </td>
    </tr>
    </tbody>
  </table>
  <div class="add-data-actions flex-item-bottom " id="add-surgeon-popup">
    <button class="button hint green js-add-select-search" id="add-surgeon-btn" type="button">
      <i class="oe-i plus pro-theme"></i>
    </button><!-- popup to add data to element -->
  </div>
</div>
<style>.Element_OphTrOperationnote_Surgeon{min-height: 54px !important;}</style>

<?php $surgeons = $element->surgeons; ?>

<script type="text/javascript">
  $(document).ready(function () {
    new OpenEyes.UI.AdderDialog({
      openButton: $('#add-surgeon-btn'),
      itemSets: [
        new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
            array_map(function ($item) {
                return ['label' => $item->first_name . ' ' . $item->last_name,
                    'id' => $item->id];
            },
            $surgeons)
        ) ?>, {'header':'Surgeon', 'id':'surgeon_id'}),
        new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
            array_map(function ($item) {
                return ['label' => $item->first_name . ' ' . $item->last_name,
                    'id' => $item->id];
            },
            $surgeons)
        ) ?>, {'header':'Assistant', 'id':'assistant_id'}),
        new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
            array_map(function ($item) {
                return ['label' => $item->first_name . ' ' . $item->last_name,
                    'id' => $item->id];
            },
            $surgeons)
        ) ?>, {'header':'Supervising Surgeon', 'id':'supervising_surgeon_id'})
      ],
      onReturn: function (adderDialog, selectedItems) {
        for (i in selectedItems) {
          var id = selectedItems[i]['id'];
          var $selector = $('#<?=\CHtml::modelName($element)?>_'+selectedItems[i]['itemSet'].options['id']);
          $selector.val(id);
        }
        return true;
      }
    });
  });
</script>
