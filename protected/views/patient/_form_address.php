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
<?php
/**
 * @var Address $address
 */
?>
<tr>
  <td>
        <?= $form->label($address, 'address_type_id') ?>
    <br/>
        <?= $form->error($address, 'address_type_id') ?>
  </td>
  <td>
        <?php echo $form->dropDownList(
            $address,
            'address_type_id',
            $address_type_ids,
            array('empty' => '-- select --')
        ); ?>
  </td>
<tr>
<tr>
  <td>
        <?= $form->labelEx($address, 'address1') ?>
    <br/>
        <?= $form->error($address, 'address1') ?>
  </td>
  <td>
        <?= $form->textField($address, 'address1', array('size' => 15, 'placeholder' => 'Address 1', 'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'))) ?>
  </td>
</tr>

<tr>
  <td>
        <?= $form->labelEx($address, 'address2') ?>
    <br/>
        <?= $form->error($address, 'address2') ?>
  </td>
  <td>
        <?= $form->textField($address, 'address2', array('size' => 15, 'placeholder' => 'Address 2', 'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'))) ?>

  </td>
</tr>
<tr>
  <td>
        <?= $form->labelEx($address, 'city') ?>
    <br/>
        <?= $form->error($address, 'city') ?>
  </td>
  <td>
        <?= $form->textField($address, 'city', array('size' => 15, 'placeholder' => 'City', 'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'))) ?>
  </td>
</tr>

<tr>
  <td>
        <?= $form->labelEx($address, 'postcode') ?>
    <br/>
        <?= $form->error($address, 'postcode') ?>
  </td>
  <td>
        <?= $form->textField($address, 'postcode', array('size' => 15, 'class' => 'postcode', 'placeholder' => 'Postcode', 'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'))) ?>
  </td>
</tr>

<tr>
  <td>
        <?= $form->labelEx($address, Yii::app()->params['county_label']) ?>
    <br/>
        <?= $form->error($address, Yii::app()->params['county_label']) ?>
  </td>
  <td>
        <?= $form->textField($address, 'county', array('size' => 15, 'placeholder' => $address->getAttributeLabel('county'), 'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'))) ?>

  </td>
</tr>

<tr>
  <td class="required">
        <?= $form->label($address, 'country_id') ?>
    <br/>
        <?= $form->error($address, 'country_id') ?>
  </td>
  <td>
        <?= $form->dropDownList($address, 'country_id', $countries, (in_array(Yii::app()->params['default_country'], $countries))  && Yii::app()->controller->action->id !== 'update' ?
            array('options' => array(array_search(Yii::app()->params['default_country'], $countries) => array('selected' => true)),
                'placeholder' => 'Country') : array('empty' => '-- select --'))  ?>
  </td>
</tr>
