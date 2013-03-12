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

$this->pageTitle=Yii::app()->name . ' - Login';
$this->layout = 'simple';
?>
		<h2 class="alert">Please login</h2>

		<div id="login-form" class="form_greyBox">
		<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'loginform',
			'enableAjaxValidation'=>false,
		))?>
		<?php echo $form->error($model,'password'); ?>
		<?php //<form action="/site/login" method="post"> ?>

			<div class="loginRow bigInput">
				<?php echo CHtml::activeLabel($model,'username', array('label'=>'Username:')); ?>
				<?php echo $form->textField($model,'username',array('tabindex' => 1)); ?>
				<?php if (Yii::app()->params['auth_source'] == 'BASIC') {?>
					<a href="#" tabindex="5"><span class="small">Forgotten your username?</span></a>
				<?php }?>
			</div>

			<div class="loginRow bigInput">
				<?php echo CHtml::activeLabel($model,'password', array('label'=>'Password:')); ?>
				<?php echo $form->passwordField($model,'password',array('tabindex' => 2, 'autocomplete' => 'off')); ?>
				<?php if (Yii::app()->params['auth_source'] == 'BASIC') {?>
					<a href="#" tabindex="6"><span class="small">Forgotten your password?</span></a>
				<?php }?>
			</div>

			<div class="loginRow bigInput">
				<?php echo CHtml::activeLabel($model,'siteId', array('label'=>'Site:')); ?>
				<?php echo $form->dropDownList($model, 'siteId', $sites, array('tabindex' => 3)); ?>
				<?php echo $form->error($model,'siteId'); ?>
			</div>

			<div class="row">
				<img class="loader" src="<?php echo Yii::app()->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="margin-right: 10px; display: none;" />
				<button id="login_button" type="submit" name="yt0" class="classy blue tall" tabindex="2"><span class="button-span button-span-blue">Login</span></button>
			</div>

		<?php $this->endWidget(); ?>
	</div><!-- #login-form -->
	<script type="text/javascript">
		$('input[id=LoginForm_username]').watermark('enter username');
		$('input[id=LoginForm_password]').watermark('enter password');

		if ($('#LoginForm_username').val() == '') {
			$('#LoginForm_username').focus();
		} else {
			$('#LoginForm_password').select().focus();
		}

		handleButton($('#login_button'));
	</script>
