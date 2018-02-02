<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use OEModule\OphCiExamination\models\HistoryRisksEntry;

?>

<?php
$model_name = CHtml::modelName($element);
$risks_options = $this->getRiskOptions();
$missing_req_risks = $this->getMissingRequiredRisks();
$required_risk_ids = array_map(function($r) { return $r->id; }, $this->getRequiredRisks());
?>
<script type="text/javascript" src="<?= $this->getJsPublishedPath('HistoryRisks.js') ?>"></script>

<div class="element-fields flex-layout full-width" id="<?= $model_name ?>_element">
  <div class="cols-7">
    <textarea id="js-risks-input-demo" autocomplete="off" rows="1" class="cols-full" placeholder="Risks" style="overflow: hidden; word-wrap: break-word; height: 24px;"></textarea>
  </div>

  <div class="flex-item-bottom">
    <button class="button hint green js-add-select-search" type="button">
      <i class="oe-i plus pro-theme"></i>
    </button>
    <div id="add-to-risks" class="oe-add-select-search auto-width" style="bottom: 61px; display: none;">
      <div class="close-icon-btn"><i class="oe-i remove-circle medium"></i></div>
      <div class="select-icon-btn"><i class="oe-i menu selected"></i></div>
      <button class="button hint green add-icon-btn"><i class="oe-i plus pro-theme"></i></button>
      <table class="select-options">
        <tr>
          <td>
            <div class="flex-layout flex-top flex-left">
              <ul class="add-options" data-multi="true" data-clickadd="false">
                  <?php
                  foreach ($risks_options as $risk) {
                      ?>
                    <li data-str="<?php echo $risk->name; ?>">
                      <span class="restrict-width"><?php echo $risk->name; ?></span>
                    </li>
                  <?php } ?>
              </ul>
            </div>
            <!-- flex layout -->
          </td>
          <td>
            <div class="flex-layout flex-top flex-left">
              <ul class="add-options" data-multi="true" data-clickadd="false">
                  <?php
                  foreach ($missing_req_risks as $risk) {
                      ?>
                    <li data-str="<?php echo $risk->name; ?>">
                      <span class="restrict-width"><?php echo $risk->name; ?></span>
                    </li>
                  <?php } ?>
              </ul>
            </div>
            <!-- flex layout -->
          </td>
        </tr>
      </table>
      <div class="search-icon-btn"><i class="oe-i search"></i></div>
      <div class="search-options" style="display:none;">
        <input type="text" class="cols-full js-search-autocomplete" placeholder="search for option (type 'auto-complete' to demo)">
        <!-- ajax auto-complete results, height is limited -->
      </div>
    </div>
  </div>
    <script type="text/template" class="<?= CHtml::modelName($element).'_entry_template' ?> hidden">
        <?php
        $empty_entry = new \OEModule\OphCiExamination\models\HistoryRisksEntry();
        $this->render(
            'HistoryRisksEntry_event_edit',
            array(
                'entry' => $empty_entry,
                'form' => $form,
                'model_name' => $model_name,
                'field_prefix' => $model_name . '[entries][{{row_count}}]',
                'row_count' => '{{row_count}}',
                'removable' => true,
                'risks' => $risks_options,
                'posted_not_checked' => false,
                'values' => array(
                    'id' => '',
                    'risk_id' => '{{risk_id}}',
                    'risk_display' => '{{risk_display}}',
                    'other' => '{{other}}',
                    'comments' => '{{comments}}',
                    'has_risk' => (string) HistoryRisksEntry::$PRESENT
                )
            )
        );
        ?>
    </script>
</div>


<script type="text/javascript">
    $(document).ready(function() {
        new OpenEyes.OphCiExamination.HistoryRisksController({
          element: $('#<?=$model_name?>_element')
        });
    });
</script>
