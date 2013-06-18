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
<div class="report curvybox white">
	<div class="admin">
		<h3 class="georgia">Change password</h3>
		<div>
			<?php $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
				'id'=>'profile-form',
				'enableAjaxValidation'=>false,
				'htmlOptions' => array('class'=>'sliding'),
			))?>
			<?php if (!Yii::app()->params['profile_user_can_change_password']) {?>
				<div class="alertBox flash-alert">
					User changing of passwords is administratively disabled.
				</div>
			<?php }?>
			<?php $this->renderPartial('//base/_messages')?>
			<?php $this->renderPartial('//elements/form_errors',array('errors'=>$errors))?>
			<?php echo $form->passwordField($user,'password_old',array('readonly'=>!Yii::app()->params['profile_user_can_change_password']))?>
			<?php echo $form->passwordField($user,'password_new',array('readonly'=>!Yii::app()->params['profile_user_can_change_password']))?>
			<?php echo $form->passwordField($user,'password_confirm',array('readonly'=>!Yii::app()->params['profile_user_can_change_password']))?>
			<?php $this->endWidget()?>
		</div>
	</div>
</div>
<?php if (Yii::app()->params['profile_user_can_change_password']) {?>
	<div>
		<?php echo EventAction::button('Save', 'save', array('colour' => 'blue'))->toHtml()?>
		<img class="loader" src="<?php echo Yii::app()->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
	</div>
<?php }?>
