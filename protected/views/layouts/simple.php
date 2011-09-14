<!doctype html> 
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]--> 
<!--[if IE 7]>		<html class="no-js ie7 oldie" lang="en"> <![endif]--> 
<!--[if IE 8]>		<html class="no-js ie8 oldie" lang="en"> <![endif]--> 
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]--> 
<head> 
	<meta charset="utf-8"> 
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
 
	<title><?php echo CHtml::encode($this->pageTitle); ?></title> 
	<meta name="viewport" content="width=device-width"> 
	<link rel="icon" href="favicon.ico" type="image/x-icon" /> 
	<link rel="shortcut icon" href="<?php echo Yii::app()->request->baseUrl; ?>/favicon.ico"/> 
	<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl; ?>/css/style.css"> 
	<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/libs/modernizr-2.0.6.min.js"></script> 
</head> 
 
<body> 
 
	<div id="container"> 
	<div id="header" class="clearfix"> 
		<div id="brand" class="ir"><h1>OpenEyes</h1></div> 
	</div> <!-- #header --> 

	<?php $this->widget('zii.widgets.CBreadcrumbs', array(
		'links'=>$this->breadcrumbs,
	)); ?><!-- breadcrumbs -->

	<div id="content"> 
		<?php echo $content; ?>
	</div> <!-- #content --> 
	<div id="help" class="clearfix"> 
	</div> <!-- #help --> 
	</div> 
	<!--#container --> 
	
	<div id="footer"> 
		<h6>&copy; Copyright OpenEyes Foundation 2011 &nbsp;&nbsp;|&nbsp;&nbsp; Terms of Use &nbsp;&nbsp;|&nbsp;&nbsp; Legals</h6> 
	</div> <!-- #footer --> 
 
 
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script> 
	<script>window.jQuery || document.write('<script src="js/libs/jquery-1.6.2.min.js"><\/script>')</script> 
 
	<script defer src="<?php echo Yii::app()->request->baseUrl; ?>/js/plugins.js"></script> 
	<script defer src="<?php echo Yii::app()->request->baseUrl; ?>/js/script.js"></script> 
	
</body> 
</html> 
