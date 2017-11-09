<?php
/**
* OpenEyes.
*
* (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
* (C) OpenEyes Foundation, 2011-2013
* This file is part of OpenEyes.
* OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
* OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
* You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
*
* @link http://www.openeyes.org.uk
*
* @author OpenEyes <info@openeyes.org.uk>
* @copyright Copyright (c) 2011-2013, OpenEyes Foundation
* @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
*/
?>



<footer class="footer row">
	<div class="large-3 medium-3 columns">
		<div class="info">
			<a href="<?php echo Yii::app()->createUrl('site/debuginfo') ?>" id="support-info-link">Served by <?php echo trim(`hostname`) ?></a>
			<br/>
			<span class="copyright">&copy; Copyright OpenEyes Foundation 2011&#x2013;<?php echo date('Y'); ?></span>
		</div>
	</div>
	<div class="large-6 medium-6 large-centered medium-centered columns help">
		<div class="panel">
			<ul class="inline-list">
				<li><strong>Need help?</strong></li>
				<?php if (Yii::app()->params['helpdesk_email']) { ?>
					<li><?php echo Yii::app()->params['helpdesk_email'] ?></li>
				<?php } ?>
				<?php if (Yii::app()->params['helpdesk_phone']) { ?>
					<li><strong><?php echo Yii::app()->params['helpdesk_phone'] ?></strong></li>
				<?php } ?>
				<?php if (Yii::app()->params['help_url']) { ?>
					<li><?php echo CHtml::link('Help Documentation', Yii::app()->params['help_url'],
					array('target' => '_blank')) ?></li>
				<?php } ?>
			</ul>
		</div>
	</div>
	<script>
	if (uservoice_enabled == 'on') {
		// Include the UserVoice JavaScript SDK (only needed once on a page)
		UserVoice=window.UserVoice||[];(function(){var uv=document.createElement('script');uv.type='text/javascript';uv.async=true;uv.src='https://widget.uservoice.com/xiXrGR5j7JSb6wqDtOQJw.js';var s=document.getElementsByTagName('script')[0];s.parentNode.insertBefore(uv,s)})();

		//
		// UserVoice Javascript SDK developer documentation:
		// https://www.uservoice.com/o/javascript-sdk
		//

		// Set colors
		UserVoice.push(['set', {
			accent_color: '#448dd6',
			trigger_color: 'white',
			trigger_background_color: '#448dd6',
			forum_id: '601405',
			contact_enabled: false,
			screenshot_enabled: false
		}]);

		// Identify the user and pass traits
		// To enable, replace sample data with actual user traits and uncomment the line
		var use_logged_in = uservoice_use_logged_in_user == 'on' ? true : false;
		UserVoice.push(['identify', {
			email: use_logged_in ? user_email : "", // User’s email address
			name: use_logged_in ? user_full_name : "", // User’s real name
			//created_at: 1364406966, // Unix timestamp for the date the user signed up
			id: use_logged_in ? user_id : "", // Optional: Unique id of the user (if set, this should not change) user id
			//type:       'Owner', // Optional: segment your users by type
			account: { // Account traits are only available on some plans
				id: use_logged_in ? institution_code : uservoice_override_account_id, // Optional: associate multiple users with a single account instituion id remote id
				name: use_logged_in ? institution_name : uservoice_override_account_name, // Account name
				//  created_at:   1364406966, // Unix timestamp for the date the account was created
				//  monthly_rate: 9.99, // Decimal; monthly rate of the account
				//  ltv:          1495.00, // Decimal; lifetime value of the account
				//  plan:         'Enhanced' // Plan name for the account
			}
		}]);

		// Add default trigger to the bottom-right corner of the window:
		UserVoice.push(['addTrigger', {mode: 'feedback', trigger_position: 'bottom-right' }]);


		// Or, use your own custom trigger:
		//UserVoice.push(['addTrigger', '#id', { mode: 'contact' }]);

		// Autoprompt for Satisfaction and SmartVote (only displayed under certain conditions)
		UserVoice.push(['autoprompt', {}]);
	}
	</script>
</footer>

<script type="text/javascript">
$(document).foundation();
$(document).ready(function () {
	$('#support-info-link').live('click', function (e) {
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
