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

<div class="element-fields full-width">
  <table class="cols-10 last-left">
    <colgroup>
      <col class="cols-5">
      <col class="cols-5">
    </colgroup>
    <thead>
    <tr>
      <th><?php echo CHtml::encode($element->getAttributeLabel('selected_site_id')) ?></th>
      <th><?php echo CHtml::encode($element->getAttributeLabel('theatre_id')) ?></th>
        <?php if ($render_date): ?>
          <th>Date</th><?php endif; ?>
    </tr>
    </thead>
    <tbody>
    <tr class="col-gap">
      <td>
          <?php
          echo $form->dropDownList(
              $element,
              'site_id',
              CHtml::listData(OphTrOperationbooking_Operation_Theatre::getSiteList($element->theatre_id), 'id',
                  'short_name'),
              array('nowrapper' => true, 'empty' => '- None -', 'class' => 'cols-full'),
              false);
          ?>
      </td>
      <td>
          <?php
          if (array_key_exists('OphTrOperationbooking',
                  Yii::app()->modules) && in_array('ophtroperationbooking_operation_theatre',
                  Yii::app()->db->getSchema()->getTableNames())) {
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
                      array('nowrapper' => true, 'class' => 'cols-full'),
                      false);
              } else {
                  echo $form->dropDownList(
                      $element,
                      'theatre_id',
                      CHtml::listData($getTheatreData, 'id', 'name'),
                      array('nowrapper' => true, 'empty' => '- None -', 'class' => 'cols-full'),
                      false);
              }
          }
          ?>
      </td>

        <?php if ($render_date): ?>
          <td>
              <?php
              echo $form->datePicker($this->event, 'event_date',
                  array('maxDate' => 'today'),
                  array(
                      'style' => 'margin-left:8px',
                      'nowrapper' => true,
                  ),
                  array(
                      'label' => 2,
                      'field' => 2,
                  )
              );
              ?>
          </td>
        <?php endif; ?>
    </tr>
    </tbody>
  </table>
</div>
<script type="text/javascript">
  $(document).ready(function () {
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
