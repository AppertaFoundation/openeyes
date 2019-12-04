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
<script>
    $(function () {
        $('div[id^=more_]').each(function (i, el) {
            var j = i + 1;
            $('#more_' + j).click(function () {
                $("#dialog_" + j).dialog({
                    width: "40%",
                    maxWidth: "700px"
                });
                return false;
            });
        });
    });

    function reprocessFile(fileName, buttonObj){
        $(buttonObj).hide();

        $.ajax({
            method: "POST",
            url: "/DicomLogViewer/reprocess",
            data: { filename: fileName }
        }).done(function() {
            $(buttonObj).html('OK - Reprocess has been scheduled').show();
        });
    }
</script>
<tr data-id="<?php echo $i + 1 ?>" data-filename="<?php echo basename($log['filename']); ?>"
    processor_id="<?php echo $log['processor_id']; ?>" status="<?php echo $log['status']; ?>">
    <td> <?php echo wordwrap(basename($log['filename']), 12, "\n", true); ?></td>
    <td> <?php echo $log['import_datetime']; ?></td>
    <td> <?php echo $log['study_datetime']; ?></td>
    <td> <?php echo $log['station_id']; ?></td>
    <td> <?php echo $log['study_location']; ?></td>
    <td> <?php echo $log['report_type']; ?></td>
    <td> <a href="/patient/view/<?php echo $log['patient_id']?>"> <?php echo $log['patient_number']; ?></a></td>
    <td> <?php echo $log['status']; ?></td>
    <td> <?php echo wordwrap($log['study_instance_id'], 12, "\n", true); ?></td>
    <td> <?php echo $log['comment']; ?></td>
    <td><i>
            <div id="more_<?php echo $i + 1 ?>"><a>More</a></div>
        </i>

        <div style="display:none; width:500px;" class="dialogbox" id="dialog_<?php echo $i + 1 ?>" title="More Info"
             data-id="<?php echo $i + 1 ?>">
            <p><b><?php echo basename($log['filename']) ?></b></p>
            <button onclick="reprocessFile('<?php echo $log['filename']?>', this)" style="float:right;margin-bottom: 20px;">Reprocess file</button>
            <p><b>History</b> <br>
            <table class="standard audit-logs">
                <thead>
                <tr>
                    <th>Status</th>
                    <th>Time Stamp</th>
                    <th>Process Name</th>
                    <th>Process Server ID</th>
                </tr>
                </thead>
                <tbody id="fileWatcherHistoryData">
                <?php
                foreach ($log['watcher_log'] as $k => $y) {
                    ?>
                    <tr data-id="<?php echo $k + 1; ?>" filename="<?php echo basename($log['filename']); ?>"
                        status="<?php echo $y['status']; ?>">
                        <td><?php echo $y['status'] ?></td>
                        <td><?php echo $y['event_date_time'] ?></td>
                        <td><?php echo $y['process_name'] ?></td>
                        <td><?php echo $log['processor_id'] ?></td>
                    </tr>
                    <?php
                } ?>
                </tbody>
            </table>
            </p>

            <p><b>Machine Details</b></p>
            <table class="standard audit-logs">
                <tbody id="machineDetailsData">
                <tr>
                    <td>Make :</td>
                    <td><?php echo $log['machine_manufacturer'] ?></td>
                </tr>
                <tr>
                    <td>Model :</td>
                    <td><?php echo $log['machine_model'] ?></td>
                </tr>
                <tr>
                    <td>Software Version :</td>
                    <td><?php echo $log['machine_software_version'] ?></td>
                </tr>
                </tbody>
            </table>

            <div id="debug_data">
            <p><b>Debug data :</b></p>

            <p >
            <textarea rows="10" cols="50" >
                <?php echo trim($log['raw_importer_output']); ?>
            </textarea>
            </p>

            </div>
            
        </div>
    </td>
</tr>
