<?php
/**
 * OpenEyes.
 *
 * (C) Copyright Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 *
 * @var Attachment $this
 */
?>
<?php $api = Yii::app()->moduleAPI->get('OphGeneric'); ?>

<div class="attachment" data-is-examination="<?= $this->is_examination ?>">
    <?php
    if ($this->element) :
        echo \CHtml::activeHiddenField($this->element, "id");
    endif; ?>

    <!-- BOTH sides -->
    <?php if (isset($this->event_attachments['BOTH'])) { ?>
        <div class="element-both-eyes">
            <!-- Add attachment icon -->
            <?php if ($this->allow_attach) : ?>
                <?php if ($this->element && $this->element->getFormTitle() !== 'Attachment') : ?>
                    <div class="flex-layout flex-left">Attachments</div>
                <?php endif ?>
                <a class="add-attachment flex-layout flex-right">
                    <i class="oe-i plus-circle small disabled"></i>
                </a>
            <?php endif; ?>
            <div class="flex-layout flex-center">

                <?php $this->render(
                    'Attachment_entry',
                    ['attachments' => $this->event_attachments['BOTH'], 'image_size' => $this->image_size, 'size' => 'small', 'eye_side' => 'Both']
                ); ?>
            </div>
            <?php if ($this->show_titles) { ?>
                <div class="flex-layout flex-center">
                    <h1 class="priority-text"><?= $this->group_titles['BOTH'] ?></h1>
                </div>
            <?php } ?>
        </div>
    <?php } ?>

    <!-- EACH side -->
    <?php if (isset($this->event_attachments['LEFT']) || isset($this->event_attachments['RIGHT'])) { ?>
        <div class="element-fields element-eyes">
            <?php foreach (array('left' => 'right', 'right' => 'left') as $page_side => $eye_side) : ?>
                <div class="js-element-eye <?= $eye_side ?>-eye column <?= $page_side ?> side"
                    data-side="<?= $eye_side ?>">

                    <?php $attachment_exists = isset($this->event_attachments[strtoupper($eye_side)]); ?>
                    <div class="flex-layout flex-center">
                        <?php if ($attachment_exists) { ?>
                            <?php $this->render(
                                'Attachment_entry',
                                [
                                    'attachments' => $this->event_attachments[strtoupper($eye_side)],
                                    'image_size' => $this->image_size,
                                    'size' => 'small',
                                    'eye_side' => $eye_side
                                ]
                            );
                        } ?>
                    </div>
                    <?php if ($attachment_exists && $this->show_titles) { ?>
                        <h1 class="priority-text"><?= $this->group_titles[strtoupper($eye_side)] ?></h1>
                    <?php } ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php } ?>
    <div class="attachment-container"></div>
</div>
