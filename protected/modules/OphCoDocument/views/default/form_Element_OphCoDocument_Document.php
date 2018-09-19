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
<div class="element-fields full-width flex-layout">
    <div class="cols-11">
        <table class="cols-6 last-left">
            <tbody>
            <tr>
                <td>Event Sub Type</td>
                <td>
                    <?php echo $form->dropDownList($element, 'event_sub_type',
                        CHtml::listData(OphCoDocument_Sub_Types::model()->findAll('is_active=1 ORDER BY display_order'), 'id', 'name'),
                        array('nowrapper' => true),
                        array(),
                        array(
                            'label' => 0,
                            'field' => 2,
                        )); ?>
                </td>
            </tr>
            <tr>
                <td>Upload</td>
                <td>
                    <label class="inline highlight ">
                        <input type="radio" value="single"
                               name="upload_mode" <?php if ($element->single_document_id > 0) {
                            echo "checked";
                        } ?>>Single file
                    </label> <label class="inline highlight ">
                        <input type="radio" name="upload_mode"
                               value="double" <?php if ($element->left_document_id > 0 || $element->right_document_id > 0) {
                            echo "checked";
                        } ?>>Right/Left sides
                    </label></td>
            </tr>
            </tbody>
        </table>
        <hr class="divider">
        <div id="single_document_uploader">
            <div class="cols-12 column" id="single_document_id_row">
                <?php $this->generateFileField($element, 'single_document'); ?>
            </div>
        </div>
        <div id="double_document_uploader" class="data-group">

            <table class="last-left cols-full">
                <thead>
                <tr>
                    <th>
                        RIGHT
                    </th>
                    <th>
                        LEFT
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        <div class="upload-box" id="right_document_id_row">
                            <?php $this->generateFileField($element, 'right_document'); ?>
                        </div>
                    </td>
                    <td>
                        <div class="upload-box" id="left_document_id_row">
                            <?php $this->generateFileField($element, 'left_document'); ?>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div id="showUploadStatus"
             style="height:10px;color:#ffffff;font-size:8px;text-align:right;width:0px;background-color:#22b24c"></div>
        <?php
        foreach (array('single_document_id', 'left_document_id', 'right_document_id') as $index_key) {
            if ($element->{$index_key} > 0) {
                echo "<input type=\"hidden\" name=\"Element_OphCoDocument_Document[" .
                    $index_key . "]\" id=\"Element_OphCoDocument_Document_" .
                    $index_key . "\" value=\"" . $element->{$index_key} .
                    "\">";
            }
        }
        ?>
        <div id="document-comments" data-comment-button="#document_comment_button"
             class="cols-full js-comment-container"
             style="<?php if ($element->comment == null) echo 'display:none' ?>">
            <div class="comment-group flex-layout flex-left " style="padding-top:5px">
                <?php
                echo $form->textArea($element,
                    'comment',
                    array('rows' => '1', 'class' => 'autosize cols-full column', 'nowrapper' => true),
                    false,
                    ['placeholder' => 'Comments']);
                ?>
                <i class="oe-i remove-circle small-icon pad-left  js-remove-add-comments"></i>
            </div>
        </div>
        <div class="data-group fade">
            The following file types are accepted: <?php echo implode(', ', $this->getAllowedFileTypes()); ?> (Maximum
            size: <?= $this->getMaxDocumentSize(); ?> MB)
        </div>
    </div>
    <div class="add-data-actions flex-item-bottom">
        <button id="document_comment_button"
                class="button js-add-comments"
                data-comment-container="#document-comments"
                type="button"
                style="visibility:<?php if ($element->comment != null) echo 'hidden' ?>">

            <i class="oe-i comments small-icon"></i>
        </button>

    </div>
