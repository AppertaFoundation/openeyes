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

<div id="generic-admin-list" class="admin box">
    <?php if ($definitions) {?>
    <form id="definition-list" method="POST">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken ?>"/>
        <table class="standard" id="et_sort" data-uri="/Admin/worklist/sortDefinitions">
            <thead>
            <tr>
                <th>
                    <?= CHtml::link('Add Definition', '/Admin/worklist/definitionUpdate/', [
                        'class' => 'button large green hint',
                    ]) ?>
                </th>
            </tr>
            <tr>
                <th>Order</th>
                <th>Name</th>
                <th>Patient Identifier Type</th>
                <th>Default Pathway Type</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody class="sortable">
            <?php foreach ($definitions as $i => $definition) { ?>
                <?php $worklist_count = $definition->worklistCount; ?>
                <tr<?= ($worklist_count ? " data-test='has-worklists'" : "") ?>>
                    <td class="reorder">
                        &uarr;&darr;
                        <input type="hidden" name="WorklistDefinition[display_order][]" value="<?= $definition->id ?>">
                    </td>
                    <td data-test="definition-name"><?= $definition->name ?></td>
                    <td><?= $definition->patient_identifier_type->getTitleWithInstitution() ?></td>
                    <td><?= $definition->pathway_type->name ?></td>
                    <td><?php if ($this->manager->canUpdateWorklistDefinition($definition)) {?>
                        <a class="button small" href="/Admin/worklist/definitionUpdate/<?=$definition->id?>">Edit</a><?php
                        }?>
                        <a class="button small" href="/Admin/worklist/definition/<?=$definition->id?>">View</a>
                        <a class="button small" href="/Admin/worklist/definitionWorklists/<?=$definition->id?>">Instances (<?= $worklist_count ?>)</a>
                        <a class="button small" href="/Admin/worklist/definitionMappings/<?=$definition->id?>">Mapping Items(<?= $definition->mappingCount ?>)</a>
                        <a class="button small" href="/Admin/worklist/definitionDisplayContexts/<?= $definition->id ?>">Display Context (<?= $definition->displayContextCount > 0 ? 'limited' : 'any'; ?>)</a>
                        <?php if ($worklist_count) { ?>
                            <a class="button small" href="/Admin/worklist/definitionWorklistsDelete/<?= $definition->id ?>">Delete Instances</a>
                        <?php } else { ?>
                            <a class="button small" href="/Admin/worklist/definitionGenerate/<?= $definition->id ?>">Generate</a>
                            <a class="button small" href="/Admin/worklist/definitionDelete/<?= $definition->id ?>">Delete</a>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </form>
    <?php } else {?>
        <?= EventAction::link('Add Definition', '/Admin/worklist/definitionUpdate/', [], ['class' => 'button primary small'])->toHtml()?>
        <div class="alert-box info">No automatic worklists have been defined. You may add one by clicking the button above ...</div>
    <?php } ?>
</div>
