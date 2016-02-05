<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>

<?php echo $form->dropDownList($element, 'incision_site_id', 'OphTrOperationnote_IncisionSite' ,array('empty'=>'- Please select -','textAttribute'=>'data-value'),false,array('field'=>4))?>
<?php echo $form->textField($element, 'length', array(),array(),array_merge($form->layoutColumns,array('field'=>3)))?>
<?php echo $form->textField($element, 'meridian', array(),array(),array_merge($form->layoutColumns,array('field'=>3)))?>
<?php echo $form->dropDownList($element, 'incision_type_id', 'OphTrOperationnote_IncisionType', array('empty'=>'- Please select -','textAttribute'=>'data-value'),false,array('field'=>4))?>
<?php echo $form->textArea($element, 'report',array(),false,array('rows'=>6))?>
<?php echo $form->dropDownList($element, 'iol_type_id', array(
		CHtml::listData(OphTrOperationnote_IOLType::model()->activeOrPk($element->iol_type_id)->findAll(array('condition'=>'private=0','order'=>'display_order asc')),'id','name'),
		CHtml::listData(OphTrOperationnote_IOLType::model()->activeOrPk($element->iol_type_id)->findAll(array('condition'=>'private=1','order'=>'display_order')),'id','name'),
	),
	array('empty' => '- Please select -','divided' => true),$element->iol_hidden,array('field' => 4))?>
<?php echo $form->textField($element, 'predicted_refraction',array(),array(),array_merge($form->layoutColumns,array('field'=>2)))?>
<?php echo $form->textField($element, 'iol_power', array('hide' => $element->iol_hidden),array(),array_merge($form->layoutColumns,array('field'=>2)))?>
<?php echo $form->dropDownList($element, 'iol_position_id', 'OphTrOperationnote_IOLPosition', array('empty'=>'- Please select -'),$element->iol_hidden,array('field'=>4))?>
<?php echo $form->multiSelectList($element, 'OphTrOperationnote_CataractOperativeDevices', 'operative_devices', 'id', $this->getOperativeDeviceList($element), $this->getOperativeDeviceDefaults(), array('empty' => '- Devices -', 'label' => 'Devices'),false,false,null,false,false,array('field'=>4))?>
<?php echo $form->multiSelectList($element, 'OphTrOperationnote_CataractComplications', 'complications', 'id', CHtml::listData(OphTrOperationnote_CataractComplications::model()->activeOrPk($element->cataractComplicationValues)->findAll(array('order'=>'display_order asc')), 'id', 'name'), null, array('empty' => '- Complications -', 'label' => 'Complications'),false,false,null,false,false,array('field'=>4))?>
<?php echo $form->textArea($element, 'complication_notes',array(),false,array('rows'=>6))?>
