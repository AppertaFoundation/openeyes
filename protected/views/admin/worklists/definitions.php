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
    <?php if ($definitions) {?>
    <form id="definition-list" method="POST">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken ?>"/>
        <table class="standard">
            <thead>
            <tr>
                <th>Order</th>
                <th>Name</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody class="sortable">
            <?php foreach ($definitions as $i => $definition) {?>
                <tr>
                    <td class="reorder">&uarr;&darr;<input type="hidden" name="item_ids[]" value="<?php echo $definition->id ?>"></td>
                    <td><?=$definition->name?></td>
                    <td><?php if ($this->manager->canUpdateWorklistDefinition($definition)) {?><a href="/worklistAdmin/definitionUpdate/<?=$definition->id?>">Edit</a> |<?php }?>
                        <a href="/worklistAdmin/definition/<?=$definition->id?>">View</a> |
                        <a href="/worklistAdmin/definitionWorklists/<?=$definition->id?>">Instances (<?=$definition->worklistCount?>)</a> |
                        <a href="/worklistAdmin/definitionMappings/<?=$definition->id?>">Mapping Items(<?=$definition->mappingCount?>)</a> |
                        <a href="/worklistAdmin/definitionDisplayContexts/<?=$definition->id?>">Display Context (<?=$definition->displayContextCount > 0 ? 'limited' : 'any';?>)</a> |
                        <?php if ($definition->worklistCount) {?>
                            <a href="/worklistAdmin/definitionWorklistsDelete/<?=$definition->id?>">Delete Instances</a>
                        <?php } else { ?>
                            <a href="/worklistAdmin/definitionGenerate/<?=$definition->id?>">Generate</a> |
                            <a href="/worklistAdmin/definitionDelete/<?=$definition->id?>">Delete</a>
                        <?php }?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="4">
                    <?php echo EventAction::link(
                        'Add Definition',
                        '/worklistAdmin/definitionUpdate/',
                        array(),
                        array('class' => 'button large')
                    )->toHtml()?>
                    <?php echo EventAction::button(
                        'Sort',
                        'sort',
                        array(),
                        array(
                            'class' => 'button large',
                            'style' => 'display:none;',
                            'data-uri' => '/worklistAdmin/definitionSort/',
                            'data-object' => 'WorklistDefinition',
                        )
                    )->toHtml() ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </form>
    <?php } else {?>
        <div class="alert-box info">No automatic worklists have been defined. You may add one by clicking the button above ...</div>
    <?php } ?>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('.sortable').sortable({
            change: function (e, ui) {
                $('#et_sort').show();
            }
        });
    });

    $('#et_sort').on('click', function() {
        $('#definition-list').attr('action', $(this).data('uri')).submit();
    })
</script>