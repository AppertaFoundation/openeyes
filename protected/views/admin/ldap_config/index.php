<?php
/**
 * (C) OpenEyes Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2020, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<div class="cols-7">
    <form id="admin_ldap_config">
        <table class="standard">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Description</th>
                    <th>LDAP Configuration</th>
                </tr>
            </thead>

            <tbody>
                <?php
                foreach ($ldap_configs as $ldap_config) { ?>
                    <tr class="clickable" data-id="<?= $ldap_config->id ?>"
                        data-uri="admin/editldapconfig?ldap_config_id=<?= $ldap_config->id ?>">
                        <td><?php echo $ldap_config->id ?></td>
                        <td><?php echo $ldap_config->description ?></td>
                        <td><?php echo $ldap_config->ldap_json_obscured ?></td>
                    </tr>
                <?php } ?>
            </tbody>

            <tfoot class="pagination-container">
            <tr>
                <td colspan="3">
                    <?= \CHtml::button(
                        'Add',
                        [
                            'class' => 'button large',
                            'id' => 'et_add',
                            'data-uri' => "/admin/editldapconfig",
                        ]
                    ); ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </form>
</div>

