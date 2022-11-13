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

/**
 * @var $institution_id int
 */

?>

<div class="cols-full">
    <div class="alert-box info">
        <b>Info</b> Settings added here will be overridden by any settings in local config files. eg common or
        core.php
    </div>
    <div>
        <?php if ($is_admin) {
            echo 'Institution: ' . CHtml::dropDownList(
                'institution_id',
                $institution_id,
                CHtml::listData(Institution::model()->getTenanted(), 'id', 'name'),
                array('empty' => 'All institutions', 'id' => 'js-institution-setting-filter')
            );
        } elseif ($institution_id !== null) {
            echo 'Institution: ' . Institution::model()->findByPk($institution_id)->name;
        }?>
    </div>

    <table class="standard">
        <thead>
            <tr>
                <th width="35%">Setting</th>
                <th>Value</th>
                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $purifier = new CHtmlPurifier();
            $allowed_classes = $institution_id ? ['SettingInstitution', 'SettingInstallation'] : ['SettingInstallation'];
            foreach (SettingMetadata::model()->byDisplayOrder()->findAll('element_type_id is null') as $metadata) {
                // Setting pulled from database
                $metadata_value = $metadata->getSettingName($metadata->key, $allowed_classes, $institution_id, true);

                $base_data_uri = "admin/editSystemSetting?key=" . $metadata->key;
                if ($metadata->lowest_setting_level === 'INSTITUTION' && $institution_id) {
                    $uri_param = "&class=SettingInstitution&institution_id={$institution_id}";
                } else {
                    $uri_param = '&class=SettingInstallation';
                }
                $data_uri = $base_data_uri . $uri_param;

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
                        <td><?php if (!empty($metadata->description)) : ?>
                            <i class="oe-i status-query small js-has-tooltip" data-tooltip-content="<?= $metadata->description ?>"></i>
        
                            <?php endif ?>
                        </td>
                    </tr>

                <?php } elseif ($institution_id && !$is_admin && $metadata->lowest_setting_level === 'INSTALLATION') { ?>
                    <tr class="disabled">
                        <td><span class="fade"><?php echo $metadata->name ?></span></td>
                        <td><span class="fade"><?= $metadata_value ?> </span></td>
                        <td><i class="oe-i info small js-has-tooltip" data-tooltip-content="This parameter can only be modified by a system administrator."></i></td>
                        <td><?php if (!empty($metadata->description)) : ?>
                            <i class="oe-i status-query small js-has-tooltip" data-tooltip-content="<?= $metadata->description ?>"></i>
        
                            <?php endif ?>
                        </td>
                    </tr>
                <?php } elseif ($institution_id && $metadata->lowest_setting_level !== 'INSTALLATION') { ?>
                    <tr class="clickable" data-uri="<?= $data_uri ?>">
                        <td><?php echo $metadata->name ?></td>
                        <td><?= $metadata_value ?></td>
                        <td>
                        <?php if ($is_admin) { ?>
                            <i class="oe-i info small js-has-tooltip" data-tooltip-content="This parameter value is specific to the currently selected institution."></i>
                        <?php } ?>
                        </td>
                        <td><?php if (!empty($metadata->description)) : ?>
                            <i class="oe-i status-query small js-has-tooltip" data-tooltip-content="<?= $metadata->description ?>"></i>
        
                            <?php endif ?>
                        </td>
                    </tr>
                <?php } else { ?>
                    <tr class="clickable" data-uri="<?= $data_uri ?>">
                        <td><?php echo $metadata->name ?></td>
                        <td><?= $purifier->purify($metadata_value);?></td>
                        <td></td>
                        <td><?php if (!empty($metadata->description)) : ?>
                            <i class="oe-i status-query small js-has-tooltip" data-tooltip-content="<?= htmlspecialchars($metadata->description) ?>"></i>
        
                            <?php endif ?>
                        </td>
                    </tr>
                    <?php
                }

                unset($param_value, $metadata_value);
            } ?>
        </tbody>
    </table>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#js-institution-setting-filter').change(function(e) {
                window.location.href = 'settings?institution_id=' + e.target.value;
            });
        })
    </script>
</div>
