<?php
/*
_____________________________________________________________________________
(C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
(C) OpenEyes Foundation, 2011
This file is part of OpenEyes.
OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
_____________________________________________________________________________
http://www.openeyes.org.uk   info@openeyes.org.uk
--
*/

$baseUrl = Yii::app()->baseUrl;
$cs = Yii::app()->getClientScript();
$cs->registerCoreScript('jquery');
$cs->registerScriptFile($baseUrl.'/js/jquery.watermark.min.js');
$this->pageTitle=Yii::app()->name . ' - Login';
$this->layout = 'simple';
?>
<div id="login">
	<div class="text">Login to OpenEyes:</div>

	<div class="form">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'login-form',
		'enableAjaxValidation'=>false,
	));?>
		<?php echo $form->error($model,'password'); ?>

		<div class="row">
			<?php echo CHtml::activeLabel($model,'username', array('label'=>'Username:')); ?>
			<?php echo $form->textField($model,'username'); ?>
		</div>

		<div class="row">
			<?php echo CHtml::activeLabel($model,'password', array('label'=>'Password:')); ?>
			<?php echo $form->passwordField($model,'password'); ?>
		</div>

		<div class="row">
			<?php echo CHtml::activeLabel($model,'siteId', array('label'=>'Site:')); ?>
			<?php echo $form->dropDownList($model, 'siteId', $sites); ?>
			<?php echo $form->error($model,'siteId'); ?>
		</div>

		<div class="row buttons">
			<?php echo CHtml::submitButton(''); ?>
		</div>

	<?php $this->endWidget(); ?>
	</div><!-- form -->

	<div class="contact">Don't have a username and password? <span style="font-weight: normal;">Contact the helpdesk on:</span><br />
		Telephone: <span class="number">ext. 0000</span> Email: <span class="number">helpdesk@openeyes.org.uk</span>
	</div>
</div>
<script type="text/javascript">
	$('input[id=LoginForm_username]').watermark('enter username');
	$('input[id=LoginForm_password]').watermark('enter password');
</script>