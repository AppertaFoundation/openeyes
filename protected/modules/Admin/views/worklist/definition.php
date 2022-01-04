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

<div class="admin box">
    <h2>Worklist Definition</h2>
    <?php echo EventAction::link('Definitions List', '/Admin/worklist/definitions/', array('level' => 'secondary'), array('class' => 'button small'))->toHtml()?>
    <br/><br/>
    Name: <?= $definition->name ?><br />
    Frequency: <i><?= $definition->rruleHumanReadable ?></i><br />
    Time Slot: <?=$definition->start_time?> - <?=$definition->end_time?><br />
    Patient Identifier Type: <?=$definition->patient_identifier_type->getTitleWithInstitution() ?>

    <hr style="margin: 5px;" />
    <h3>Current Maps</h3>
    <?php if (!count($definition->mappings)) {?>
        <i>None set</i>
    <?php } else {?>
        <table>
            <tr>
                <th>Key</th>
                <th>Values</th>
            </tr>
        <?php foreach ($definition->mappings as $mapping) {?>
            <tr>
                <td><?=$mapping->key?></td>
                <td><?=$mapping->valueList?></td>
            </tr>
        <?php }?>
        </table>
    <?php } ?>
    <hr style="margin: 5px;" />
    <h3>Display Contexts</h3>
    <?php $this->renderPartial('definition_display_contexts_table', array('definition' => $definition)); ?>
    <hr style="margin: 5px;" />
    <h3>Generated Instances</h3>
    <?php $this->renderPartial('definition_worklists_table', array('definition' => $definition)) ?>
</div>