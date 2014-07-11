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

<footer class="footer row">
		<div class="large-3 medium-3 columns">
				<div class="info">
						<a href="<?php echo Yii::app()->createUrl('site/debuginfo')?>" id="support-info-link">Served, with love, by <?php echo trim(`hostname`)?></a>
						<br />
						<span class="copyright">&copy; Copyright OpenEyes Foundation 2011&#x2013;<?php echo date('Y'); ?></span>
				</div>
		</div>
		<div class="large-6 medium-6 large-centered medium-centered columns help">
				<div class="panel">
						<ul class="inline-list">
								<li><strong>Need help?</strong></li>
								<?php if (Yii::app()->params['helpdesk_email']) {?>
									<li><?php echo Yii::app()->params['helpdesk_email']?></li>
								<?php }?>
								<?php if (Yii::app()->params['helpdesk_phone']) {?>
									<li><strong><?php echo Yii::app()->params['helpdesk_phone'] ?></strong></li>
								<?php } ?>
								<?php if (Yii::app()->params['help_url']) {?>
									<li><?php echo CHtml::link('Help Documentation', Yii::app()->params['help_url'], array('target'=>'_blank')) ?></li>
								<?php } ?>
						</ul>
				</div>
		</div>
</footer>

<script type="text/javascript">
$(document).ready(function() {
	$('#support-info-link').live('click',function(e) {
		e.preventDefault();
		new OpenEyes.UI.Dialog({
			url: this.href,
			title: 'Support Information'
		}).open();
	});
});
</script>

<?php
	$this->renderPartial('//base/_script_templates', array());
?>