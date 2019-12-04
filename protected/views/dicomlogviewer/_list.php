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
<div class="cols-12 column">
  <h2>Results:</h2>
</div>

<div class="cols-12 column">
        <div class="box generic">
            <?php
            if (empty($data['items'])) {?>
                <div class="alert-box">
                    No Dicom logs match the search criteria.
                </div>
                <?php
            } else {?>
                <div class="pagination"></div>
                <table class="standard audit-logs">
                    <thead>
                        <tr>
                            <th>File Name</th>
                            <th>Import Date</th>
                            <th>Study date</th>
                            <th>Station ID</th>
                            <th>Location</th>
                            <th>Type</th>
                            <th>Patient Number</th>
                            <th>Status</th>
                            <th>Study Instance ID</th>
                            <th>Comment</th>
                            <th><i>More</i></th>
                        </tr>
                    </thead>
                    <tbody id="auditListData">
                        <?php foreach ($data['files_data'] as $i => $log) {
                            $this->renderPartial('//dicomlogviewer/_list_row', array('i' => $i, 'log' => $log));
                        }?>
                    </tbody>
                </table>
                <div class="pagination last"></div>
            <?php }?>
        </div>
    </div>
