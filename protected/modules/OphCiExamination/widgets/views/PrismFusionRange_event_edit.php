<?php
/**
 * (C) Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/** @var \OEModule\OphCiExamination\models\PrismFusionRange $element */
/** @var \OEModule\OphCiExamination\widgets\PrismFusionRange $this */

?>

<?php $model_name = CHtml::modelName($element); ?>
<div class="element-fields full-width flex-layout" id="<?= $model_name ?>_form">
    <div class="cols-11">

        <table class="cols-full">
            <thead>
            <tr class="no-line">
                <th><?= $this->getReadingAttributeLabel('prism_over_eye_id') ?></th>
                <th colspan="4">Near</th>
                <th colspan="4">Distance</th>
                <th><?= $this->getReadingAttributeLabel('correctiontype_id') ?></th>
                <th><?= $this->getReadingAttributeLabel('with_head_posture') ?></th>
                <th><!-- trash --></th>
            </tr>
            </thead>
            <tbody>

            <tr>
                <?= $this->renderEntriesForElement($element->entries) ?>
            </tr>
            </tbody>
        </table>
        <div id="prismfusionrange-comments" class="cols-full js-comment-container"
             data-comment-button="#add-prismfusionrange-popup .js-add-comments"
            <?php
            if (!$element->comments) {
                echo 'style="display: none"';
            }
            ?>
        >
            <!-- comment-group, textarea + icon -->
            <div class="comment-group flex-layout flex-left">
                <textarea id="<?= $model_name ?>_comments"
                          name="<?= $model_name ?>[comments]"
                          class="js-comment-field cols-10"
                          placeholder="Enter comments here"
                          autocomplete="off" rows="1"
                          style="overflow: hidden; word-wrap: break-word; height: 24px;"><?= CHtml::encode($element->comments) ?></textarea>
                <i class="oe-i remove-circle small-icon pad-left  js-remove-add-comments"></i>
            </div>
        </div>
    </div>
    <div class="add-data-actions flex-item-bottom " id="add-prismfusionrange-popup">
        <button class="button js-add-comments"
                type="button"
                data-comment-container="#<?= $model_name ?>_form .js-comment-container"
            <?php if ($element->comments) {
                echo 'style="display: none"';
            }
            ?>
        >
            <i class="oe-i comments small-icon "></i>
        </button>
        <button class="button hint green js-add-select-search" data-adder-trigger="true" type="button">
            <i class="oe-i plus pro-theme"></i>
        </button>
    </div>
    <template class="hidden" data-entry-template="true">
        <?= $this->renderEntryTemplate() ?>
    </template>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        new OpenEyes.UI.ElementController.MultiRow({
            container: document.querySelector('#<?= $model_name ?>_form')
        });
    });
</script>
