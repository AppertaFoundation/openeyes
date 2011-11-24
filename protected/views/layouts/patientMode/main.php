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
http://www.openeyes.org.uk	 info@openeyes.org.uk
--
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
	<link rel="icon" href="favicon.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="<?php echo Yii::app()->request->baseUrl; ?>/favicon.ico"/>
	<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl; ?>/css/style.css">
	<link rel="stylesheet" type="text/css" href="/css/jquery.fancybox-1.3.4.css" />
	<script type="text/javascript" src="/js/jquery.js"></script>
	<script type="text/javascript" src="/js/jui/js/jquery-ui.min.js"></script>
	<script type="text/javascript" src="/js/jquery.watermark.min.js"></script>
	<script type="text/javascript" src="/js/jquery.fancybox-1.3.4.pack.js"></script>
	<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/libs/modernizr-2.0.6.min.js"></script>
	<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.printElement.min.js"></script>

</head>

<body>
	<?php if (Yii::app()->params['watermark']) {?>
		<div class="h1-watermark"><?php echo Yii::app()->params['watermark']?></div>
	<?php }?>

	<?php echo $this->renderPartial('/base/_debug',array())?>
	<div id="container">
		<div id="header" class="clearfix">
			<div id="brand" class="ir"><a href="/site/index"><h1>OpenEyes</h1></a></div>
			<?php echo $this->renderPartial('//base/_form', array()); ?>
			<div id="patientID">
				<div class="i_patient">
					<a href="/patient/view/<?php echo $this->model->id?>" class="small">View Summary</a>
					<img class="i_patient" src="/img/_elements/icons/patient_small.png" alt="patient_small" width="26" height="30" />
				</div>

				<div class="patientReminder">
					<span class="given"><?php echo $this->model->first_name?></span>
					<span class="surname"><?php echo $this->model->last_name?></span>
					<span class="number"><?php echo $this->model->hos_num?></span>
				</div>
			</div> <!-- #patientID -->

		</div> <!-- #header -->

		<div id="content">
			<?php echo $content; ?>




  <!-- ====================================================  P R I N T  S T U F F ============  -->
  <div class="printable" id="printable">

  </div> <!-- end of printable area -->

  <!-- ====================================================  end of P R I N T  S T U F F ============  -->






		</div> <!-- #content -->
		<div id="help" class="clearfix">
			<?php /*
			<div class="hint">
				<p><strong>Online Help</strong></p>
				<p><a href="#">Quick Reference Guide</a></p>
				<p>&nbsp;</p>
				<p><strong>Helpdesk</strong></p>
				<p>Telephone: <?php echo Yii::app()->params['helpdesk_phone']?></p>
				<p>Email: <a href="mailto:<?php echo Yii::app()->params['helpdesk_email']?>"><?php echo Yii::app()->params['helpdesk_email']?></a></p>
			</div>
			*/?>
		</div>
	</div>
	<!--#container -->

	<?php echo $this->renderPartial('/base/_footer',array())?>

	<script defer src="<?php echo Yii::app()->request->baseUrl; ?>/js/plugins.js"></script>
	<script defer src="<?php echo Yii::app()->request->baseUrl; ?>/js/script.js"></script>

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

		function clearPrintContent() {
			$('#printable').html('');
		}

		function appendPrintContent(content) {
			$('#printable').append(content);
		}

		function printContent() {
			printElem({
				pageTitle:'openeyes printout',
				printBodyOptions:{styleToAdd:'width:auto !important; margin: 0.75em !important;',classNameToAdd : 'openeyesPrintout'},
				overrideElementCSS:['css/style.css',{href:'css/style.css',media:'print'}]
			});
		}

		function printElem(options){
			$('#printable').printElement(options);
		}
	</script>

	<?php if (Yii::app()->params['watermark']) {?>
		<div class="h1-watermark"><?php echo Yii::app()->params['watermark']?></div>
	<?php }?>

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
</body>
</html>
