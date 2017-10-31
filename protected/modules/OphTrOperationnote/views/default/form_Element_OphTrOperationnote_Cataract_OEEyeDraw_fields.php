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

<?php echo $form->dropDownList($element, 'incision_site_id', 'OphTrOperationnote_IncisionSite',
    array('empty' => '- Please select -', 'textAttribute' => 'data-value'), false, array('field' => 4)) ?>
<?php echo $form->textField($element, 'length', array(), array(),
    array_merge($form->layoutColumns, array('field' => 3))) ?>
<?php echo $form->textField($element, 'meridian', array(), array(),
    array_merge($form->layoutColumns, array('field' => 3))) ?>
<?php echo $form->dropDownList($element, 'incision_type_id', 'OphTrOperationnote_IncisionType',
    array('empty' => '- Please select -', 'textAttribute' => 'data-value'), false, array('field' => 4)) ?>
<?php echo $form->textArea($element, 'report', array(), false, array('rows' => 6)) ?>
<?php
    if(isset(Yii::app()->modules["OphInBiometry"])){
        echo $form->dropDownList($element, 'iol_type_id', CHtml::listData(OphInBiometry_LensType_Lens::model()->findAll(array(
            'condition' => ($element->iol_type_id > 0)?'active=1 or id='.$element->iol_type_id:'active=1',
            'order' => 'display_name',
        )), 'id', 'display_name'),
            array('empty' => '- Please select -'), $element->iol_hidden, array('field' => 4));
    }else {
        echo $form->dropDownList($element, 'iol_type_id', array(
            CHtml::listData(OphTrOperationnote_IOLType::model()->activeOrPk($element->iol_type_id)->findAll(array(
                'condition' => 'private=0',
                'order' => 'display_order asc',
            )), 'id', 'name'),
            CHtml::listData(OphTrOperationnote_IOLType::model()->activeOrPk($element->iol_type_id)->findAll(array(
                'condition' => 'private=1',
                'order' => 'display_order',
            )), 'id', 'name'),
        ),
            array('empty' => '- Please select -', 'divided' => true), $element->iol_hidden, array('field' => 4));
    }?>
    <div id="div_Element_OphTrOperationnote_Cataract_iol_power" class="row field-row">
        <div class="large-3 column">
            <label for="Element_OphTrOperationnote_Cataract_iol_power">IOL power:</label>
        </div>
        <div class="large-2 column end">
            <input id="Element_OphTrOperationnote_Cataract_iol_power" type="text"
                   name="Element_OphTrOperationnote_Cataract[iol_power]" autocomplete="off" hide=""
                   value="<?php echo $element->iol_power; ?>">
        </div>
        <div class="large-3 column">
            <label for="Element_OphTrOperationnote_Cataract_predicted_refraction">Predicted refraction:</label>
        </div>
        <div class="large-2 column end">
            <input id="Element_OphTrOperationnote_Cataract_predicted_refraction" type="text"
                   name="Element_OphTrOperationnote_Cataract[predicted_refraction]" autocomplete="off"
                   value="<?php echo $element->predicted_refraction; ?>">
        </div>
    </div>
<?php
    //var_dump($element); //, 'iol_position_id'
?>

<?php echo $form->dropDownList($element, 'iol_position_id', 'OphTrOperationnote_IOLPosition',
    array(
        'empty' => '- Please select -',
        'options'=>array(
            8=>array('disabled'=>'disabled'),
        )
    ),
    $element->iol_hidden, array('field' => 4)
    ) ?>
<?php echo $form->multiSelectList($element, 'OphTrOperationnote_CataractOperativeDevices', 'operative_devices', 'id',
    $this->getOperativeDeviceList($element), $this->getOperativeDeviceDefaults(),
    array('empty' => '- Agents -', 'label' => 'Agents'), false, false, null, false, false, array('field' => 4)) ?>
<?php echo $form->multiSelectList($element, 'OphTrOperationnote_CataractComplications', 'complications', 'id',
    CHtml::listData(OphTrOperationnote_CataractComplications::model()->activeOrPk($element->cataractComplicationValues)->findAll(array('order' => 'display_order asc')),
        'id', 'name'), null, array('empty' => '- Complications -', 'label' => 'Complications'), false, false, null,
    false, false, array('field' => 4)) ?>
<?php echo $form->textArea($element, 'complication_notes', array(), false, array('rows' => 6)) ?>
<?php echo $form->hiddenInput($element, 'pcr_risk') ?>
