<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>
<!doctype html>
<!--[if lt IE 7]><html class="no-js ie6 oldie" lang="en"><![endif]-->
<!--[if IE 7]><html class="no-js ie7 oldie" lang="en"><![endif]-->
<!--[if IE 8]><html class="no-js ie8 oldie" lang="en"><![endif]-->
<!--[if gt IE 8]><!--><html class="no-js" lang="en"><!--<![endif]-->
<head>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<title>OpenEyes - Login</title>
<meta name="viewport" content="width=device-width" />
<link rel="icon" href="<?php echo Yii::app()->createUrl('favicon.ico')?>" type="image/x-icon" />
<link rel="shortcut icon" href="<?php echo Yii::app()->createUrl('favicon.ico')?>" />
<?php Yii::app()->getClientScript()->registerCSSFile(Yii::app()->createUrl('css/style-error.css'))?>
</head>
<body>
	<div id="container">
		<div id="header" class="clearfix">
			<?php echo $this->renderPartial('//base/_brand'); ?>
		</div><!-- #header -->
		<div id="content">
			<div id="down-form" class="form_greyBox">
				<?php echo $content; ?>
			</div>
		</div><!-- #content -->
		<div id="help" class="clearfix"></div><!-- #help -->
	</div><!--#container -->
	<div id="footer">
		<h6>&copy; Copyright OpenEyes Foundation 2011&#x2013;<?php echo date('Y'); ?>&nbsp;&nbsp;|&nbsp;&nbsp;
			<a href="<?php echo Yii::app()->createUrl('site/debuginfo')?>" id="support-info-link">served, with love, by <?php echo trim(`hostname`)?></a></h6>
	</div><!-- #footer -->
</body>
</html>
