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
?>

<?php
$queueset_id = $queueset ? $queueset->getId() : null;
$qsc_svc = Yii::app()->service->getService($this::$QUEUESETCATEGORY_SERVICE);
$queueset_list = $qsc_svc->getCategoryQueueSetsList($category, Yii::app()->user->id);
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'ticket-filter',
    'enableAjaxValidation' => false,
    'method' => 'get',
    'action' => ["/{$this->module->id}/default/"]
));
?>

<div class="oe-popup-wrap" style="display: none">
    <div class="oe-popup">
        <div class="title">Change Virtual Clinic</div>
        <div class="close-icon-btn">
            <i class="oe-i remove-circle pro-theme" id="close-btn"></i>
        </div>
        <div class="oe-popup-content previous-elements">
            <input type="hidden" name="cat_id" value="<?= $cat_id; ?>"/>
            <?php echo CHtml::hiddenField('queueset_id', ($queueset ? $queueset->getId() : null)) ?>
            <ul class="oe-btn-list">
                <?php foreach ($queueset_list as $item) {?>
                    <li><a id="<?php echo $item?>"><?php echo $item ?></a> </li>
                <?php }?>
            </ul>
        </div>
    </div>
</div>


<?php
$this->endWidget(); ?>
<script>
    $(document).on('click', '.button.blue.hint', function (e) {
        e.preventDefault();
        $('.oe-popup-wrap').css('display', 'flex');
    });

    $(document).on('click', '.oe-i.remove-circle.pro-theme', function (e) {
        e.preventDefault();
        $('.oe-popup-wrap').css('display', 'none');
    });

    $(".oe-btn-list").ready(function() {
        $("li").click(function(event) {
           $('#ticket-filter').submit();
        });
    });
</script>

