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
	<div id="footer">
		<h6>&copy; Copyright OpenEyes Foundation 2011&#x2013;<?php echo date('Y'); ?>&nbsp;&nbsp;|&nbsp;&nbsp;
			<a href="<?php echo Yii::app()->createUrl('site/debuginfo')?>" id="support-info-link">served, with love, by <?php echo trim(`hostname`)?></a></h6>
		<div class="help">

				<span><strong>Need Help?</strong></span>
				<span class="divider">|</span>
				<?php if (Yii::app()->params['helpdesk_email']) {?>
					<span><?php echo Yii::app()->params['helpdesk_email']?></span>
				<?php }?>
					<span class="divider">|</span>
				<?php if (Yii::app()->params['helpdesk_phone']) {?>
					<span><strong><?php echo Yii::app()->params['helpdesk_phone'] ?></strong></span>
				<?php } ?>
				<span class="divider">|</span>
				<span><?php echo CHtml::link('Help Documentation','http://mehhome/about/trust-wide-projects/openeyes/training-arrangements/',array('target'=>'_new'))?></span>
		</div>
	</div> <!-- #footer -->

<script type="text/javascript">
$(document).ready(function() {
	$('#support-info-link').live('click',function() {
		var debug = $('<div></div>')
		.html('<iframe style="border: 0px; " src="<?php echo Yii::app()->createUrl('site/debuginfo')?>" width="360px" height="300px"></iframe>')
		.dialog({
			'autoOpen': true,
			'modal': true,
			'height': 360,
			'width': 400,
			'resizable': false,
			'title': 'Support Information',
		});
		return false;
	});
});
</script>
