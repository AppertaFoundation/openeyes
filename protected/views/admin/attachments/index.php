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

<?php
$form = $this->beginWidget(
    'BaseEventTypeCActiveForm',
    [
        'id' => 'adminform',
        'enableAjaxValidation' => false,
        'layoutColumns' => array(
            'label' => 2,
            'field' => 5,
        ),
    ]
) ?>

    <div class="cols-5">
        <form id="admin_institutions">
            <table class="standard">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Show attachments</th>
                </tr>
                </thead>

                <tbody>
                <?php
                foreach ($event_types as $i => $event_type) { ?>
                    <tr data-id="<?php echo $event_type->id ?>">
                        <td><?php echo $event_type->id ?></td>
                        <td><?php echo $event_type->name ?></td>
                        <td><?= \CHtml::activeCheckBox($event_type, "[" . $event_type->id . "]show_attachments") ?></td>
                    </tr>
                <?php } ?>
                </tbody>

                <tfoot>
                <tr>
                    <td colspan="5">
                        <?= \OEHtml::submitButton('Save', ['type' => 'submit']) ?>
                    </td>
                </tr>
                </tfoot>
            </table>
        </form>
    </div>

<?php $this->endWidget() ?>