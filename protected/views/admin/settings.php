<?php

/**
 * (C) OpenEyes Foundation, 2018
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

<div class="cols-7">
    <div class="alert-box info">
        <b>Info</b> Settings added here will be overridden by any settings in local config files. eg common or
        core.php
    </div>

    <table class="standard">
        <thead>
            <tr>
                <th>Setting</th>
                <th>Value</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach (SettingMetadata::model()->byDisplayOrder()->findAll('element_type_id is null') as $metadata) {
                // Setting pulled from database
                $metadata_value = (string)$metadata->getSettingName($metadata->key, ['SettingInstallation']);

                // Check to see if the param is being set in a config file
                if (array_key_exists($metadata->key, OEConfig::getMergedConfig('main')['params'])) {
                    $param_value = OEConfig::getMergedConfig('main')['params'][$metadata->key];
                }

                // If it isn't set, use the database value
                if (isset($param_value)) {
                    // Transform the param if need be
                    if (is_array($param_value)) {
                        // If it's an array, implode it to display as a string
                        $param_value = implode(",", $param_value);
                    } elseif ($data = @unserialize($metadata->data)) {
                        // If it's an option for a serialised array get the value
                        if (gettype($param_value) != "boolean" && array_key_exists($param_value, $data)) {
                            $param_value = $data[$param_value];
                        } elseif ($param_value === 1 || $param_value === true) {
                            $param_value = $data['on'];
                        } elseif ($param_value === 0 || $param_value === false) {
                            $param_value = $data['off'];
                        }
                    }
                    ?>
                    <tr class="disabled">
                        <td><span class="fade"><?php echo $metadata->name ?></span></td>
                        <td><span class="fade"><?= $param_value ?> </span></td>
                        <td><i class="oe-i info small js-has-tooltip" data-tooltip-content="This parameter is being overridden by a config file and cannot be modified."></i></td>
                    </tr>

                    <?php
                } else {
                    ?>
                    <tr class="clickable" data-uri="admin/editInstallationSetting?key=<?= $metadata->key; ?>">
                        <td><?php echo $metadata->name ?></td>
                        <td><?= $metadata_value; ?></td>
                        <td></td>
                    </tr>
                    <?php
                }

                unset($param_value, $metadata_value);
            } ?>
        </tbody>
    </table>
</div>