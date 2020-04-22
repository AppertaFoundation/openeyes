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

<div class="row divider">
    <h2>Steps</h2>
</div>

<form id="admin_workflow_steps">
    <table class="standard">
        <thead>
        <tr>
            <th>Position</th>
            <th>Step</th>
            <th colspan="2">Actions</th>
        </tr>
        </thead>

        <tbody class="sortable">
        <?php
        foreach ($model->steps as $i => $step) {
            ?>
            <tr class="selectable" data-id="<?php echo $step->id ?>">
                <td><?php echo $step->position ?></td>
                <td><?php echo $step->name ?></td>
                <td>
                    <?php if ($step->isDeletable()) : ?>
                        <a href="#" class="removeElementSet" rel="<?php echo $step->id ?>">Remove</a>
                    <?php else : ?>
                        <i class="js-has-tooltip oe-i info small pad right"
                           data-tooltip-content="Step is in use and cannot be deleted."></i>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="#" class="js-elementSetActiveStatus" data-stepid="<?php echo $step->id ?>"><?= ($step->is_active ? "Disable" : "Enable"); ?></a>
                </td>
            </tr>
            <?php
        } ?>
        </tbody>

        <tfoot class="pagination-container">
        <tr>
            <td colspan="5">
                <?php echo EventAction::button('Add step', 'add_step', null, array('class' => 'small'))->toHtml() ?>
            </td>
        </tr>
        </tfoot>
    </table>

    <div class="box_admin" id="step_element_types">
    </div>
</form>
