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

?><!doctype html>
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>		<html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>		<html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	
	
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
	<meta name="viewport" content="width=device-width">
	<?php if (Yii::app()->params['disable_browser_caching']) {?>
		<meta http-equiv='cache-control' content='no-cache'>
		<meta http-equiv='expires' content='0'>
		<meta http-equiv='pragma' content='no-cache'>
	<?php }?>
	<link rel="icon" href="<?php echo Yii::app()->createUrl('favicon.ico')?>" type="image/x-icon" />
	<link rel="shortcut icon" href="<?php echo Yii::app()->createUrl('favicon.ico')?>"/>
	<?php Yii::app()->clientScript->registerCoreScript('jquery'); ?>
	<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->createUrl('js/jui/js/jquery-ui.min.js'))?>
	<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->createUrl('js/jquery.watermark.min.js'))?>
	<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->createUrl('js/mustache.js'))?>
	<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->createUrl('js/libs/modernizr-2.0.6.min.js'))?>
	<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->createUrl('js/jquery.printElement.min.js'))?>
	<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->createUrl('js/jquery.autosize-min.js'))?>
	<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->createUrl('js/print.js'))?>
	<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->createUrl('js/buttons.js'))?>
	<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->createUrl('js/events_and_episodes.js'))?>
	<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->createUrl('/js/jquery.textarea.js'))?>
	<?php if (Yii::app()->params['google_analytics_account']) {?>
		<script type="text/javascript">

			var _gaq = _gaq || [];
			_gaq.push(['_setAccount', '<?php echo Yii::app()->params['google_analytics_account']?>']);
			_gaq.push(['_trackPageview']);

			(function() {
				var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
				ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
				var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
			})();

		</script>
	<?php }?>
	<script type="text/javascript">
		var baseUrl = '<?php echo Yii::app()->baseUrl?>';
	</script>
</head>

<body>
	<?php if (Yii::app()->user->checkAccess('admin')) {?>
		<div class="h1-watermark-admin"><?php echo Yii::app()->params['watermark_admin']?></div>
	<?php } else if (Yii::app()->params['watermark']) {?>
		<div class="h1-watermark"><?php echo Yii::app()->params['watermark']?></div>
	<?php }?>
	<?php echo $this->renderPartial('//base/_debug',array())?>
	<div id="container">
		<div id="header" class="clearfix">
			<div id="brand" class="ir"><h1><?php echo CHtml::link('OpenEyes',array('site/'))?></h1></div>
			<?php echo $this->renderPartial('//base/_form', array()); ?>
			<div id="patientID">
				<div class="i_patient">
					<?php echo CHtml::link('Patient Summary',array('/patient/view/'.$this->patient->id),array('class'=>'small'))?>
					<img class="i_patient" src="<?php echo Yii::app()->createUrl('img/_elements/icons/patient_small.png')?>" alt="patient_small" width="26" height="30" />
				</div>

				<div class="patientReminder">
					<?php echo $this->patient->getDisplayName()?>
					<span class="number">Hospital number: <?php echo $this->patient->hos_num?></span>
					<span class="number">NHS number: <?php echo $this->patient->nhsnum?></span>
				</div>
			</div> <!-- #patientID -->

		</div> <!-- #header -->

		<script type="text/javascript"> var et_patient_id = <?php echo $this->patient->id?>; </script>
		<div id="content">
