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

<div class="box admin">
  <div class="cols-10 column">
    <h2>Manage Manual Worklists</h2>
      <?php echo EventAction::link('Add Worklist', '/worklist/manualAdd/', array(), array('class' => 'button primary small'))->toHtml()?>

      <?php if ($current_worklists || $available_worklists) { ?>
            <div id="draggablelist">
                <?= CHtml::beginForm(array('/worklist/manualUpdateDisplayOrder'), 'post'); ?>
                <?= CHtml::hiddenField('item_ids'); ?>
                <div id="draggablelist-items" class="data-group">
                    <div class="cols-6 column">
                        <h2>Current Worklists</h2>
                        <?php $this->renderPartial('manual/_worklists_table', array('id' => 'draggablelist-items-enabled', 'items' => $current_worklists)); ?>
                        <div class="right">
                            <button class="small" type="submit">Save</button>
                            <button id="draggablelist-cancel" class="small warning" type="button">Cancel</button>
                        </div>
                    </div>
                    <div class="cols-6 column">
                        <h2>Available Worklists</h2>
                        <?php $this->renderPartial('manual/_worklists_table', array('id' => 'draggablelist-items-available', 'items' => $available_worklists)); ?>
                    </div>
                </div>
                <?= CHtml::endForm(); ?>
            </div>
            <?php
            } else {?>
                <div class="alert-box info">You currently have no access to any manual worklists. You may add one by clicking the button above ...</div>
            <?php } ?>
        </div>
</div>
<script type="text/javascript">
    // TODO: move this into a self contained library
    $(document).ready(function() {
        var showHideEmpty = function (el, min) {
            if (el.find('.draggablelist-item').length > min) {
                el.find('.draggablelist-empty').hide();
            } else {

                el.find('.draggablelist-empty').show();
            }
        };

        var items_enabled = $('#draggablelist-items-enabled');

        var items_available = $('#draggablelist-items-available');

        var extractItemIds = function () {
            $('#draggablelist #item_ids').val(  // remove -items
                items_enabled.find('.draggablelist-item').map(
                    function () {
                        return $(this).data('item-id');
                    }
                ).get().join(',')
            );
        };

        showHideEmpty(items_enabled, 0);
        showHideEmpty(items_available, 0);
        extractItemIds();

        var options = {
            containment: '#draggablelist-items',
            items: '.draggablelist-item',
            change: function (e, ui) {
                showHideEmpty($(this), 0);
                if (ui.sender) showHideEmpty(ui.sender, 1);
            }
        };

        items_enabled.sortable($.extend({connectWith: items_available}, options));
        items_available.sortable($.extend({connectWith: items_enabled}, options));

        $('#draggablelist form').submit(extractItemIds);
        $('#draggablelist-cancel').click(function () {
            location.reload();
        });
    });
</script>