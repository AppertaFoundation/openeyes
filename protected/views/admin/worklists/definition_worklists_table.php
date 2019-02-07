<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
 ?>

<?php if ($definition->worklists) { ?>
    <table class="generic-admin standard">
        <thead>
        <tr>
            <th>Date</th>
            <th>Name</th>
            <th>Number of Patients</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($definition->worklists as $i => $worklist) { ?>
            <tr>
                <td><?= $worklist->displayDate ?></td>
                <td><?= $worklist->name ?></td>
                <td><?= count($worklist->worklist_patients) ?></td>
                <td><a href="/worklistAdmin/worklistPatients/<?= $worklist->id ?>">View</a></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
<?php } else {?>
    <div class="alert-box info">No instances have been generated for this Worklist Definition.</div>
<?php } ?>
