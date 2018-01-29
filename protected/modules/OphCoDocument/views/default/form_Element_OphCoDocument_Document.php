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
<div class="element-fields">
    <?php echo $form->dropDownList($element, 'event_sub_type',
        CHtml::listData(OphCoDocument_Sub_Types::model()->findAll('is_active=1 ORDER BY display_order' ), 'id', 'name'),
        array(),
        array(),
        array(
            'label' => 4,
            'field' => 2,
        )); ?>
    <div class="row">
        <div class="cols-8 column">
            <label class="inline highlight">
                <input type="radio" name="upload_mode" value="single" <?php if($element->single_document_id >0){echo "checked";}?>>Single file
            </label>
            <label class="inline highlight">
                <input type="radio" name="upload_mode" value="double" <?php if($element->left_document_id > 0 || $element->right_document_id >0){echo "checked";}?>>Right/Left sides
            </label>
        </div>
    </div>
    <div class="row" id="single_document_uploader">
        <div class="cols-8 column" id="single_document_id_row">
            <?php $this->generateFileField($element, 'single_document'); ?>
        </div>
    </div>
    <div id="double_document_uploader">
        <div class="row">
            <div class="cols-6 column">
                <b>RIGHT</b>
            </div>
            <div class="cols-6 column">
                <b>LEFT</b>
            </div>
        </div>
        <div class="row" id="double_document_uploader">
            <div class="cols-6 column" id="right_document_id_row">
                <?php $this->generateFileField($element, 'right_document'); ?>
            </div>
            <div class="cols-6 column" id="left_document_id_row">
                <?php $this->generateFileField($element, 'left_document'); ?>
            </div>
        </div>
    </div>
    <div id="showUploadStatus" style="height:10px;color:#ffffff;font-size:8px;text-align:right;width:0px;background-color:#22b24c"></div>
    <?php
        foreach (array('single_document_id', 'left_document_id', 'right_document_id') as $index_key) {
            if($element->{$index_key} > 0)
            {
                echo "<input type=\"hidden\" name=\"Element_OphCoDocument_Document[".$index_key."]\" id=\"Element_OphCoDocument_Document_".$index_key."\" value=\"".$element->{$index_key}."\">";
            }
        }
    ?>
    <div class="upload-info" style="font-size:13px">
        <span class="has-tooltip fa fa-info-circle left" style="margin:3px 3px 0px 0px"></span> The following file types are accepted: <?php echo implode(', ', $this->getAllowedFileTypes()); ?>
        (Maximum size: <?=$this->getMaxDocumentSize();?> MB)
    </div>
    
    <div class="row field-row" style="padding-top:10px;">
        <div class="large-8 column">
            <label style="font-weight:bold">Comments:</label>
            <?php
                echo $form->textArea($element, 'comment', array('rows' => '5', 'cols' => '80', 'class' => 'autosize', 'nowrapper' => true), false);
            ?>
        </div>
    </div>
</div>

