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
/**
 * This file is developed to test DICOM File selector page for File Watcher feature of IOLMaster
 * and removed it once IOL Master  implimentation get completed and stable on live.
 */
?>
<div class="admin box">
    <h2>DICOM File Watcher</h2>
    <form id="dicom_file_watcher">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken ?>"/>
        <table class="grid">
            <thead>
            <tr>
                <th>Select DICOM File</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if (!empty($msg)) {
                if ($msg == 1) {
                    echo '<div id="flash-formula" class="alert-box with-icon info">File Copied Successfully!</div>';
                } else {
                    echo '<div id="flash-formula" class="alert-box with-icon warning">' . $msg . '</div>';
                }
            }
            ?>
            <tr>
                <td>
                    <select name="dicomfiles">
                        <?php
                        foreach ($dirlist as $file) {
                            //if($file['ext'] == "dcm") {
                                echo "<option value=" . $file['name'] . ">" . $file['name'] . "</option>";
                           // }
                        }
                        ?>
                    </select>
                </td>
            </tr>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="9">
                    <?php echo EventAction::button('Submit', 'move_dicom_file', null, array('class' => 'small'))->toHtml() ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </form>
</div>