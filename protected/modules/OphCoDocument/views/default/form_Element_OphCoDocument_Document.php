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
                    <?php echo $form->dropDownList(
                        $element,
                        'event_sub_type',
                        CHtml::listData(OphCoDocument_Sub_Types::model()->findAll('is_active=1 ORDER BY display_order'), 'id', 'name'),
                        array('nowrapper' => true),
                        array(),
                        array(
                            'label' => 0,
                            'field' => 2,
                        )
                    ); ?>
                </td>
            </tr>
            <tr>
                <td>Upload</td>
                <td>
                    <label class="inline highlight">
                        <input type="radio" value="single" id="upload_single"
                               name="upload_mode"
                            <?= $element->single_document_id || !$element->hasSidedAttributesSet("AND") ? "checked" : ""; ?>
                            <?= ($element->hasSidedAttributesSet("OR") ? ' disabled' : '') ?>>
                               Single file
                    </label> <label class="inline highlight">
                        <input type="radio" name="upload_mode"
                               value="double"
                            <?= ($element->hasSidedAttributesSet("OR") ? "checked" : ""); ?>
                            <?= ($element->single_document_id || $element->single_comment ? " disabled" : ""); ?>>
                               Right/Left sides
                    </label></td>
            </tr>
            </tbody>
        </table>
        <hr class="divider">
        <div id="single_document_uploader" class="data-group js-document-upload-wrapper"
            <?= (!$element->single_document_id &&
            ($element->hasSidedAttributesSet("OR")) ? 'style="display:none"' : ''); ?>>
            <div id="single-rotate-actions"
                <?= (!$element->single_document_id ||
                $element->single_document->mimetype == "application/pdf" ?
                    'style="display:none"' :
                    ''); ?>>
                <label>Rotate Single Image:</label>
                <i class="oe-i history large pad-left js-change-rotate" onClick="rotateImage(90, 'single');"></i>
                <i class="oe-i history large pad-left js-change-rotate" onClick="rotateImage(-90, 'single');" style="transform: scale(-1, 1);"></i>
                <input type="hidden" value="<?=!empty($element->single_document->rotate) ? $element->single_document->rotate : ''?>" name="single_document_rotate" id="single_document_rotate">
            </div>
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
                                   data-side="single"
                            >
                        </div>

                        <?php $this->generateFileField($element, 'single_document'); ?>

                        <div class="flex-layout flex-right js-remove-document-wrapper" <?= (!$element->single_document_id ? 'style="display:none"' : ''); ?>>
                            <?php if ($element->single_document_id) : ?>
                            <input type="hidden" id="original-doc" name="original-doc" value="<?= $element->single_document_id ?>">
                            <?php endif; ?>
                            <button class="hint red" data-side="single">remove uploaded file</button>
                        </div>

                        <?= CHtml::activeHiddenField($element, 'single_document_id', ['class' => 'js-document-id']); ?>

                    </td>
                </tr>
                </tbody>
            </table>
            <div class="cols-11">
                <div class="js-comment-container flex-layout flex-left"
                     id="document-single-comments"
                     style="display: <?= $element->single_comment || array_key_exists('single_comment', $element->getErrors()) ? 'block;' : 'none;' ?>"
                     data-comment-button="#document_single_comment_button">
                    <?= $form->textArea(
                        $element,
                        'single_comment',
                        array('rows' => '1', 'nowrapper' => true),
                        false,
                        ['placeholder' => 'Comments', 'class' => 'js-comment-field autosize']
                    ); ?>
                    <i class="oe-i remove-circle small-icon pad-left js-remove-add-comments"></i>
                </div>
                <button id="document_single_comment_button"
                        class="button js-add-comments"
                        data-comment-container="#document-single-comments"
                        type="button"
                        data-hide-method="display"
                        style="display: <?= $element->single_comment || array_key_exists('single_comment', $element->getErrors()) ? 'none;' : 'block;' ?>">
                    <i class="oe-i comments small-icon"></i>
                </button>
            </div>
        </div>

        <div id="double_document_uploader" class="data-group js-document-upload-wrapper"
            <?= ($element->hasSidedAttributesSet("OR") ? '' : 'style="display:none"'); ?> >
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
                    <?php
                    foreach (['right', 'left'] as $side) :
                        $document = $side.'_document';
                        $document_id = $side.'_document_id';
                        ?>
                    <td>
                        <div id="<?=$side?>-rotate-actions"
                            <?= (!$element->{$side . '_document_id'} ||
                            $element->{$side."_document"}->mimetype == "application/pdf" ?
                                'style="display:none"'
                                : ''); ?>>
                            <label>Rotate <?=$side?> Image:</label>
                            <i class="oe-i history large pad-left js-change-rotate" onClick="rotateImage(90, '<?=$side?>');"></i>
                            <i class="oe-i history large pad-left js-change-rotate" onClick="rotateImage(-90, '<?=$side?>');" style="transform: scale(-1, 1);"></i>
                            <input type="hidden" value="<?=!empty($element->{$side."_document"}->rotate) ? $element->{$side."_document"}->rotate : ''?>" name="<?=$side?>_document_rotate" id="<?=$side?>_document_rotate">
                        </div>
                        <div class="upload-box"
                             id="<?=$side?>_document_id_row" <?= $element->{$side."_document_id"} ? 'style="display:none"' : ''; ?>>
                            <label for="Document_<?=$side?>_document_row_id" id="upload_box_<?=$side?>_document"
                                   class="upload-label">
                                <i class="oe-i download no-click large"></i>
                                <br>
                                <span class="js-upload-box-text">Click to select file or DROP here</span>
                            </label>
                            <input autocomplete="off"
                                   type="file"
                                   name="Document[<?=$side?>_document_id]"
                                   id="Document_<?=$side?>_document_row_id"
                                   style="display:none;"
                                   class="js-document-file-input"
                                   data-side="<?=$side?>"
                            >
                        </div>
                        <?php $this->generateFileField($element, $side.'_document'); ?>

                        <div class="flex-layout flex-<?=$side?> js-remove-document-wrapper"
                            <?= ($element->{$side."_document_id"} ? '' : 'style="display:none"'); ?> >
                            <input type="hidden" id="original-<?=$side?>-doc" value="<?= $element->{$side."_document_id"} ?>">
                            <button class="hint red" data-side="<?=$side?>">remove uploaded file</button>
                        </div>
                        <?= CHtml::activeHiddenField($element, $side.'_document_id', ['class' => 'js-document-id']); ?>
                            <div class="js-comment-container flex-layout flex-left" id="document-<?= $side ?>-comments"
                                <?= $element->{$side."_comment"} || array_key_exists("{$side}_comment", $element->getErrors()) ? '' : 'style="display:none;"' ?>
                                 data-comment-button="#document_<?= $side ?>_comment_button">
                                <?= $form->textArea(
                                    $element,
                                    "{$side}_comment",
                                    array('rows' => '1', 'nowrapper' => true),
                                    false,
                                    ['placeholder' => 'Comments', 'class' => 'js-comment-field autosize cols-full']
                                ); ?>
                                <i class="oe-i remove-circle small-icon pad-left js-remove-add-comments"></i>
                            </div>
                            <button id="document_<?= $side ?>_comment_button"
                                    class="button js-add-comments"
                                    data-comment-container="#document-<?= $side ?>-comments"
                                    type="button"
                                    data-hide-method="display"
                                    style="display: <?= $element->{$side."_comment"} || array_key_exists("{$side}_comment", $element->getErrors()) ? 'none;' : 'block;' ?>">
                                <i class="oe-i comments small-icon"></i>
                            </button>
                    </td>
                    <?php endforeach; ?>
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
        function rotateImage(degree, type) {
            let document_rotate = $('#'+type+'_document_rotate').val();
            degree = Number(document_rotate)+Number(degree);
            let image_id = $('#Element_OphCoDocument_Document_'+type+'_document_id').val();
            let image_src = $('#ophco-image-container-'+image_id+' img').attr('src');
            image_src = image_src.split('?');
            image_src = image_src[0];

            $('#ophco-image-container-'+image_id+' img').animate({  transform: degree }, {
                step: function() {
                    $(this).attr({
                        'src': image_src + '?rotate=' + degree
                    });
                    $('#'+type+'_document_rotate').val(degree);
                }
            });
        }

        window.addEventListener("unload", function () {
            let documents = [];
            let controller = $('.js-document-upload-wrapper').data('controller');
            let removed_docs = $('#removed-docs');

            if (controller.options.action === 'cancel' || controller.options.action === '') {
                $('.js-document-id').each(function () {
                    if ($(this).val() !== "") {
                        $(this).parents('td').find(controller.options.removeButtonSelector).trigger('click');
                    }
                });

                documents = removed_docs.data('documents');

                if (window.location.href.includes('update')) {
                    if ($('#upload_single').prop('checked')) {
                        let original_doc = $('#original-doc').val();
                        documents = documents.filter(function (document) {
                            return document !== original_doc;
                        });
                        removed_docs.data('documents', documents);
                    } else {
                        for (let side of ['left', 'right']) {
                            documents = documents.filter(function (document) {
                                return document !== $('#original-' + side + '-doc').val();
                            });
                            removed_docs.data('documents', documents);
                        }
                    }
                }
            }

            if (documents.length !== 0) {
                let formData = new FormData();
                OpenEyes.Util.createFormData(formData, 'doc_ids', documents);
                formData.append('YII_CSRF_TOKEN', YII_CSRF_TOKEN);
                navigator.sendBeacon('/OphCoDocument/Default/removeDocuments', formData);
            }
        });

    </script>