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
  <div class="cols-11 flex-layout">
    <textarea id="js-history-input-demo" autocomplete="off" rows="1" class="cols-6" placeholder="History" style="overflow: hidden; word-wrap: break-word; height: 24px;"></textarea>
    <div class="cols-5">
      <div class="data-label">Previous Management</div>
      <div class="data-value">
        <div class="inline-previous-element"
             data-element-type-id="<?= ElementType::model()->findByAttributes(array('class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_Management'))->id ?>"
             data-no-results-text="No previous management recorded"
             data-limit="1"
             data-template-id="previous-management-template">Loading previous management information ...</div>
      </div>
    </div>
  </div>

  <div class="flex-item-bottom">
    <button class="button hint green js-add-select-search">
      <i class="oe-i plus pro-theme"></i>
    </button>
    <!-- popup to add to element is click -->
    <div id="add-to-history" class="oe-add-select-search auto-width" style="bottom: -124px; display: none;">
      <?php $this->renderPartial('OEModule.OphCiExamination.views.default._attributes', array('element' => $element, 'field' => 'description', 'form' => $form))?>
    </div>
  </div>
</div>
<script type="text/html" id="previous-management-template">
    <strong>{{subspecialty}} {{event_date}} ({{last_modified_user_display}} <span class="has-tooltip fa fa-info-circle" data-tooltip-content="This is the user that last modified the Examination event. It is not necessarily the person that originally added the comment."></span>):</strong> {{comments_or_children}}
</script>
<?php Yii::app()->assetManager->registerScriptFile("js/OpenEyes.UI.InlinePreviousElements.js", null, -10); ?>
