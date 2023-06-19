<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

?>
<div class="box admin">
    <div class="row">
        <div class="large-8 column">
            <h2>Signature Import Log</h2>
        </div>
        <div class="large-8 column" style="display: none">
            CVI <input type="radio" name="signature_import_type_change"
                       value="1" <?= ($type == SignatureImportLog::TYPE_CVI) ? 'checked' : '' ?>>
            Consent Form <input type="radio" name="signature_import_type_change"
                                value="2" <?= ($type == SignatureImportLog::TYPE_CONSENT) ? 'checked' : '' ?>>
        </div>
    </div>
    <form id="admin_institution_sites">
        <table class="standard cols-full" id="signature_import_log_table">
            <thead>
            <tr>
                <th>ID</th>
                <th><a href="/DicomLogViewer/signatureList?sortby=<?= $sortby == 'ASC' ? 'DESC' : 'ASC' ?>">Import
                        date</a></th>
                <th>Status</th>
                <th>Log</th>
                <th>Original</th>
                <th>Cropped</th>
                <th>Crop</th>
                <th>Patient Name</th>
                <th>HOS num.</th>
                <th>Event</th>
            </tr>
            </thead>
            <tbody data-test="result_body">
            <?php
            foreach ($logs as $i => $log) { ?>
                <tr>
                    <td><?= $log->id ?></td>
                    <td><?= $log->import_datetime ?></td>
                    <td><?= $log->getStatusName() ?></td>
                    <td>
                        <button class="signature_import_log_dialog_open" data-id="<?php
                        echo $log->id ?>" style="background-color: <?= $log->getStatusColor() ?>">Log
                        </button>
                    </td>
                    <td>
                        <div
                            class="button viewImageButton"
                            data-url="<?= $log->id ? "/DicomLogViewer/signatureImageView?id=" . $log->getProtectedFileIdFromLogId(
                                    $log->id
                                      ) : "//#0" ?>"
                        > Image
                        </div>
                    </td>
                    <td>
                        <?php
                        if ($log->cropped_file_id) { ?>
                            <div
                                class="button viewImageButton"
                                data-url="<?= $log->id ? "/DicomLogViewer/signatureImageView?id=" . $log->cropped_file_id : "//#0" ?>"
                            > Image
                            </div>
                            <?php
                        } ?>
                    </td>
                    <td>
                        <?php
                        if ($type == SignatureImportLog::TYPE_CVI) { ?>
                            <a class="button crop_button"
                               href="<?= $log->id ? "/DicomLogViewer/signatureCrop?id=" . $log->id . "&type=" . $type . "&page=" . $current_page : "//#0" ?>">Crop</a>
                            <?php
                        } ?>
                    </td>
                    <td>
                        <?php
                        if ($log->event) { ?>
                            <?= $log->event->episode->patient->getFullName(); ?>
                            <?php
                        } ?>
                    </td>
                    <td>
                        <?php
                        if ($log->event) { ?>
                            <?= $log->event->episode->patient->getHos(); ?>
                            <?php
                        } ?>
                    </td>
                    <td>
                        <?php
                        if ($log->event_id) { ?>
                            <a href="/OphCoCvi/default/view/<?= $log->event_id ?>">Open Event</a>
                            <?php
                        } ?>
                    </td>
                </tr>
                <div id="signature_import_log_dialog_<?= $log->id ?>" title="Original Import Log"
                     style="display: none;">
                    <div id="delete_diagnosis">
                        <div class="alert-box alert with-icon">
                            <strong><?= $log->return_message ?></strong>
                        </div>
                    </div>
                </div>
                <?php
            } ?>
            </tbody>
            <tfoot class="pagination-container">
            <tr>
                <td colspan="10">
                    <?php
                    echo $this->renderPartial('//dicomlogviewer/_signature_import_log_pagination', array(
                        'pagination' => $pagination,
                    )) ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </form>
</div>
<div id='imageViewer'>
    <div id="divInDialog"></div>
</div>
<script type="text/javascript">

    $('.viewImageButton').on('click', function () {
        let url = $(this).attr('data-url');
        image = new Image();
        image.src = url;
        image.onload = function () {
            $('#divInDialog').empty().append(image);
        };
        $('#divInDialog').dialog({
            resizable: false,
            modal: true,
            height: 800,
            width: 1200,
            buttons: [
                {
                    text: 'Cancel',
                    'class': 'button',
                    click: function () {
                        $(this).dialog('close');
                    }
                }
            ]
        });
    });

    $('.signature_import_log_dialog_open').click(function () {
        var data_id = $(this).attr('data-id');
        $('#signature_import_log_dialog_' + data_id).dialog({
            resizable: false,
            modal: true,
            buttons: [
                {
                    text: 'Cancel',
                    'class': 'button',
                    click: function () {
                        $(this).dialog('close');
                    }
                }
            ]
        });
        return false;
    });

    $('input[name="signature_import_type_change"]').change(function () {
        window.location.href = baseUrl + '/DicomLogViewer/signatureList?type=' + this.value;
    });
</script>
