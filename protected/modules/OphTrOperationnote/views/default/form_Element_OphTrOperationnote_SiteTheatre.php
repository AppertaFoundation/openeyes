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
<?php $render_date = $form && (($this->action->id === strtolower($this::ACTION_TYPE_CREATE)) || $this->checkAdminAccess()) ?>

<div class="element-fields full-width flex-layout">
  <table class="cols-10 last-left">
    <colgroup>
      <col class="cols-4">
      <col class="cols-4">
      <col class="cols-4">
    </colgroup>
    <tbody>
    <tr class="col-gap">
      <td>
            <?php
            echo $form->dropDownList(
                $element,
                'site_id',
                CHtml::listData(
                    OphTrOperationbooking_Operation_Theatre::getSiteList($element->theatre_id),
                    'id',
                    'short_name'
                ),
                array(
                    'nowrapper' => false,
                    'empty' => '- None -',
                    'class' => 'cols-full',
                    'data-prefilled-value' => $template_data['site_id'] ?? '',
                    'data-test' => 'site'
                ),
                false,
                array('label' => 2, 'field' => 8)
            );
            ?>
      </td>
      <td>
            <?php
            if (
                array_key_exists(
                    'OphTrOperationbooking',
                    Yii::app()->modules
                ) && in_array(
                    'ophtroperationbooking_operation_theatre',
                    Yii::app()->db->getSchema()->getTableNames()
                )
            ) {
                $siteId = ($element->site_id) ? $element->site_id : Yii::app()->session['selected_site_id'];
                $getTheatreData = OphTrOperationbooking_Operation_Theatre::model()->findAll(array(
                  'condition' => 'active=1 and site_id=' . $siteId,
                  'order' => 'name',
                ));

                if (count($getTheatreData) == 1) {
                    echo $form->dropDownList(
                        $element,
                        'theatre_id',
                        CHtml::listData($getTheatreData, 'id', 'name'),
                        array(
                            'nowrapper' => false,
                            'class' => 'cols-full',
                            'data-prefilled-value' => $template_data['theatre_id'] ?? '',
                            'data-test' => 'theatre'
                        ),
                        false,
                        array('label' => 2, 'field' => 8)
                    );
                } else {
                    echo $form->dropDownList(
                        $element,
                        'theatre_id',
                        CHtml::listData($getTheatreData, 'id', 'name'),
                        array(
                            'nowrapper' => false,
                            'empty' => '- None -',
                            'class' => 'cols-full',
                            'data-prefilled-value' => $template_data['theatre_id'] ?? '',
                            'data-test' => 'theatre'
                        ),
                        false,
                        array('label' => 2, 'field' => 8)
                    );
                }
            }
            ?>
      </td>
    </tr>
    </tbody>
  </table>
  <div class="add-data-actions flex-item-bottom " id="change-site-theatre-popup">
    <button class="button hint green js-add-select-search" id="change-site-theatre-btn" type="button">
      <i class="oe-i plus pro-theme"></i>
    </button><!-- popup to add data to element -->
  </div>
</div>
<script type="text/javascript">
  $(document).ready(function () {
    new OpenEyes.UI.AdderDialog({
      openButton: $('#change-site-theatre-btn'),
      itemSets:[
        new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
            array_map(function ($item) {
                return ['label' => $item->short_name, 'id' => $item->id];
            },
            OphTrOperationbooking_Operation_Theatre::getSiteList($element->theatre_id))
        ) ?>,
          {'header':'site', 'id':'site_id'})
      ],
      onReturn: function (adderDialog, selectedItems) {
        for (i in selectedItems) {
          var id = selectedItems[i]['id'];
          var $selector = $('#<?=\CHtml::modelName($element)?>_'+selectedItems[i]['itemSet'].options['id']);
          $selector.val(id);
          $selector.trigger('change');
        }
        return true;
      }
    });
    $('#Element_OphTrOperationnote_SiteTheatre_site_id').change(function () {
      $.ajax({
        type: 'GET',
        url: '/OphTrOperationnote/Default/getTheatreOptions',
        data: {
          siteId: $(this).val()
        },
        success: function (result) {
          $('#Element_OphTrOperationnote_SiteTheatre_theatre_id').html(result);
        }
      });
    });
  });
</script>
<style>.Element_OphTrOperationnote_SiteTheatre{min-height: 54px !important;}</style>
