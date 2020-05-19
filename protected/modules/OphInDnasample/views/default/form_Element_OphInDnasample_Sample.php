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
<fieldset class="element-fields">
    <?php
    $form->activeWidget(
        'DropDownList',
        $element,
        'type_id',
        array(
            'data' => CHtml::listData(OphInDnasample_Sample_Type::model()->findAll(array('order' => 'display_order asc')), 'id', 'name'),
            'htmlOptions' => array('empty' => 'Select'),
        )
    );

    /* now way to hide the whole row using the widget : $form->activeWidget('TextField', $element, 'other_sample_type', array('class' => 'hidden')); */

    ?>

    <?php
        $hidden = $element->other_sample_type ? '' : 'hidden'; //hide if null
    if ( $element->getError('other_sample_type') ) {
        // show the field if there is an error
        $hidden = '';
    }
    ?>
    <div id="div_Element_OphInDnasample_Sample_other_sample_type" class="data-group <?php echo $hidden; ?>">
        <div class="cols-2 column">
            <label for="Element_OphInDnasample_Sample_other_sample_type"><?php echo $element->getAttributeLabel('other_sample_type'); ?></label>
        </div>
        <div class="cols-10 column end">
            <?=\CHtml::textField('Element_OphInDnasample_Sample[other_sample_type]', $element->other_sample_type); ?>
        </div>
    </div>

    <?php
    $form->activeWidget(
        'DatePicker',
        $element,
        'blood_date',
        array(
            'options' => array('maxDate' => 'today'),
        )
    );

    $form->activeWidget('TextField', $element, 'volume');
    $form->activeWidget('TextField', $element, 'destination');

    $users = User::model()->findAllByRoles(['Genetics User', 'Genetics Clinical', 'Genetics Laboratory Technician', 'Genetics Admin'], true);

    $form->dropDownList(
        $element,
        'consented_by',
        CHtml::listData($users, 'id', function($row){return $row->last_name.', '.$row->first_name;
        }),
        array('empty' => '- Select -', 'options'=>array(Yii::app()->user->id => array("selected"=>true)))
    );

    $user = User::model()->findByPk(Yii::app()->user->id);

    $form->multiSelectList(
        $element,
        CHtml::modelName($element) .'[studies]',
        'studies',
        'id',
        CHtml::listData(GeneticsStudy::model()->findAll(), 'id', 'name'),
        array(),
        array('label' => 'Study(s)', 'empty' => '-- Add --')
    );

    //$user['first_name'].' '.$user['last_name'];
    $form->activeWidget('TextField', $element, 'comments');

    ?>
</fieldset>