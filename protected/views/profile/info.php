<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

?>

<div class="box admin">
	<h2>Basic information</h2>

	<?php $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
		'id' => 'profile-form',
		'enableAjaxValidation' => false,
		'layoutColumns' => array(
			'label' => 2,
			'field' => 5,
		)
	))?>

		<?php if (!Yii::app()->params['profile_user_can_edit']) {?>
			<div class="alert-box alert">
				User editing of basic information is administratively disabled.
			</div>
		<?php }?>

		<?php $this->renderPartial('//base/_messages')?>
		<?php $this->renderPartial('//elements/form_errors',array('errors'=>$errors))?>

		<?php echo $form->textField($user, 'title', array('autocomplete' => Yii::app()->params['html_autocomplete'], 'readonly' => !Yii::app()->params['profile_user_can_edit']), null, array('field' => 2));?>
		<?php echo $form->textField($user, 'first_name', array('autocomplete' => Yii::app()->params['html_autocomplete'], 'readonly' => !Yii::app()->params['profile_user_can_edit']));?>
		<?php echo $form->textField($user, 'last_name', array('autocomplete' => Yii::app()->params['html_autocomplete'], 'readonly' => !Yii::app()->params['profile_user_can_edit']));?>
		<?php echo $form->textField($user, 'email', array('autocomplete' => Yii::app()->params['html_autocomplete'], 'readonly' => !Yii::app()->params['profile_user_can_edit']));?>
		<?php echo $form->textField($user, 'qualifications', array('autocomplete' => 'readonly' => !Yii::app()->params['profile_user_can_edit']));?>

		<?php if (Yii::app()->params['profile_user_can_edit']) {?>
			<div class="row field-row">
				<div class="large-5 large-offset-2 column">
					<?php echo EventAction::button('Save', 'save')->toHtml()?>
					<img class="loader" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
				</div>
			</div>
		<?php }?>

	<?php $this->endWidget()?>
</div>


