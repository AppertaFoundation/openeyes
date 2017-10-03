<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<script type="text/javascript" src="<?=$this->getJsPublishedPath("SocialHistory.js")?>"></script>
<div class="element-fields">
    <?php
    echo $form->dropDownList($element, 'occupation_id',
        CHtml::listData($element->occupation_options, 'id', 'name'),
        array('empty' => '- Select -'), false, array('label' => 2, 'field' => 2));

    echo $form->textField($element, 'type_of_job', array('hide' => true, 'autocomplete' => Yii::app()->params['html_autocomplete']), null, array('label' => 2, 'field' => 3));

    echo $form->multiSelectList($element, CHtml::modelName($element) . '[driving_statuses]', 'driving_statuses', 'id',
        CHtml::listData($element->driving_statuses_options, 'id', 'name'),
        array(),
        array('empty' => '- Select -', 'label' => $element->getAttributeLabel('driving_statuses')),
        false, false, null, false, false, // various attributes we don't care about
        array('label' => 2, 'field' => 2));

    echo $form->dropDownList($element, 'smoking_status_id',
        CHtml::listData($element->smoking_status_options, 'id', 'name'),
        array('empty' => '- Select -'), false, array('label' => 2, 'field' => 2));

    echo $form->dropDownList($element, 'accommodation_id', CHtml::listData($element->accommodation_options, 'id', 'name'),
        array('empty' => '- Select -'), false, array('label' => 2, 'field' => 2));

    echo $form->textArea($element, 'comments', array('rows' => '1', 'cols' => '80', 'class' => 'autosize'), false,
        array('placeholder' => 'Enter comments here'), array('label' => 2, 'field' => 6));

    echo $form->dropDownList($element, 'carer_id', CHtml::listData($element->carer_options, 'id', 'name'),
        array('empty' => '- Select -'), false, array('label' => 2, 'field' => 2));

    echo $form->textField($element, 'alcohol_intake',
        array('autocomplete' => Yii::app()->params['html_autocomplete'], 'append-text' => 'units/week'),
        null,
        array('label' => 2, 'field' => 2, 'append-text' => 2));

    echo $form->dropDownList($element, 'substance_misuse_id',
        CHtml::listData($element->substance_misuse_options, 'id', 'name'),
        array('empty' => '- Select -'), false, array('label' => 2, 'field' => 2));

    ?>
</div>
