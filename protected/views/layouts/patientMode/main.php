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

<body onload="eyedraw_init();">

<div class="container" id="page">

	<div id="header">
		<div id="logo"><img src="/images/logo_colour.png" alt="OpenEyes Logo" /></div>

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
	$('select[id=selected_firm_id]').live('change', function() {
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
