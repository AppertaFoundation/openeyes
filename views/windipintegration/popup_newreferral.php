<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

?>

<div class="element">
	<div class="element-data">
		<div class="row data-row">
			<div class="large-6 column hidden end" id="external-referral-popup-blocked">
				Unable to automatically open WinDip. Please click the button below.
			</div>
		</div>
		<div class="row data-row">
			<div class="large-6 column" id="external-referral-button">
				<a href="<?=$external_link?>" class="button primary small">click to view</a>
			</div>
		</div>
		<div class="row data-row">
			<div class="large-12 column hidden" id="external-referral-status">
				placeholder for displaying the status information and/or link for the referral in windip.
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">

	$(document).on('ready', function() {
		OpenEyes.UI.Window.createNewWindow('<?= $external_link?>', 'Internalreferralintegration',
			function(popup) {
				popup.focus();
				$('#external-referral-button').addClass('hidden');
				$('#external-referral-status').removeClass('hidden');
			},
			function() {
				$('#external-referral-popup-blocked').removeClass('hidden');
			}
		);
	});
</script>