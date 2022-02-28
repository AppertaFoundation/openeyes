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
Yii::app()->clientScript->registerScriptFile("{$this->assetPath}/js/pages.js", \CClientScript::POS_HEAD);
Yii::app()->clientScript->registerScriptFile("{$this->assetPath}/js/imageLoader.js", \CClientScript::POS_HEAD);

if (!($element->draft && SettingMetadata::checkSetting('disable_draft_correspondence_email', 'on'))) {
    Yii::app()->clientScript->registerScriptFile("{$this->assetPath}/js/email.js", \CClientScript::POS_HEAD);
}

$correspondeceApp = Yii::app()->params['ask_correspondence_approval'];
$is_mobile_or_tablet = preg_match('/(ipad|iphone|android)/i', Yii::app()->getRequest()->getUserAgent()); ?>
<div class="element-data full-width flex-layout flex-top col-gap">
    <div class="cols-3">
        <table class="cols-full">
            <tbody>
            <?php if ($correspondeceApp === "on") { ?>
                <tr>
                    <td class="data-label"><?= \CHtml::encode($element->getAttributeLabel('is_signed_off')) . ' '; ?></td>
                    <td>
                        <div class="data-value text-right">
                            <?php
                            if ($element->is_signed_off == null) {
                                echo 'N/A';
                            } elseif ((int)$element->is_signed_off == 1) {
                                echo 'Yes';
                            } else {
                                echo 'No';
                            } ?>
                        </div>
                    </td>
                </tr>
            <?php } ?>
                <?php
                $letter_type = LetterType::model()->findByPk($element->letter_type_id); ?>
                <tr>
                    <td class="data-label"><?=\CHtml::encode($element->getAttributeLabel('letter_type_id')) . ' '; ?></td>
                    <td>
                        <div class="data-value text-right">
                            <?php
                            if ($letter_type == null) {
                                echo 'N/A';
                            } else {
                                echo $letter_type->name;
                            } ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <small class="fade">To</small><br>
                        <?php
                        $ccString = "";
                        $toAddress = "";
                        if ($element->document_instance) {
                            foreach ($element->document_instance as $instance) {
                                foreach ($instance->document_target as $target) {
                                    $emailOutputStatus = "";
                                    $isInfoBoxRequired = false;
                                    foreach ($target->document_output as $documentOutput) {
                                        if ( ($documentOutput->output_type === 'Email') || ($documentOutput->output_type === 'Email (Delayed)') ) {
                                            $isInfoBoxRequired = true;
                                        }
                                    }
                                    if ($isInfoBoxRequired) {
                                        $emailOutputStatus = "<i class='oe-i info small pad js-has-tooltip-document-output-status' id='document_target_id_$target->id' data-tooltip-content=></i>";
                                    }
                                    if ($target->ToCc == 'To') {
                                        $toAddress = $target->contact_name . ($emailOutputStatus ?? "") . "\n" . $target->address;
                                    } else {
                                        $contact_type = $target->contact_type != \SettingMetadata::model()->getSetting('gp_label') ? ucfirst(strtolower($target->contact_type)) : $target->contact_type;
                                        $ccString .= "<small class='fade'>CC</small><br/>" . ($contact_type != "Other" ? $contact_type . ": " : "") . $target->contact_name . ($emailOutputStatus ?? "") . "<br/>" . $element->renderSourceAddress($target->address) . "<br/>";
                                    }
                                }
                            }
                        } else {
                            $toAddress = $element->address;
                            foreach (explode("\n", trim($element->cc)) as $line) {
                                if (trim($line)) {
                                    $ccString .= "<small class='fade'>CC</small>" . str_replace(';', ',', $line) . "<br/>";
                                }
                            }
                        }
                        echo str_replace("\n", '<br/>', $toAddress) . "<br/>" . $ccString;
                        ?>
                </td>
            </tr>

            </tbody>
        </table>
    </div>
    <div class="cols-9">
        <div class="spinner-overlay" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
            <p style="margin-bottom: 100px;">Generating PDFs</p>
            <i class="spinner"></i>
        </div>
        <?php if ($is_mobile_or_tablet) { ?>
            <div class="js-correspondence-image-overlay" style="position: relative;"></div>
        <?php } else { ?>
            <iframe src="/OphCoCorrespondence/default/PDFprint/<?= $element->event_id; ?>?auto_print=<?= $element->checkPrint() ?>&is_view=1#toolbar=0" data-eventid="<?= $element->event_id ?>"
                    style="width: <?= Yii::app()->params['lightning_viewer']['blank_image_template']['width'] ?>px; height: <?= Yii::app()->params['lightning_viewer']['blank_image_template']['height'] ?>px; border: 0; position: relative;"></iframe>
        <?php } ?>
    </div>
</div>
</section>
<section class="element view full">
        <?php
        $associated_content = EventAssociatedContent::model()
            ->with('initAssociatedContent')
            ->findAllByAttributes(
                array('parent_event_id' => $element->event_id),
                array('order' => 't.display_order asc')
            );

        $this->renderPartial('view_event_associated_content', array(
            'associated_content' => $associated_content,
        ));
        ?>
<script type="text/javascript">
    $(document).ready(function () {
        let options = [];
        // OE-8581 Disable lightning image loading due to speed issues
        options['disableAjaxCall'] = <?= ($is_mobile_or_tablet ? 'false' : 'true'); ?>;
        new OpenEyes.OphCoCorrespondence.ImageLoaderController(OE_event_id, options);
        if ((String)($('iframe').data('doprint')).charAt(0) === '1') {
            let eventId = $('iframe').data('eventid');
            $.ajax({
                'type': 'GET',
                'url': baseUrl + '/OphCoCorrespondence/default/markPrinted/' + eventId,
                'success': function(html) {
                    printEvent(html);
                },
                'error': function() {
                    new OpenEyes.UI.Dialog.Alert({
                        content: "Something went wrong trying to print the letter, please try again or contact support for assistance."
                    }).open();
                }
            });
        }
    });

    function getDocumentOutputStatus($element) {
        var elementId = $element.id;
        var id = elementId.substring(elementId.lastIndexOf('_') + 1);

        // get the current status of the document output.
        $.ajax({
            'url': baseUrl+'/OphCoCorrespondence/Default/getDocumentOutputStatus?document_target_id=' + id,
            'type': 'GET',
            'success': function (data) {
                let isHovered = $($element).is(":hover");
                if (isHovered) {
                    $("#" + elementId).data("tooltip-content", data);
                    // This method is defined in the script.js
                    showToolTip($element);
                }
            },
        });
    }

    $('.js-has-tooltip-document-output-status').mouseover(function(e) {
        getDocumentOutputStatus(this);
    });

    $('.js-has-tooltip-document-output-status').mouseout(function () {
        $('body').find(".oe-tooltip").remove();
    });

    $(window).on('load', function () {
        // check if there is any document_output with the print output_type and has the status of DRAFT
        // if there is any, open the print dialog.
        $.ajax({
            'type': 'GET',
            'url': '/OphCoCorrespondence/Default/getDraftPrintRecipients/' + OE_event_id,
            'success': function (data) {
                if (data) {
                    OphCoCorrespondence_do_print(false)
                }
            }
        });
    });
</script>
