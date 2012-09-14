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
	<div id="footer">
		<h6>&copy; Copyright OpenEyes Foundation 2011&#x2013;2012 &nbsp;&nbsp;|<!--&nbsp;&nbsp; Terms of Use &nbsp;&nbsp;|&nbsp;&nbsp; Legals &nbsp;&nbsp;|-->&nbsp;&nbsp; <a href="<?php echo Yii::app()->createUrl('site/debuginfo')?>" id="support-info-link">served, with love, by <?php echo trim(`hostname`)?></a></h6>
		<div class="help">

				<span><strong>Need Help?</strong></span>
				<span class="divider">|</span>
				<?php if (Yii::app()->params['helpdesk_email']) {?>
					<span>email: <a href="mailto:<?php echo Yii::app()->params['helpdesk_email']?>"><?php echo Yii::app()->params['helpdesk_email'] ?></a></span>
				<?php }?>
					<span class="divider">|</span>
				<?php if (Yii::app()->params['helpdesk_phone']) {?>
					<span>phone: <strong><?php echo Yii::app()->params['helpdesk_phone'] ?></strong></span>
				<?php } ?>
				<span class="divider">|</span>
				<span><a href="/pdf/OpenEyesOnlineHelp.pdf" target="_new">Online Help Documentation</a></span>
		</div>
	</div> <!-- #footer -->

<?php $this->widget('application.extensions.fancybox.EFancyBox', array(
	'target'=>'#support-info-link',
	'config'=>array()
	));
?>
