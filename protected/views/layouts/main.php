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
<!doctype html> 
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>		<html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>		<html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head> 
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
 
	<title><?php echo CHtml::encode($this->pageTitle); ?></title> 
	<meta name="viewport" content="width=device-width, initial-scale=0.62">
	<?php if (Yii::app()->params['disable_browser_caching']) {?>
		<meta http-equiv='cache-control' content='no-cache'>
		<meta http-equiv='expires' content='0'>
		<meta http-equiv='pragma' content='no-cache'>
	<?php }?>
	<link rel="icon" href="<?php echo Yii::app()->createUrl('favicon.ico')?>" type="image/x-icon" /> 
	<link rel="shortcut icon" href="<?php echo Yii::app()->createUrl('favicon.ico')?>"/> 
	<?php $cs = Yii::app()->clientScript; ?>
	<?php $cs->registerCoreScript('jquery')?>
	<?php $cs->registerCoreScript('jquery.ui')?>
	<?php $cs->registerCSSFile($cs->getCoreScriptUrl().'/jui/css/base/jquery-ui.css', 'screen')?>
	<?php $cs->registerScriptFile(Yii::app()->createUrl('js/jquery.watermark.min.js'))?>
	<?php $cs->registerScriptFile(Yii::app()->createUrl('js/mustache.js'))?>
	<?php $cs->registerScriptFile(Yii::app()->createUrl('js/waypoints.min.js'))?>
	<?php $cs->registerScriptFile(Yii::app()->createUrl('js/waypoints-sticky.min.js'))?>
	<?php $cs->registerScriptFile(Yii::app()->createUrl('js/libs/modernizr-2.0.6.min.js'))?>
	<?php $cs->registerScriptFile(Yii::app()->createUrl('js/jquery.printElement.min.js'))?>
	<?php $cs->registerScriptFile(Yii::app()->createUrl('js/jquery.hoverIntent.min.js'))?>
	<?php $cs->registerScriptFile(Yii::app()->createUrl('js/print.js'))?>
	<?php $cs->registerScriptFile(Yii::app()->createUrl('js/buttons.js'))?>
	<?php $cs->registerScriptFile(Yii::app()->createUrl('js/script.js'))?>
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
	<?php echo $this->renderPartial('//base/_banner_watermark',array())?>
	<?php echo $this->renderPartial('//base/_debug',array())?> 
	<div id="container"> 
		<div id="header" class="clearfix">
			<?php echo $this->renderPartial('//base/_brand'); ?>
			<?php echo $this->renderPartial('//base/_form', array()); ?>
		</div> <!-- #header --> 
		<!--div id="mainmenu">
			<?php $this->widget('zii.widgets.CMenu',array(
				'items'=>array(
					array('label'=>'Home', 'url'=>array('/site/index'), 'visible'=>!Yii::app()->user->isGuest),
					array('label'=>'Admin', 'url'=>array('/admin'), 'visible'=>!Yii::app()->user->isGuest),
					array('label'=>'Search Patients', 'url'=>array('/patient/admin'), 'visible'=>!Yii::app()->user->isGuest),
					array('label'=>'Phrases for this firm', 'url'=>array('/phraseByFirm/index'), 'visible'=>!Yii::app()->user->isGuest),
					array('label'=>'Login', 'url'=>array('/site/login'), 'visible'=>Yii::app()->user->isGuest),
					array('label'=>'Logout ('.Yii::app()->user->name.')', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest)
				),
			)); ?>
		</div--><!-- mainmenu -->
		<div id="content"> 
			<?php echo $content; ?>
		</div><!-- #content --> 
		<div id="help" class="clearfix"> 
			<?php /*
			<div class="hint">
				<p><strong>Do you need help with OpenEyes?</strong></p>
				<p>Before you contact the helpdesk...</p>
				<p>Is there a "Super User" in your office available? (A "Super User" is...)</p>
				<p>Have you checked the <a href="#">Quick Reference Guide?</a></p>
			</div>
			<div class="hint">
				<p><strong>Searching by patient details.</strong></p>
				<p>Although the Last Name is required it doesn't have to be complete. For example if you search for "Smi", the results will include all last names starting with "Smi...". Any other information you can add will help narrow the search results.</p>
			</div>

			<div class="hint">
				<p><strong>Still need help?</strong></p>
				<p>Contact the helpdesk:</p>
				<p>Telephone: <?php echo Yii::app()->params['helpdesk_phone']?></p>
				<p>Email: <a href="mailto:<?php echo Yii::app()->params['helpdesk_email']?>"><?php echo Yii::app()->params['helpdesk_email']?></a></p>
			</div>
			*/?>
		</div><!-- #help --> 
	</div><!--#container --> 

	<?php echo $this->renderPartial('//base/_footer',array())?>
 
</body> 
</html>
