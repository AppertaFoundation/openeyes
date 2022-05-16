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
<script src="<?= Yii::app()->assetManager->createUrl('../../node_modules/fabric/dist/fabric.min.js')?>"></script>
<script src="<?= Yii::app()->assetManager->createUrl('../../node_modules/pdfjs-dist/build/pdf.min.js')?>"></script>
<script src="<?= Yii::app()->assetManager->createUrl('../../node_modules/pdfjs-dist/build/pdf.worker.min.js')?>"></script>
<script src="<?= Yii::app()->assetManager->createUrl('../../node_modules/jspdf/dist/jspdf.min.js')?>"></script>
<div class="element-fields full-width flex-layout">
    <input type="hidden" id="removed-docs" name="removed-docs" value="">
    <div id="document-event" class="<?= $element->single_document_id || $element->hasSidedAttributesSet("OR") ? 'cols-full' : 'cols-11' ?>">

        <div id="document_summary" class="flex-t col-gap js-document-summary-wrapper" <?= ($element->single_document_id || $element->hasSidedAttributesSet("OR") ? '' : 'style="display:none"'); ?>>
            <div class="cols-full" data-side="single" <?= ($element->single_document_id ? '' : 'style="display:none"'); ?>>
                <?php $this->renderPartial('./document_upload_summary', array(
                    'form' => $form,
                    'element' => $element,
                    'document' => $element->single_document,
                    'document_id' => $element->single_document_id,
                    'side' => 'single',
                ), false); ?>
            </div>
            <div class="cols-half" data-side="right" <?= ($element->right_document_id ? '' : 'style="display:none"'); ?>>
                <?php $this->renderPartial('./document_upload_summary', array(
                    'form' => $form,
                    'element' => $element,
                    'document' => $element->right_document,
                    'document_id' => $element->right_document_id,
                    'side' => 'right',
                ), false); ?>
            </div>
            <div class="cols-half" data-side="left" <?= ($element->left_document_id ? 'style="margin-left: auto"' : 'style="display:none"'); ?>>
                <?php $this->renderPartial('./document_upload_summary', array(
                    'form' => $form,
                    'element' => $element,
                    'document' => $element->left_document,
                    'document_id' => $element->left_document_id,
                    'side' => 'left',
                ), false); ?>
            </div>
        </div>

        <table id="document-event-info" class="cols-6 last-left" <?= $element->single_document_id || $element->hasSidedAttributesSet("OR") ? 'style="display:none"' : '' ?>>
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
            <input type="hidden" value="<?=!empty($element->single_document->rotate) ? $element->single_document->rotate : ''?>" name="single_document_rotate" id="single_document_rotate">
            <table class="last-left cols-full">
                <colgroup>
                    <col class="cols-full">
                </colgroup>
                <thead></thead>
                <tbody>
                <tr class="valign-top">
                    <td data-side="single">
                        <div class="pdf-actions"<?= (!$element->single_document_id ||
                        $element->single_document->mimetype != "application/pdf" ?
                            'style="display:none"' :
                            ''); ?>>
                            <label>Page:</label>
                            <i class="oe-i direction-left large pad-left js-pdf-prev"></i>
                            <i class="oe-i direction-right large pad-left js-pdf-next"></i>
                        </div>
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

                        <?php $this->generateFileField($element, 'single_document', 'single'); ?>

                        <?php if ($element->single_document_id) : ?>
                            <input type="hidden" id="original-doc" name="original-doc" value="<?= $element->single_document_id ?>">
                        <?php endif; ?>

                        <?= CHtml::activeHiddenField($element, 'single_document_id', ['class' => 'js-document-id']); ?>
                        <input type="hidden" class="js-protected-file-content" name="ProtectedFile[single_file_content]" id="ProtectedFile_single_file_content" value="">
                        <input type="hidden" class="js-canvas-modified" name="single_file_canvas_modified" id="single_file_canvas_modified" value="">
                    </td>
                </tr>
                </tbody>
            </table>
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
                    <td data-side="<?=$side?>">
                        <input type="hidden" value="<?=!empty($element->{$side."_document"}->rotate) ? $element->{$side."_document"}->rotate : ''?>" name="<?=$side?>_document_rotate" id="<?=$side?>_document_rotate">
                        <div class="pdf-actions"<?= (!$element->{$side."_document_id"} ||
                        $element->{$side.'_document'}->mimetype != "application/pdf" ?
                            'style="display:none"' :
                            ''); ?>>
                            <label>Page:</label>
                            <i class="oe-i direction-left large pad-left js-pdf-prev"></i>
                            <i class="oe-i direction-right large pad-left js-pdf-next"></i>
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
                        <?php $this->generateFileField($element, $side.'_document', $side); ?>

                        <input type="hidden" id="original-<?=$side?>-doc" value="<?= $element->{$side."_document_id"} ?>">

                        <?= CHtml::activeHiddenField($element, $side.'_document_id', ['class' => 'js-document-id']); ?>

                        <input type="hidden" class="js-protected-file-content" name="ProtectedFile[<?=$side?>_file_content]" id="ProtectedFile_<?=$side?>_file_content" value="">
                        <input type="hidden" class="js-canvas-modified" name="<?=$side;?>_file_canvas_modified" id="<?=$side;?>_file_canvas_modified" value="">
                    </td>
                    <?php endforeach; ?>
                </tr>
                </tbody>
            </table>
        </div>

        <p id="pdf-message" style="display: none;">Please save/cancel the annotation to annotate another page of the PDF.</p>

        <div class="oe-annotate-image" id="js-annotate-image" style="display: none;">
        </div>

        <div id="accepted-file-types" class="data-group fade">
            The following file types are accepted: <?php echo implode(', ', $this->getAllowedFileTypes()); ?> (Maximum
            size: <?= $this->getMaxDocumentSize(); ?> MB)
        </div>
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

<template id="oe-upload-orientation-template">
    <div class="oe-upload-orientation">
        <div class="adjustments">
            Does uploaded image require rotating?
            <div class="flex-btns">
                <button class="blue hint js-rotate-image"><i class="oe-i forward small pad-right"></i>Rotate image 90°</button>
                <button class="hint green js-finalize-image">Image is correct</button>
            </div>
        </div>
    </div>
</template>

<template id="oe-annotate-image-template">
    <?php $this->renderPartial('./annotation_toolbox', null, false); ?>
    <div class="canvas-js">
    </div>
</template>

<script>

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
