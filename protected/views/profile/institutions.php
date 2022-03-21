<?php
/**
 * OpenEyes.
 *
 * (C) Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see http://www.gnu.org/licenses/.
 *
 * @link http://www.openeyes.org.uk/
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2020 Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * @var $user User
 */
?>
<h2>Institutions you work at</h2>
<table class="standard">
    <thead>
        <tr>
            <th></th>
            <th>Name</th>
        </tr>
    </thead>
    <tbody>
    <?php
    foreach ($user->authentications as $i => $user_authentication) {
        $active = false;
        if ($user_authentication->institutionAuthentication->institution->id === Institution::model()->getCurrent()->id) {
            $active = true;
        }
        ?>
        <tr>
            <td><input type="checkbox"<?= !$active ? ' disabled' : '' ?> readonly name="institutions[<?= $i ?>]"<?= $active ? ' checked' : '' ?>/></td>
            <td><?= $user_authentication->institutionAuthentication->institution->name ?></td>
        </tr>
    <?php }?>
    </tbody>
</table>
