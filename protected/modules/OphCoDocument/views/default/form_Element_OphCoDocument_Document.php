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
    <input type="hidden" id="removed-docs" name="removed-docs" value="">
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
                        <input type="radio" value="single" id="upload_single"
                               name="upload_mode"
                            <?= $element->single_document_id || (!$element->right_document_id && !$element->left_document_id) ? "checked" : ""; ?>
                            <?= ($element->left_document_id || $element->right_document_id ? ' disabled' : '') ?>>
                               Single file
                    </label> <label class="inline highlight ">
                        <input type="radio" name="upload_mode"
                               value="double"
                            <?= ($element->left_document_id || $element->right_document_id ? "checked" : ""); ?>
                            <?= ($element->single_document_id ? " disabled" : ""); ?>>
                               Right/Left sides
                    </label></td>
            </tr>
            </tbody>
        </table>
        <div class="element-fields flex-layout flex-top col-gap">
            <div class="cols-11">
                <div id="document-comments" data-comment-button="#document_comment_button"
                     class="cols-full js-comment-container "
                     style="<?php if ($element->comment == null) echo 'display:none' ?>">
                    <div class="comment-group flex-layout flex-left " style="padding-top:5px">
                        <?php
                        echo $form->textArea($element,
                            'comment',
                            array('rows' => '1','nowrapper' => true),
                            false,
                            ['placeholder' => 'Comments' , 'class' => 'autosize ']);
                        ?>
                        <i class="oe-i remove-circle small-icon pad-left  js-remove-add-comments"></i>
                    </div>
                </div>
            </div>
            <div class="cols-1 last-right">
                <div class="add-data-actions">
                    <button id="document_comment_button"
                            class="button js-add-comments"
                            data-comment-container="#document-comments"
                            type="button"
                            style="visibility:<?php if ($element->comment != null) echo 'hidden' ?>">

                        <i class="oe-i comments small-icon"></i>
                    </button>
                </div>
            </div>

        </div>




        <hr class="divider">

        <div id="single_document_uploader" class="data-group js-document-upload-wrapper"
            <?= (!$element->single_document_id &&
            ($element->right_document_id || $element->left_document_id) ? 'style="display:none"' : ''); ?>
        >
            <table class="last-left cols-full">
                <colgroup>
                    <col class="cols-full">
                </colgroup>
                <thead></thead>
                <tbody>
                <tr class="valign-top">
                    <td>
                        <div class="upload-box"
                             id="single_document_id_row" <?= $element->single_document_id ? 'style="display:none"' : ''; ?>>
                            <label for="Document_single_document_row_id" id="upload_box_single_document"
                                   class="upload-label">
                                <i class="oe-i download no-click large"></i>
                                <br>
                                <span class="js-upload-box-text">Click to select file or DROP here</span>
                            </label>
                            <input autocomplete="off"
                                   type="file"
                                   name="Document[single_document_id]"
                                   id="Document_single_document_row_id"
                                   style="display:none;"
                                   class="js-document-file-input"
                            >
                        </div>

                        <?php $this->generateFileField($element, 'single_document'); ?>

                        <div class="flex-layout flex-right js-remove-document-wrapper" <?= (!$element->single_document_id ? 'style="display:none"' : ''); ?>>
                            <?php if($element->single_document_id): ?>
                            <input type="hidden" id="original-doc" name="original-doc" value="<?= $element->single_document_id ?>">
                            <?php endif; ?>
                            <button class="hint red" data-side="single">remove uploaded file</button>
                        </div>

                        <?= CHtml::activeHiddenField($element, 'single_document_id', ['class' => 'js-document-id']); ?>

                    </td>
                </tr>
                </tbody>
            </table>
        </div>


        <div id="double_document_uploader" class="data-group js-document-upload-wrapper"
            <?= ($element->left_document_id || $element->right_document_id ? '' : 'style="display:none"'); ?> >

            <table class="last-left cols-full">
                <colgroup>
                    <col class="cols-half" span="2">
                </colgroup>
                <thead>
                <tr>
                    <th>RIGHT</th>
                    <th>LEFT</th>
                </tr>
                </thead>
                <tbody>
                <tr class="valign-top">
                    <td>
                        <div class="upload-box"
                             id="right_document_id_row" <?= $element->right_document_id ? 'style="display:none"' : ''; ?>>
                            <label for="Document_right_document_row_id" id="upload_box_right_document"
                                   class="upload-label">
                                <i class="oe-i download no-click large"></i>
                                <br>
                                <span class="js-upload-box-text">Click to select file or DROP here</span>
                            </label>
                            <input autocomplete="off"
                                   type="file"
                                   name="Document[right_document_id]"
                                   id="Document_right_document_row_id"
                                   style="display:none;"
                                   class="js-document-file-input"
                            >
                        </div>
                        <?php $this->generateFileField($element, 'right_document'); ?>

                        <div class="flex-layout flex-right js-remove-document-wrapper"
                            <?= ($element->right_document_id ? '' : 'style="display:none"'); ?> >
                            <?php if($element->right_document_id): ?>
                                <input type="hidden" id="original-right-doc" value="<?= $element->single_document_id ?>">
                            <?php endif; ?>
                            <button class="hint red" data-side="right">remove uploaded file</button>
                        </div>
                        <?= CHtml::activeHiddenField($element, 'right_document_id', ['class' => 'js-document-id']); ?>
                    </td>
                    <td>
                        <div class="upload-box"
                             id="left_document_id_row" <?= $element->left_document_id ? 'style="display:none"' : ''; ?>>
                            <label for="Document_left_document_row_id" id="upload_box_left_document"
                                   class="upload-label">
                                <i class="oe-i download no-click large"></i>
                                <br>
                                <span class="js-upload-box-text">Click to select file or DROP here</span>
                            </label>
                            <input autocomplete="off"
                                   type="file"
                                   name="Document[left_document_id]"
                                   id="Document_left_document_row_id"
                                   style="display:none;"
                                   class="js-document-file-input"
                            >
                        </div>
                        <?php $this->generateFileField($element, 'left_document'); ?>

                        <div class="flex-layout flex-right js-remove-document-wrapper"
                            <?= ($element->left_document_id ? '' : 'style="display:none"'); ?> >
                            <?php if($element->left_document_id): ?>
                                <input type="hidden" id="original-left-doc" value="<?= $element->single_document_id ?>">
                            <?php endif; ?>
                            <button class="hint red" data-side="left">remove uploaded file</button>
                        </div>
                        <?= CHtml::activeHiddenField($element, 'left_document_id', ['class' => 'js-document-id']); ?>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>


        <div class="data-group fade">
            The following file types are accepted: <?php echo implode(', ', $this->getAllowedFileTypes()); ?> (Maximum
            size: <?= $this->getMaxDocumentSize(); ?> MB)
        </div>
    </div>

    <script type="text/template" id="side-selector-popup">
        <table>
            <colgroup>
                <col class="cols-6">
                <col class="cols-6">
            </colgroup>
            <tbody>
            <tr class="col-gap">
                <td>
                    <button id="right-select-button" data-side="right" class="js-side-picker large cols-full">Right
                        (R)
                    </button>
                </td>
                <td>
                    <button id="left-select-button" data-side="left" class="js-side-picker large cols-full">Left (L)
                    </button>
                </td>
            </tr>
            </tbody>
        </table>
    </script>
    <script>
            window.addEventListener("unload", function () {
                let controller = $('.js-document-upload-wrapper').data('controller');
                let removed_docs = $('#removed-docs').val();

                if (controller.options.action === 'cancel' || controller.options.action === '') {

                    $('.js-document-id').each(function () {
                        if ($(this).val() !== "") {
                            $(this).parents('td').find(controller.options.removeButtonSelector).trigger('click');
                        }
                    });

                    removed_docs = $('#removed-docs').val();

                    if (window.location.href.includes('update')) {
                        if ($('#upload_single').prop('checked')) {
                            let original_doc = $('#original-doc').val();
                            removed_docs = removed_docs.replace(original_doc + ';', '');
                        } else {
                            for (let side of ['left', 'right']) {
                                removed_docs = removed_docs.replace($('#original-' + side + '-doc').val() + ';', '');
                            }
                        }
                    }
                }

                if (removed_docs !== "") {
                    $.post('/OphCoDocument/Default/removeDocuments', {
                        doc_ids: removed_docs,
                        YII_CSRF_TOKEN: YII_CSRF_TOKEN
                    });
                }
            });
    </script>