<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use OEModule\OphCiExamination\models\FreehandDraw;

if (!isset($values)) {
    $values = array(
        'id' => $entry->id,
        'protected_file_id' => $entry->protected_file_id,
        // If the protected file is missing, use a single pixel placeholder image in its place to prevent the event from crashing.
        // This at least allows comments to be viewed, even if the image has been lost
        'template_url' => $entry->protected_file ?
            $entry->protected_file->getDownloadURL() :
            'data:' . FreehandDraw::SINGLE_PIXEL_IMAGE_DATA_PLACEHOLDER,
        'data_url' => $entry->protected_file ?
            $entry->protected_file->getFileAsDataUrl() :
            'data:' . FreehandDraw::SINGLE_PIXEL_IMAGE_DATA_PLACEHOLDER,
        'filename' => $entry->protected_file ? $entry->protected_file->name : '! File Missing !',
        'full_name' => $entry->protected_file ? $entry->protected_file->user->fullName : '',
        'date' => \Helper::convertMySQL2NHS($entry->last_modified_date),
        'comments' => $entry->comments,
    );
}
?>

<div class="freedraw-group" id="annote-template-view-<?= $row_count; ?>" data-key="<?= $row_count; ?>">
    <div class="flex-t col-gap">
        <div class="cols-6">
            <img id="js-img-preview-<?= $row_count; ?>" src="<?= $values['template_url']; ?>" width="100%">
        </div>
        <table class="cols-6">
            <tbody>
            <tr>
                <td><?= $values['filename']; ?></td>
                <td><i class="oe-i trash"></i></td>
            </tr>
            <tr>
                <td>
                    <i class="oe-i comments-who user-comment-<?= $row_count; ?> small pad-right js-has-tooltip"
                       data-tooltip-content="User comment"
                       style="display:<?= (!$values['comments'] ? 'none' : 'inline-flex') ?>"
                    ></i>
                    <span id="user-comment-<?= $row_count; ?>" class="user-comment">
                        <?= \OELinebreakReplacer::replace($values['comments']); ?>
                    </span>
                    <div class="js-input-comments-wrapper cols-full" style="display: none;">
                        <div class=" flex-layout flex-left">
                            <textarea
                                placeholder="Comments"
                                autocomplete="off"
                                rows="1"
                                class="cols-full js-input-comments autosize"
                                name="<?= $field_prefix ?>[comments]"
                                id="comments-field-<?= $row_count; ?>"><?= \CHtml::encode($values['comments']); ?></textarea>
                            <i class="oe-i remove-circle small-icon pad-left js-remove-add-comments"></i>
                        </div>
                    </div>
                </td>
                <td>
                    <button class="button js-add-comments"
                            data-comment-container="#comments-field-<?= $row_count; ?>"
                    >
                        <i class="oe-i comments small-icon"></i>
                    </button>
                </td>
            </tr>
            <tr>
                <td><?= $values['full_name']; ?> <?= $values['date']; ?></td>
                <td>
                    <button class="blue hint js-image-annotate" data-annotate-row-index="<?= $row_count; ?>"
                            data-test="annotate-freehand-drawing-image-btn">Annotate image
                    </button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="freedraw-group js-annotate-wrapper" style="display:none" id="annotate-wrapper-<?= $row_count; ?>" data-key="<?= $row_count; ?>">
    <div class="flex-t">
        <table class="cols-full">
            <tbody>
            <tr>
                <td><?= $values['filename']; ?></td>
                <td><i class="oe-i trash" data-test="remove-entry"></i></td>
            </tr>
            <tr>
                <td>Comments</td>
                <td>
                    <?php /* textarea value will be copied to the other <textarea> and that one will be POSTed */ ?>
                    <textarea
                        placeholder="Comments"
                        autocomplete="off"
                        rows="1"
                        class="cols-full js-annotate-input-comments"
                        id="annote-comments-field-<?= $row_count; ?>"
                        key="<?= $row_count; ?>"
                    ></textarea>
                </td>
            </tr>
            <tr>
                <td><?= $values['full_name']; ?> <?= $values['date']; ?></td>
                <td class="js-annotation-actions-container" style="display: none;">
                    <button
                        class="green hint js-save-annotation"
                        data-test="save-freehand-drawing-annotation-btn"
                        data-annotate-row-index="<?= $row_count; ?>">Save annotation
                    </button>
                    <button
                        data-annotate-row-index="<?= $row_count; ?>"
                        data-template-url="<?= $values['template_url']; ?>"
                        data-test="cancel-freehand-drawing-annotation-btn"
                        class="red hint js-cancel-annotation">Clear & cancel annotations
                    </button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <hr class="divider">

    <div
        data-key="<?= $row_count; ?>"
        data-template-url="<?= $values['template_url']; ?>"
        class="oe-annotate-image js-key-<?= $row_count; ?>"
        id="js-annotate-image-<?= $row_count; ?>"
    >

        <input type="hidden" name="<?= $field_prefix ?>[id]" value="<?= $values['id'] ?>"/>
        <input type="hidden" name="<?= $field_prefix ?>[protected_file_id]"
               value="<?= $values['protected_file_id'] ?>"/>
        <input type="hidden" class="js-image-data-<?= $row_count; ?>" name="<?= $field_prefix ?>[image][data]"
               value="<?= $values['data_url']; ?>"/>
        <input type="hidden" class="js-image-name-<?= $row_count; ?>" name="<?= $field_prefix ?>[image][name]"
               value="<?= $values['filename']; ?>"/>

        <div class="toolbox">
            <button name="manipulate" class="tool-manipulate js-tool-btn">
                <svg viewBox="0 0 57 19" class="tool-icon">
                    <use xlink:href="<?= $annotate_tools_icon_url; ?>#manipulate"></use>
                </svg>
            </button>
            <button name="freedraw" class="tool-btn js-tool-btn">
                <svg viewBox="0 0 19 19" class="tool-icon">
                    <use xlink:href="<?= $annotate_tools_icon_url; ?>#freedraw"></use>
                </svg>
            </button>
            <button name="circle" class="tool-btn js-tool-btn">
                <svg viewBox="0 0 19 19" class="tool-icon">
                    <use xlink:href="<?= $annotate_tools_icon_url; ?>#circle"></use>
                </svg>
            </button>
            <button name="pointer" class="tool-btn js-tool-btn">
                <svg viewBox="0 0 19 19" class="tool-icon">
                    <use xlink:href="<?= $annotate_tools_icon_url; ?>#pointer"></use>
                </svg>
            </button>
            <div class="line-width">
                <div class="js-line-width"><small>Line width: 3</small></div>
                <input type="range" min="1" max="5" value="3" class="cols-full js-tool-line-width">
            </div>
            <hr>
            <svg class="colors" viewBox="0 0 150 25" width="100%" height="25px">
                <rect class="selected" x="1" y="2" width="20" height="20" rx="10" fill="#c00"></rect>
                <rect x="26" y="2" width="20" height="20" rx="10" fill="#0c0"></rect>
                <rect x="51" y="2" width="20" height="20" rx="10" fill="#09f"></rect>
                <rect x="76" y="2" width="20" height="20" rx="10" fill="#ff0"></rect>
                <rect x="101" y="2" width="20" height="20" rx="10" fill="#e50"></rect>
                <rect x="126" y="2" width="20" height="20" rx="10" fill="#f5b"></rect>
            </svg>
            <svg class="colors" viewBox="0 0 150 25" width="100%" height="25px">
                <rect x="1" y="2" width="20" height="20" rx="10" fill="#000"></rect>
                <rect x="26" y="2" width="20" height="20" rx="10" fill="#888"></rect>
                <rect x="51" y="2" width="20" height="20" rx="10" fill="#fff"></rect>
                <rect x="76" y="2" width="20" height="20" rx="10" fill="#600"></rect>
                <rect x="101" y="2" width="20" height="20" rx="10" fill="#060"></rect>
                <rect x="126" y="2" width="20" height="20" rx="10" fill="#006"></rect>
            </svg>
            <hr>
            <input type="text" class="js-label-text cols-full" placeholder="Label text...">
            <button name="text" class="js-tool-btn">Add label</button>
            <hr>
            <button name="erase" class="js-tool-btn">Erase selected</button>
            <button name="clear-all" class="js-tool-btn js-clear-all">Clear all</button>
        </div>

        <div class="canvas-js js-not-initialized"></div>
    </div>

</div>
