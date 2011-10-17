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
	<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/libs/modernizr-2.0.6.min.js"></script> 
</head> 
 
<body> 
 
	<div id="container"> 
	<div id="header" class="clearfix"> 
		<div id="brand" class="ir"><a href="/site/index"><h1>OpenEyes</h1></a></div> 
	</div> <!-- #header --> 

	<?php $this->widget('zii.widgets.CBreadcrumbs', array(
		'links'=>$this->breadcrumbs,
	)); ?><!-- breadcrumbs -->

	<div id="content"> 
		<?php echo $content; ?>
	</div> <!-- #content --> 
	<div id="help" class="clearfix"> 
		<div class="hint">
			<p><strong>Login Help</strong></p>
			<p>User name and password are case sensitive: "A" is different to "a".</p>
			<p>Ensure that CAPs LOCK is off.</p>
		</div>

		<div class="hint">
			<p><strong>Do you require a username and password?</strong></p>
			<p>Contact the helpdesk:</p>
			<p>Telephone: 01234 12343567 ext. 0000</p>
			<p>Email: <a href="#">helpdesk@openeyes.org.uk</a></p>
		</div>
	</div> <!-- #help --> 
	</div> 
	<!--#container --> 
	
	<div id="footer"> 
		<h6>&copy; Copyright OpenEyes Foundation 2011 &nbsp;&nbsp;|&nbsp;&nbsp; Terms of Use &nbsp;&nbsp;|&nbsp;&nbsp; Legals</h6> 
	</div> <!-- #footer --> 
 
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
	<script>window.jQuery || document.write('<script src="js/libs/jquery-1.6.2.min.js"><\/script>')</script>

</body> 
</html> 
