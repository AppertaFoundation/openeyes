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

<div class="admin box cols-6">
    <h2>Mapping Items for <?= $definition->name ?></h2>
    <?php echo EventAction::link('Definitions List', '/Admin/worklist/definitions/', array('level' => 'secondary'), array('class' => 'button small'))->toHtml()?>
    <?php echo EventAction::link('View Definition', '/Admin/worklist/definition/'.$definition->id, array('level' => 'secondary'), array('class' => 'button small'))->toHtml()?>
    <?php if ($this->manager->canUpdateWorklistDefinition($definition)) {
        echo EventAction::link('Add Mapping', '/Admin/worklist/addDefinitionMapping/'.$definition->id, array('level' => 'primary'), array('class' => 'button small'))->toHtml();
    }?>
    <?php if ($definition->mappings) { ?>
    <form id="mapping-list" method="POST">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken ?>"/>
        <?php if ($definition->displayed_mappings) {?>
        <h3>Displayed Mapping Items</h3>
        <table class="table standard">
            <thead>
            <tr>
                <th>Order</th>
                <th>Name</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody class="sortable">
            <?php foreach ($definition->displayed_mappings as $i => $mapping) { ?>
                <tr>
                    <td class="reorder">&uarr;&darr;<input type="hidden" name="item_ids[]"
                                                           value="<?php echo $mapping->id ?>"></td>
                    <td><?= $mapping->key ?></td>
                    <td><?php if ($this->manager->canUpdateWorklistDefinition($definition)) {?>
                        <a href="/Admin/worklist/definitionMappingUpdate/<?= $mapping->id ?>">Edit</a> |
                        <a href="/Admin/worklist/definitionMappingDelete/<?= $mapping->id ?>">Delete</a>
                        <?php } else {?>
                            <span title="Cannot change mappings for un-editable definition">
                            Edit | Delete
                            </span>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="4">
                    <?php echo EventAction::button(
                        'Sort',
                        'sort',
                        array(),
                        array(
                            'class' => 'small',
                            'style' => 'display:none;',
                            'data-uri' => '/Admin/worklist/definitionMappingSort/'.$definition->id,
                            'data-object' => 'WorklistDefinitionMapping',
                        )
                    )->toHtml() ?>
                </td>
            </tr>
            </tfoot>
        </table>
            <?php
        }
        if ($definition->hidden_mappings) {?>
            <h2>Hidden Mapping Items</h2>
            <table class="generic-admin standard">
                <thead>
                <tr>
                    <th>Order</th>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($definition->hidden_mappings as $i => $mapping) {?>
                    <tr>
                        <td class="reorder">-</td>
                        <td><?=$mapping->key?></td>
                        <td><?php if ($this->manager->canUpdateWorklistDefinition($definition)) {?>
                            <a href="/Admin/worklist/definitionMappingUpdate/<?=$mapping->id?>">Edit</a> |
                            <a href="/Admin/worklist/definitionMappingDelete/<?=$mapping->id?>" disabled="disabled">Delete</a></td>
                            <?php } else {?>
                            <span title="Cannot change mappings for un-editable definition">
                            Edit | Delete
                            </span>
                            <?php } ?>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        <?php }?>
        </form>
    <?php } else { ?>
        <?php if ($this->manager->canUpdateWorklistDefinition($definition)) { ?>
            <div class="alert-box info">
                No mapping items have been defined for this Worklist Definition.
                You may add one by clicking the button above ...
            </div>
        <?php } else { ?>
            <div class="alert-box info">
                No mapping items have been defined for this Worklist Definition.
                Your system does not allow definitions to be edited after generating instances.
                If you want to add some mappings please delete instances first.
            </div>
        <?php }
    } ?>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('.sortable').sortable({
            change: function (e, ui) {
                $('#et_sort').show();
            }
        });

        $('#et_sort').on('click', function() {
            $('#mapping-list').attr('action', $(this).data('uri')).submit();
        })
    });
</script>