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
$whiteboard_display_mode = SettingMetadata::model()->getSetting('theatre_diary_whiteboard_display_mode');
$coreapi = new CoreAPI();

if (empty($diary)) { ?>
    <div id="theatre-search-no-results">
        <div class="cols-12 column">
            <div class="alert-box"><strong>No theatre schedules match your search criteria.</strong></div>
        </div>
    </div>
<?php } else { ?>
    <?php $list_t = microtime(true);?>
    <div class="theatre-diary-list">
        <?php foreach ($diary as $theatre) { ?>
            <h2>
                <?php echo $theatre->name ?> (<?php echo $theatre->site->name ?>)
            </h2>
            <?php foreach ($theatre->sessions as $session) {
                $this->renderPartial(
                    '_session',
                    array(
                        'session' => $session,
                        'theatre' => $theatre,
                        'assetPath' => $assetPath,
                        'ward_id' => $ward_id,
                        'coreapi' => $coreapi,
                        'whiteboard_display_mode' => $whiteboard_display_mode
                    )
                );
                break;
            }
        } ?>
    </div>

    <!-- Theatre diary list rendered: <?= microtime(true) - $list_t;?> -->
<?php } ?>
<script>
    $(document).ready(function () {
        theatreDiaryIconHovers();
        // Add handler for Display Whiteboard icon when the display mode is 'Always open in new window/tab'.
        // The other display mode handler is already handled within module.js.
        $(document).on('click', '#js-display-whiteboard-window, #js-display-whiteboard-window_footer', function () {
            window.open('/OphTrOperationbooking/whiteboard/view/' + $(this).data('id'), '_blank');
        });
    });
</script>
