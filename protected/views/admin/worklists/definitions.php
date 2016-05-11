<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

?>

<div class="admin box">
    <h2>Automatic Worklist Definitions</h2>
    <?php echo EventAction::link('Add Definition', '/worklistAdmin/definition/', array(), array('class' => 'button primary small'))->toHtml()?>
    <?php if ($definitions) {?>
        <table class="generic-admin grid">
            <thead>
            <tr>
                <th>Order</th>
                <th>Name</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody class="sortable">
            <?php foreach ($definitions as $i => $definition) {?>
                <tr class="clickable">
                    <td class="reorder">&uarr;&darr;<input type="hidden" name="<?php echo get_class($definition) ?>[display_order][]" value="<?php echo $i ?>"></td>
                    <td><?=$definition->name?></td>
                    <td>Edit</td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    <?php } else {?>
        <div class="alert-box info">No automatic worklists have been defined. You may add one by clicking the button above ...</div>
    <?php } ?>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('.sortable').sortable();
    });
</script>