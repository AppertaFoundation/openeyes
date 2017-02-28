<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>
<fieldset class="element-fields">
	<?php
    $form->activeWidget('DropDownList', $element, 'type_id',
        array(
            'data' => CHtml::listData(OphInDnasample_Sample_Type::model()->findAll(array('order' => 'display_order asc')), 'id', 'name'),
            'htmlOptions' => array('empty' => '- Please select -'),
        ));

    $form->activeWidget('TextField', $element, 'other_sample_type');

    $form->activeWidget('DatePicker', $element, 'blood_date',
        array(
            'options' => array('maxDate' => 'today'),
        ));

    $form->activeWidget('TextField', $element, 'volume');
    $form->radioBoolean($element, 'is_local', array(), array('label' => 3, 'field' => 9));
    $form->activeWidget('TextField', $element, 'destination');

    $form->dropDownList(
        $element,
        'consented_by',
        CHtml::listData(User::model()->findAll(array('order' => 'last_name asc')), 'id', function($row){return $row->last_name.', '.$row->first_name;}),
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
