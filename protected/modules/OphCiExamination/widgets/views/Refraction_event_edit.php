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

/**
 * @var \OEModule\OphCiExamination\models\Element_OphCiExamination_Refraction $element
 * @var \OEModule\OphCiExamination\widgets\Refraction $this
 */
?>
<script type="text/javascript" src="<?= $this->getJsPublishedPath("Refraction.js") ?>"></script>
<?php $model_name = CHtml::modelName($element); ?>
<div class="element-fields element-eyes">
    <?php echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField')); ?>
    <?php foreach (['left' => 'right', 'right' => 'left'] as $page_side => $eye_side) : ?>
        <div class="js-element-eye <?= $eye_side ?>-eye <?= $page_side ?>
          <?= $element->hasEye($eye_side) ? "" : "inactive" ?>"
             data-side="<?= $eye_side ?>"
             id="<?= $model_name ?>_<?= $eye_side ?>_form"
        >
            <div class="active-form"
                 style="<?= !$element->hasEye($eye_side) ? 'display: none;' : '' ?>">
                <div class="remove-side"><i class="oe-i remove-circle small"></i></div>
                <table class="cols-full">
                    <colgroup>
                        <col class="cols-2">
                        <col class="cols-2">
                        <col class="cols-2">
                        <col class="cols-6">
                    </colgroup>
                    <thead>
                    <tr>
                        <th><?= $this->getReadingAttributeLabel('sphere') ?></th>
                        <th><?= $this->getReadingAttributeLabel('cylinder') ?></th>
                        <th><?= $this->getReadingAttributeLabel('axis') ?></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                        <?= $this->renderReadingsForElement($element->{"{$eye_side}_readings"}) ?>
                    </tbody>
                </table>
                <div class="flex-layout">
                    <div class="cols-10">
                        <div id="refraction-<?= $eye_side ?>-comments"
                             class="flex-layout flex-left comment-group js-comment-container"
                             style="<?= !$element->{$eye_side . '_notes'} ? 'display: none;' : '' ?>"
                             data-comment-button="#refraction-<?= $eye_side ?>-comment-button">
                            <?=\CHtml::activeTextArea(
                                $element,
                                $eye_side . '_notes',
                                [
                                    'rows' => 1,
                                    'placeholder' => $element->getAttributeLabel($eye_side . '_notes'),
                                    'class' => 'cols-full js-comment-field',
                                    'style' => 'overflow-wrap: break-word; height: 24px;',
                                ]
                            ) ?>
                            <i class="oe-i remove-circle small-icon pad-left js-remove-add-comments"></i>
                        </div>
                    </div>
                    <div class="add-data-actions flex-item-bottom">
                        <button id="refraction-<?= $eye_side ?>-comment-button"
                                class="button js-add-comments" data-comment-container="#refraction-<?= $eye_side ?>-comments"
                                type="button" style="<?= $element->{$eye_side . '_notes'} ? 'display: none;' : '' ?>">
                            <i class="oe-i comments small-icon"></i>
                        </button>

                        <button class="button hint green" data-adder-trigger="true" type="button">
                            <i class="oe-i plus pro-theme"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="inactive-form" style="<?= $element->hasEye($eye_side) ? 'display: none;' : '' ?>">
                <div class="add-side">
                    <a href="#">
                        Add <?= $eye_side ?> side <span class="icon-add-side"></span>
                    </a>
                </div>
            </div>

            <template class="hidden" data-entry-template="true">
                <?= $this->renderReadingTemplateForSide($eye_side) ?>
            </template>
        </div>

        <script type="text/javascript">
            new OpenEyes.OphCiExamination.RefractionController({
                container: document.querySelector('#<?= $model_name ?>_<?= $eye_side ?>_form'),
                side: '<?= $eye_side ?>'
            });
        </script>
    <?php endforeach; ?>
</div>
