<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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

<body>

<div class="container" id="page">

	<div id="header">
		<div id="logo"><?php echo CHtml::encode(Yii::app()->name); ?> ADMIN PAGE</div>
	</div><!-- header -->

	<div id="mainmenu">
		<?php $this->widget('zii.widgets.CMenu',array(
			'items'=>array(
				array('label'=>'Home', 'url'=>array('/site/index')),
				array('label'=>'Users', 'url'=>array('/admin/adminUser/index')),
				array('label'=>'Firms', 'url'=>array('/admin/adminFirm/index')),
				array('label'=>'Global phrases', 'url'=>array('/admin/adminPhrase/index')),
				array('label'=>'Phrases by specialty', 'url'=>array('/admin/adminPhraseBySpecialty/index')),
				array('label'=>'Phrases by firm', 'url'=>array('/admin/adminPhraseByFirm/index')),
				array('label'=>'Letter Templates', 'url'=>array('/admin/adminLetterTemplate/index')),
				array('label'=>'Contact Types', 'url'=>array('/admin/adminContactType/index')),
				array('label'=>'Ophthalmic Disorders', 'url'=>array('/admin/adminCommonOphthalmicDisorder/index')),
				array('label'=>'Systemic Disorders', 'url'=>array('/admin/adminCommonSystemicDisorder/index')),
				array('label'=>'Site Element Types', 'url'=>array('/admin/adminSiteElementType')),
				array('label'=>'Login', 'url'=>array('/site/login'), 'visible'=>Yii::app()->user->isGuest),
				array('label'=>'Logout ('.Yii::app()->user->name.')', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest)
			),
		)); ?>
	</div><!-- mainmenu -->

	<?php $this->widget('zii.widgets.CBreadcrumbs', array(
		'links'=>$this->breadcrumbs,
	)); ?><!-- breadcrumbs -->

	<?php echo $content; ?>

	<div id="footer">
		Copyright OpenEyes Foundation 2011<br/>
	</div><!-- footer -->

</div><!-- page -->

</body>
</html>
