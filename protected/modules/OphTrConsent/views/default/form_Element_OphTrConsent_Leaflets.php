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
$model_name = CHtml::modelName($element);
?>

<div class="element-fields flex-layout full-width" id="<?= $model_name ?>_element">
  <input type="hidden" name="<?= $model_name ?>[present]" value="1"/>
  <table id="<?= $model_name ?>_entry_table" class="cols-7">
    <tbody>
    <?php $leaflets = $element->leaflets;
    foreach ($leaflets as $leaflet_item) { ?>
      <tr>
        <td>
          <input type="hidden" value="<?= $leaflet_item->leaflet_id ?>" name="OphTrConsent_Leaflet[]">
            <?= $leaflet_item->leaflet->name ?>
        </td>
        <td><i class="oe-i trash"></i></td>
      </tr>
    <?php } ?>
    </tbody>
  </table>
  <div class="add-data-actions flex-item-bottom" id="consent-form-leaflets-popup" style="display:">
    <button class="button hint green js-add-select-search" id="add-leaflet-btn" type="button">
      <i class="oe-i plus pro-theme"></i>
    </button>
  </div>
</div>

<script type="text/javascript">
  $(function () {
    var $table = $('#Element_OphTrConsent_Leaflets_entry_table');
    $table.on('click', 'i.trash', function (e) {
      e.preventDefault();
      $(e.target).parents('tr').remove();
    });

    new OpenEyes.UI.AdderDialog({
      openButton: $('#add-leaflet-btn'),
      itemSets: [new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
          array_map(function ($leaflet) {
              return ['label' => $leaflet->name, 'id' => $leaflet->id];
          },
              OphTrConsent_Leaflet::model()->findAllByCurrentFirm($element->leafletValues))
      ) ?>, {multiSelect: true})],
      onReturn: function (dialog, selectedItems) {
        $.each(selectedItems, function (key, item) {
          var leaflet_id = item['id'];
          var leaflet_display = item['label'];
          var $tr = $("<tr>"),
            $td = $("<td>"),
            $td_action = $("<td>", {"class": "right"}),
            $i_remove = $("<i>", {"class": "oe-i trash"}),
            $hidden = $("<input>", {"type": "hidden", "name": "OphTrConsent_Leaflet[]", "value": leaflet_id});
          $td.text(leaflet_display);
          $td.append($hidden);
          $td_action.append($i_remove);
          $table.find('tbody').append($tr.append($td).append($td_action));
        });
        return true;
      }
    });
  })
</script>