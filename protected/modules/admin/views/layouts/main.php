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
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

  <title>Open Eyes - Admin</title>
  <meta name="viewport" content="width=device-width">

  <link rel="icon" href="/favicon.ico" type="image/x-icon" />
  <link rel="shortcut icon" href="/favicon.ico"/>
	<?php Yii::app()->clientScript->registerCssFile('/css/style.css'); ?>
	<?php Yii::app()->clientScript->registerCssFile('/css/admin.css'); ?>
  <script src="/js/libs/modernizr-2.0.6.min.js"></script>
</head>

<body>

  <div id="container">
	<div id="header" class="clearfix">

		<?php echo $this->renderPartial('//base/_brand'); ?>
		
		<div id="user_panel" class="inAdmin">
			<div class="clearfix">
				<div id="user_id">
					Hi <strong>Bob Andrews</strong>&nbsp;<a href="#" class="small">(not you?)</a>
				</div>
				
				<ul id="user_nav">

					<li><a href="/">OpenEyes Home</a></li>
					<li><a href="/site/logout" class="logout">Logout</a></li>
				</ul>
			</div>
		</div> <!-- #user_panel -->
	</div> <!-- #header -->
	
	<div id="content" class="adminMode">

		<h2 class="admin">ADMIN</h2>
		<div id="mainmenu" class="fullBox" style="background:#ccc;">

		<?php $this->widget('zii.widgets.CMenu',array(
			'items'=>array(
				array('label'=>'Home', 'url'=>array('/site/index')),
				array('label'=>'Users', 'url'=>array('/admin/adminUser/index')),
				array('label'=>'Firms', 'url'=>array('/admin/adminFirm/index')),
				array('label'=>'Global phrases', 'url'=>array('/admin/adminPhrase/index')),
				array('label'=>'Phrases by subspecialty', 'url'=>array('/admin/adminPhraseBySubspecialty/index')),
				array('label'=>'Phrases by firm', 'url'=>array('/admin/adminPhraseByFirm/index')),
				array('label'=>'Letter Templates', 'url'=>array('/admin/adminLetterTemplate/index')),
				array('label'=>'Sequences', 'url'=>array('/admin/adminSequence/index')),
				array('label'=>'Sessions', 'url'=>array('/admin/adminSession/index')),
				//array('label'=>'Ophthalmic Disorders', 'url'=>array('/admin/adminCommonOphthalmicDisorder/index')),
				//array('label'=>'Systemic Disorders', 'url'=>array('/admin/adminCommonSystemicDisorder/index')),
				// Removed because the typical admin shouldn't be able to alter site_element_types. Surely they are the domain of the sysadmin?
				//array('label'=>'Site Element Types', 'url'=>array('/admin/adminSiteElementType')),
				array('label'=>'Episode status','url'=>array('/admin/adminEpisodeStatus/index')),
				array('label'=>'Login', 'url'=>array('/site/login'), 'visible'=>Yii::app()->user->isGuest),
				array('label'=>'Logout ('.Yii::app()->user->name.')', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest)
			),
		)); ?>
		</div>	<!-- #mainmenu -->
		
		<div class="whiteBox"> <!-- REPLACEs <div id="content"> in current HTML structure (already have a "content" id) -->

		<div id="sidebar">
			<div id="yw2" class="portlet">
				<div class="portlet-content">

                <?php
                        $this->beginWidget('zii.widgets.CPortlet', array(
                                'title'=>'Operations',
                        ));
                        $this->widget('zii.widgets.CMenu', array(
                                'items'=>$this->menu,
                                'htmlOptions'=>array('class'=>'operations'),
                        ));
                        $this->endWidget();
                ?>
				</div> <!-- .portlet-content -->
			</div>	<!-- .portlet -->	
		</div> <!-- #sidebar -->

	<?php echo $content; ?>

	</div> <!-- .whiteBox -->
		
	</div> <!-- #content -->

	<div id="help" class="clearfix">
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
			<p>Telephone: 01234 12343567 ext. 0000</p>
			<p>Email: <a href="#">helpdesk@openeyes.org.uk</a></p>
		</div>
		
	</div> <!-- #help -->
  </div> 
  <!--#container -->

  
  <div id="footer">
		<h6>&copy; Copyright OpenEyes Foundation 2011&#x2013;<?php echo date('Y'); ?>&nbsp;&nbsp;|&nbsp;&nbsp;
			<a href="<?php echo Yii::app()->createUrl('site/debuginfo')?>" id="support-info-link">served, with love, by <?php echo trim(`hostname`)?></a></h6>
  </div> <!-- #footer -->

</body>
</html>
