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
 */
?>

<?php foreach ($attachments as $group) : ?>
    <div class="group flex-layout flex-center break" style="padding-right: 10px">
        <!-- Display the GROUPED folder thumbnail -->
        <?php if (count($group) > 1) {
            // set attachments info in the group thumbnail img for JS to create the Dialog
            $group_data = [];
            foreach ($group as $attachment) {
                $group_data[] = [
                    'id' => $attachment['attachmentData']->id,
                    'type' => $attachment['attachmentData']->attachment_type,
                    'title_short' => strtoupper($eye_side[0]) . " - " . $attachment['attachmentType']->title_short,
                    'title_full' => $attachment['attachmentType']->title_full,
                    'mime' => $attachment['attachmentData']->mime_type,
                    'group_id' => $attachment['group_id'],
                ];
            } ?>

            <img class="js-thumbnail-group-attachment"
                 src="http://icons.iconarchive.com/icons/dtafalonso/yosemite-flat/128/Folder-icon.png"
                 width="<?= $image_size ?>px" height="<?= $image_size ?>px"
                 data-group='<?= json_encode($group_data) ?>'
                 data-group_id='<?= $group_data[0]['group_id']?>'
                 data-full-title="<?= ""/* TODO */ ?>"
            />
            <!-- Display ungrouped attachments -->
        <?php } else {
            foreach ($group as $attachment) : ?>
                <div class="image-hover" style="width:<?= $image_size ?>px; font-size:70%; ">
                    <!-- allow removal only if it was added by the user -->
                    <a class="remove-attachment"
                       style="<?= $attachment['attachmentData']->system_only_managed && $this->allow_attach ? '' : 'display: none;' ?>">
                        <i class="oe-i remove-circle small"></i>
                    </a>

                    <img class="js-small-thumbnail-attachment <?= $attachment['preSelected'] ? $attachment['preSelected'] : '' ?>"
                         src="/Api/attachmentDisplay/view/id/<?= $attachment['attachmentData']->id ?>?attachment=thumbnail_small_blob&mime=image/png"
                         width="<?= $image_size ?>px" height="<?= $image_size ?>px"
                         data-full-title="<?= $attachment['attachmentType']->title_full ?>"
                         data-mime= <?= $attachment['attachmentData']->mime_type ?>
                         data-id="<?= $attachment['attachmentData']->id ?>"
                    />

                    <!-- show short title below the image -->
                    <div class="cols-full" style="text-align:center;">
                        <?= strtoupper($eye_side[0]) . " - " . $attachment['attachmentData']->attachmentType->title_short ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php } ?>
    </div>
<?php endforeach; ?>
