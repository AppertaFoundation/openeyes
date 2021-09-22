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
<div class="element-fields full-width flex-layout">
        <?php echo $form->multiSelectList(
            $element,
            'Drug',
            'drugs',
            'id',
            $this->getPostOpDrugList($element),
            null,
            array('empty' => '- Drugs -', 'label' => 'Drugs'),
            false,
            false,
            null,
            false,
            false,
            array('field' => 10)
        ) ?>

  <div class="add-data-actions flex-item-bottom " id="add-surgeon-popup">
    <button class="button hint green js-add-select-search" id="add-postop-drugs-btn" type="button">
      <i class="oe-i plus pro-theme"></i>
    </button><!-- popup to add data to element -->
  </div>
</div>
<style>
  #div_Element_OphTrOperationnote_PostOpDrugs_Drugs .multi-select-selections li{
    margin-right: 5px;
    margin-bottom: 5px;
  }
</style>
<?php $drugs = $this->getPostOpDrugList($element); ?>
<script type="text/javascript">
  $(document).ready(function () {
    new OpenEyes.UI.AdderDialog({
      openButton: $('#add-postop-drugs-btn'),
      itemSets:[
        new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
            array_map(function ($key, $item) {
                return ['label' => $item, 'id' => $key];
            },
            array_keys($drugs),
            $drugs)
        )?>,
          {'multiSelect': true})],
      onReturn: function (adderDialog, selectedItems) {
        for (i in selectedItems) {
          var id = selectedItems[i]['id'];
          var $selector = $('#div_Element_OphTrOperationnote_PostOpDrugs_Drugs #Drug');
          $selector.val(id);
          $selector.trigger('change');
        }
        return true;
      }
    });
  });
</script>
