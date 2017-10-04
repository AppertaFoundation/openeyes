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
if (empty($diary)) {?>
	<div class="row" id="theatre-search-no-results">
		<div class="large-12 column">
			<div class="alert-box"><strong>No theatre schedules match your search criteria.</strong></div>
		</div>
	</div>
<?php } else {
    foreach ($diary as $theatre) {?>
		<div class="row">
			<div class="large-12 column">
				<h2><?php echo $theatre->name?> (<?php echo $theatre->site->name?>)</h2>
			</div>
		</div>
		<?php foreach ($theatre->sessions as $session) {
    $this->renderPartial('_session', array('session' => $session, 'theatre' => $theatre, 'assetPath' => $assetPath, 'ward_id' => $ward_id));
}
    }
}
?>
<script type="text/javascript">
	$(document).ready(function() {
		theatreDiaryIconHovers();
	});
</script>
