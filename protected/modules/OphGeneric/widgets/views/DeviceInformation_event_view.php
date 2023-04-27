<?php
/**
 * OpenEyes.
 *
 * (C) Copyright Apperta Foundation, 2020
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
<div class="flex-layout flex-top col-gap">
    <div class="cols-6">
        <table>
            <tr>
                <td class="data-label">
                    <?= $element->getAttributeLabel('manufacturer'); ?>
                </td>
                <td>
                    <div class="element-data">
                        <div class="data-value"><?= $element->manufacturer ?></div>
                    </div>
                </td>
            </tr>

            <tr>
                <td class="data-label">
                    <?= $element->getAttributeLabel('manufacturer_model_name'); ?>
                </td>
                <td>
                    <div class="element-data">
                        <div class="data-value"><?= $element->manufacturer_model_name ?></div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="data-label">
                    <?= $element->getAttributeLabel('series_description'); ?>
                </td>
                <td>
                    <div class="element-data">
                        <div class="data-value"><?= $element->series_description ?></div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="data-label">
                    <?= $element->getAttributeLabel('laterality'); ?>
                </td>
                <td>
                    <div class="element-data">
                        <div class="data-value"><?= $element->laterality ?></div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="data-label">
                    <?= $element->getAttributeLabel('image_laterality'); ?>
                </td>
                <td>
                    <div class="element-data">
                        <div class="data-value"><?= $element->image_laterality ?></div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="data-label">
                    <?= $element->getAttributeLabel('study_description'); ?>
                </td>
                <td>
                    <div class="element-data">
                        <div class="data-value"><?= $element->study_description ?></div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="data-label">
                    <?= $element->getAttributeLabel('document_title'); ?>
                </td>
                <td>
                    <div class="element-data">
                        <div class="data-value"><?= $element->document_title ?></div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="cols-6">
        <table>
            <tr>
                <td class="data-label">
                    <?= $element->getAttributeLabel('acquisition_date_time'); ?>
                </td>
                <td>
                    <div class="element-data">
                        <div class="data-value"><?= $element->acquisition_date_time ?></div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="data-label">
                    <?= $element->getAttributeLabel('study_date'); ?>
                </td>
                <td>
                    <div class="element-data">
                        <div class="data-value"><?= $element->study_date ?></div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="data-label">
                    <?= $element->getAttributeLabel('study_time'); ?>
                </td>
                <td>
                    <div class="element-data">
                        <div class="data-value"><?= $element->study_time ?></div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="data-label">
                    <?= $element->getAttributeLabel('content_date'); ?>
                </td>
                <td>
                    <div class="element-data">
                        <div class="data-value">
                            <?= $element->content_date ?></div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="data-label">
                    <?= $element->getAttributeLabel('content_time'); ?>
                </td>
                <td>
                    <div class="element-data">
                        <div class="data-value"><?= $element->content_time ?></div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="data-label">
                    <?= $element->getAttributeLabel('station_name'); ?>
                </td>
                <td>
                    <div class="element-data">
                        <div class="data-value"><?= $element->station_name ?></div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="data-label">
                    <?= $element->getAttributeLabel('operators_name'); ?>
                </td>
                <td>
                    <div class="element-data">
                        <div class="data-value"><?= $element->operators_name ?></div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

</div>
<div>
    <div class="flex-layout">
        <div id="js-listview-additional-info-pro" style="display: block;">
        </div>
        <div id="js-listview-additional-info-full" class="listview-full" style="display: none;">
            <table>
                <tr>
                    <td class="data-label">
                        <?= $element->getAttributeLabel('last_request_id'); ?>
                    </td>
                    <td>
                        <div class="element-data">
                            <div class="data-value"><?= $element->last_request_id ?></div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="data-label">
                        <?= $element->getAttributeLabel('software_version'); ?>
                    </td>
                    <td>
                        <div class="element-data">
                            <div class="data-value"><?= $element->software_version ?></div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="data-label">
                        <?= $element->getAttributeLabel('model_version'); ?>
                    </td>
                    <td>
                        <div class="element-data">
                            <div class="data-value"><?= $element->model_version ?></div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="data-label">
                        <?= $element->getAttributeLabel('study_instance_uid'); ?>
                    </td>
                    <td>
                        <div class="element-data">
                            <div class="data-value"><?= $element->study_instance_uid ?></div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="data-label">
                        <?= $element->getAttributeLabel('series_instance_uid'); ?>
                    </td>
                    <td>
                        <div class="element-data">
                            <div class="data-value">
                                <?= $element->series_instance_uid ?></div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="data-label">
                        <?= $element->getAttributeLabel('study_id'); ?>
                    </td>
                    <td>
                        <div class="data-value"><?= $element->study_id ?></div>
                    </td>
                </tr>
                <tr>
                    <td class="data-label">
                        <?= $element->getAttributeLabel('series_number'); ?>
                    </td>
                    <td>
                        <div class="element-data">
                            <div class="data-value"><?= $element->series_number ?></div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="data-label">
                        <?= $element->getAttributeLabel('instance_number'); ?>
                    </td>
                    <td>
                        <div class="element-data">
                            <div class="data-value"><?= $element->instance_number ?></div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="data-label">
                        <?= $element->getAttributeLabel('modifying_system'); ?>
                    </td>
                    <td>
                        <div class="element-data">
                            <div class="data-value"><?= $element->modifying_system ?></div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="data-label">
                        <?= $element->getAttributeLabel('operator_identification_sequence'); ?>
                    </td>
                    <td>
                        <div class="element-data">
                            <div class="data-value"><?= $element->operator_identification_sequence ?></div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="data-label">
                        <?= $element->getAttributeLabel('sop_instance_uid'); ?>
                    </td>
                    <td>
                        <div class="element-data">
                            <div class="data-value"><?= $element->sop_instance_uid ?></div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <div>
            <i class="oe-i small js-listview-expand-btn expand" data-list="additional-info"></i>
        </div>
    </div>
</div>

