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
$correspondeceApp = Yii::app()->params['ask_correspondence_approval'];
$is_mobile_or_tablet = preg_match('/(ipad|iphone|android)/i', Yii::app()->getRequest()->getUserAgent());?>
<div class="element-data full-width flex-layout flex-top col-gap">
    <div class="cols-3">
        <table class="cols-full">
            <tbody>
                <?php if ($correspondeceApp === "on") { ?>
                <tr>
                    <td class="data-label"><?=\CHtml::encode($element->getAttributeLabel('is_signed_off')) . ' '; ?></td>
                    <td>
                        <div class="data-value" style="text-align: right">
                            <?php
                            if ($element->is_signed_off == NULL) {
                                echo 'N/A';
                            } else if ((int)$element->is_signed_off == 1) {
                                echo 'Yes';
                            } else {
                                echo 'No';
                            }
                            ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>Text size</td>
                    <td>Large Font</td>
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
                                    if ($target->ToCc == 'To') {
                                           $toAddress = $target->contact_name . "\n" . $target->address;
                                    } else {
                                        $contact_type = $target->contact_type != Yii::app()->params['gp_label'] ? ucfirst(strtolower($target->contact_type)) : $target->contact_type;
                                         $ccString .= "CC: " . ($contact_type != "Other" ? $contact_type . ": " : "") . $target->contact_name . ", " . $element->renderSourceAddress($target->address)."<br/>";
                                    }
                                }
                            }
                        } else {
                            $toAddress = $element->address;
                            foreach (explode("\n", trim($element->cc)) as $line) {
                                if (trim($line)) {
                                    $ccString .= "CC: " . str_replace(';', ',', $line)."<br/>";
                                }
                            }
                        }
                            echo str_replace("\n", '<br/>', CHtml::encode($toAddress))."<br/>".$ccString;
                        ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <div class="cols-9">
        <div class="spinner-overlay" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
            <p style="margin-bottom: 100px;">Generating PDFs</p>
            <i class="spinner"></i>
        </div>
        <?php if($is_mobile_or_tablet){?>
            <div class="js-correspondence-image-overlay" style="position: relative;"></div>
        <?php } else {?>
            <iframe src="/OphCoCorrespondence/default/PDFprint/<?= $element->event_id; ?>?auto_print=<?= $element->checkPrint() ?>" style="width: <?=Yii::app()->params['lightning_viewer']['blank_image_template']['width']?>px; height: <?=Yii::app()->params['lightning_viewer']['blank_image_template']['height']?>px; border: 0; position: relative;"></iframe>
        <?php } ?>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        let options = [];
        // OE-8581 Disable lightning image loading due to speed issues
        options['disableAjaxCall'] = <?= ($is_mobile_or_tablet ? 'false' : 'true'); ?>;
        new OpenEyes.OphCoCorrespondence.ImageLoaderController(OE_event_id , options);
    });
</script>
