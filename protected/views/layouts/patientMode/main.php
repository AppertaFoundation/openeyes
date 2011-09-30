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

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />

	<!-- blueprint CSS framework -->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print" />
	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css" media="screen, projection" />
	<![endif]-->

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body onload="eyedraw_init();">

<div class="container" id="page">

	<div id="header">
		<div id="logo"><a href="/site/index"><img src="/images/logo_colour.png" alt="OpenEyes Logo" /></a></div>

		<?php echo $this->renderPartial('//base/_form', array()); ?>
	</div><!-- header -->

	<div id="outer_content">
		<div id="content">
			<?php $this->widget('application.components.MyBreadcrumbs', array(
				'homeLink'=>CHtml::link('OpenEyes', array('site/index')),
				'links'=>$this->breadcrumbs,
				'prefixText'=>'You are here: &nbsp; ',
			)); ?><!-- breadcrumbs -->
			<?php echo $content; ?>
		</div>
	</div>

	<div id="footer">
		Copyright OpenEyes Foundation 2011<br/>
	</div><!-- footer -->

</div><!-- page -->
<?php echo EyeDrawService::activeEyeDrawInit($this); ?>

<script type="text/javascript">
	$('select[id=selected_firm_id]').die('change').live('change', function() {
		var firmId = $('select[id=selected_firm_id]').val();
		$.ajax({
			type: 'post',
			url: '<?php echo Yii::app()->createUrl('site'); ?>',
			data: {'selected_firm_id': firmId },
			success: function(data) {
				console.log(data);
				window.location.href = '<?php echo Yii::app()->createUrl('site'); ?>';
			}
		});
	});
</script>
</body>
</html>
