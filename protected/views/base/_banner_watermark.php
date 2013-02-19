<?php if (Yii::app()->user->checkAccess('admin')) {?>
<div id="alert_banner">
	<div class="banner-watermark admin"><?php echo (Yii::app()->params['watermark_admin']) ? Yii::app()->params['watermark_admin'] : 'You are logged in as admin' ?></div>
</div>
<?php } else if (Yii::app()->params['watermark']) {?>
<div id="alert_banner">
	<div class="banner-watermark"><?php echo Yii::app()->params['watermark']?></div>
</div>
<?php }?>
<?php if (@$description && Yii::app()->params['watermark_description']) {?>
<div class="banner-watermark-description"><p><?php echo Yii::app()->params['watermark_description']?></p></div>
<?php }?>
