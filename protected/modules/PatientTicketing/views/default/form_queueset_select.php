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

    <div <?php if (!$queueset) { ?> style="display: none;"<?php } ?>>
        <button class="button blue hint" id="js-virtual-clinic-btn">Change <?= $category->name ?></button>
    </div>
    <div id="js-virtual-clinic" class="oe-popup-wrap" style="display: none"><!-- generic oe-popup -->
        <div class="oe-popup">
            <div class="title">Select <?= $category->name ?>:</div>
            <div class="close-icon-btn"><i class="oe-i remove-circle pro-theme"></i></div>
            <div class="oe-popup-content previous-elements">
                <ul class="oe-btn-list">
                    <?php foreach ($queueset_list as $clinic): ?>
                        <li><?= $clinic ?></li>
                    <?php endforeach; ?>
                </ul>

            </div>

        </div>
    </div>

    <div id="queueset-select-toggle-wrapper"<?php if (!$queueset) {?> style="display: none;"<?php }?>><button class="secondary small" id="queueset-select-toggle">Change <?=$category->name?></button></div>
    <div id="queueset-form" class="row field-row"<?php if ($queueset) { ?> style="display: none;"<?php } ?>>
        <div class="title">Select <?= $category->name ?>:</div>
        <div class="large-3 column"><?php echo CHtml::dropDownList('queueset_id', ($queueset ? $queueset->getId() : null), $queueset_list, array('empty' => '- Please Select -')) ?></div>
        <div class="large-2 column end">
            <input type="hidden" name="cat_id" value="<?= $cat_id; ?>"/>
            <input type="submit" class="secondary small" value="Select"/>
            <?php if ($queueset) { ?>
                <button class="small warning" id="queueset-select-cancel">Cancel</button>
            <?php } ?>
        </div>
    </div>
<?php
$this->endWidget();?>
<script>
    $(document).on('click', '#js-virtual-clinic-btn', function(e) {

        e.preventDefault();
        var returnUrl = window.location.href;

        new OpenEyes.UI.Dialog($.extend({}, {
            url: baseUrl + '/site/changesiteandfirm',
            width: 500,
            data: {
                returnUrl: returnUrl,
                patient_id: window.OE_patient_id || null
            }
        })).open();
    });

</script>

