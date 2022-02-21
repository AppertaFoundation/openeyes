<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * @var \OEModule\PatientTicketing\services\PatientTicketing_QueueSet $queueset
 * @var \OEModule\PatientTicketing\services\PatientTicketing_QueueSetCategory $category
 * @var \OEModule\PatientTicketing\services\PatientTicketing_QueueSetCategoryService $qsc_svc
 * @var int $cat_id
 */
?>

<?php
$queueset_id = $queueset ? $queueset->getId() : null;
$qsc_svc = Yii::app()->service->getService($this::$QUEUESETCATEGORY_SERVICE);
$queueset_list = $qsc_svc->getCategoryQueueSetsList($category, Yii::app()->user->id);
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'ticket-filter',
    'enableAjaxValidation' => false,
    'method' => 'get',
    'action' => ["/{$this->module->id}/default/", "reset_filters" => 1],
));
?>
<input type="hidden" name="sort_by" id="ticket_sort_by" value="<?= Yii::app()->getRequest()->getParam('sort_by', '');?>">
<input type="hidden" name="sort_by_order" id="ticket_sort_by_order" value="<?= Yii::app()->getRequest()->getParam('sort_by_order', '');?>">
<div class="oe-popup-wrap js-wrap-virtual-clinic" style="display: none">
  <div class="oe-popup">
    <div class="title">Change Virtual Clinic</div>
    <div class="close-icon-btn">
      <i class="oe-i remove-circle pro-theme" id="close-btn"></i>
    </div>
    <div class="oe-popup-content previous-elements">
      <input type="hidden" id="cat_id" name="cat_id" value="<?= $cat_id; ?>"/>
        <?=\CHtml::hiddenField('queueset_id', ($queueset ? $queueset->getId() : null)) ?>
      <ul class="btn-list">
            <?php foreach ($queueset_list as $id => $item) { ?>
            <li id="<?= $id ?>" class="<?= $queueset && (integer)$queueset_id === $id ? 'selected' : '' ?>">
                <?= $item ?>
            </li>
            <?php } ?>
      </ul>
    </div>
  </div>
</div>


<?php
$this->endWidget(); ?>
<script>
  $(document).on('click', '#js-virtual-clinic-btn', function (e) {
    e.preventDefault();
    $('.js-wrap-virtual-clinic').show();
  });

  $(document).on('click', '.oe-popup .close-icon-btn', function (e) {
    e.preventDefault();
    $('.js-wrap-virtual-clinic').hide();
  });

  $(".btn-list").ready(function () {
    $(".btn-list").on('click' , 'li' , function (event) {
      var $ticketFilter = $('#ticket-filter');
      $ticketFilter.find('input[name="queueset_id"]').val($(this).attr('id'));
      $ticketFilter.submit();
    });
  });
</script>

