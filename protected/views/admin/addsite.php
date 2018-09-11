<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<main class="oe-full-main admin-main">
	<h2>Add site</h2>
	<?php echo $this->renderPartial('_form_errors', array('errors' => $errors))?>
	<?php
    $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
        'id' => 'adminform',
        'enableAjaxValidation' => false,
        'focus' => '#username',
        'layoutColumns' => array(
            'label' => 2,
            'field' => 5,
        ),
    ))?>

    <div class="cols-5">
        <table class="standard cols-full">
            <colgroup>
                <col class="cols-3">
                <col class="cols-5">
            </colgroup>
            <tbody>
            <tr>
                <td>Institution</td>
                <td >
                    <?php echo CHtml::activeDropDownList($site, 'institution_id',
                        CHtml::listData( Institution::model()->findAll() , 'id', 'name'),['class' => 'cols-full']); ?>
                </td>
            </tr>

            <?php foreach (['name', 'short_name', 'remote_id'] as $field) : ?>
                <tr>
                    <td><?= $site->getAttributeLabel($field); ?></td>
                    <td>
                        <?= CHtml::activeTextField($site, $field, [
                            'autocomplete' => Yii::app()->params['html_autocomplete'],
                            'class' => 'cols-full'
                        ]); ?>
                    </td>
                </tr>
            <?php endforeach; ?>

            <?php foreach (['address1', 'address2', 'city', 'county', 'postcode'] as $field) : ?>
                <tr>
                    <td><?= $address->getAttributeLabel($field); ?></td>
                    <td>
                        <?= CHtml::activeTextField($address, $field, [
                            'autocomplete' => Yii::app()->params['html_autocomplete'],
                            'class' => 'cols-full'
                        ]); ?>
                    </td>
                </tr>
            <?php endforeach; ?>

            <?php foreach (['telephone', 'fax'] as $field) : ?>
                <tr>
                    <td><?= $site->getAttributeLabel($field); ?></td>
                    <td>
                        <?= CHtml::activeTextField($site, $field, [
                            'autocomplete' => Yii::app()->params['html_autocomplete'],
                            'class' => 'cols-full'
                        ]); ?>
                    </td>
                </tr>
            <?php endforeach; ?>

            <tr>
                <td>Country</td>
                <td >
                    <?php echo CHtml::activeDropDownList($address, 'country_id',
                        CHtml::listData( Country::model()->findAll() , 'id', 'name'), ['class' => 'cols-full']); ?>
                </td>
            </tr>
            </tbody>

            <tfoot>
            <tr>
                <td colspan="5">
                    <?php echo CHtml::button('Save', ['class' => 'button large primary event-action',
                        'name' => 'save', 'type' => 'submit', 'id' => 'et_save']); ?>
                    <?php echo CHtml::button('Cancel', ['class' => 'warning button large primary event-action',
                        'data-uri' => '/admin/sites', 'type' => 'submit', 'name' => 'cancel', 'id' => 'et_cancel']); ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </div>

	<?php $this->endWidget()?>
</main>
