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
$correspondeceApp = Yii::app()->params['ask_correspondence_approval'];
if($correspondeceApp === "on") {
    ?>
<div class="element-fields full-width flex-layout flex-top col-gap" style="padding: 10px;">
    <div class="cols-5 ">
        <table class="cols-full">
            <tr>
                <td class="data-label"><?=\CHtml::encode($element->getAttributeLabel('is_signed_off')) . ' '; ?></td>
                <td>
                    <div class="data-value" style="text-align: right">
                        <?php
                        if($element->is_signed_off == NULL){
                            echo 'N/A';
                        } else if((int)$element->is_signed_off == 1){
                            echo 'Yes';
                        } else {
                            echo 'No';
                        }
                        ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="data-label">
                    From
                </td>
                <td>
                    <?php
                    echo $element->site->getLetterAddress(array(
                        'include_name' => true,
                        'delimiter' => '<br />',
                        'include_telephone' => true,
                        'include_fax' => true,
                    ))?>
                </td>
            </tr>
            <tr>
                <td class="data-label">
                    Direct Line
                </td>
                <td>
                    <?php
                    echo $element->direct_line
                    ?>
                </td>
            </tr>
            <tr>
                <td class="data-label">
                    Direct Fax
                </td>
                <td >
                    <?php
                    echo $element->fax
                    ?>
                </td>
            </tr>
            <tr>
                <td class="data-label">
                    Cc
                </td>
                <td>
                    <?php
                    $ccString = "";
                    $toAddress = "";

                    if($element->document_instance) {

                        foreach ($element->document_instance as $instance) {
                            foreach ($instance->document_target as $target) {
                                if($target->ToCc == 'To'){
                                    $toAddress = $target->contact_name . "\n" . $target->address;
                                } else {
                                    $contact_type = $target->contact_type != 'GP' ? ucfirst(strtolower($target->contact_type)) : $target->contact_type;

                                    $ccString .= "" . $contact_type . ": " . $target->contact_name . ", " . $element->renderSourceAddress($target->address)."<br/>";
                                }
                            }
                        }
                    }else
                    {
                        $toAddress = $element->address;
                        foreach (explode("\n", trim($element->cc)) as $line) {
                            if (trim($line)) {
                                $ccString .= "" . str_replace(';', ',', $line)."<br/>";
                            }
                        }
                    }
                    ?>
                    <?php
                    echo $ccString
                    ?>
                </td>
            </tr>
        </table>
    </div>
    <?php } ?>
<div class="js-correspondence-image-overlay"></div>
</div>
