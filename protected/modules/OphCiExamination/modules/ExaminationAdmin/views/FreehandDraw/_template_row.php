<?php
/**
* OpenEyes
*
* (C) OpenEyes Foundation, 2021
* This file is part of OpenEyes.
* OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
* OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
* You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
*
* @package OpenEyes
* @link http://www.openeyes.org.uk
* @author OpenEyes <info@openeyes.org.uk>
* @copyright Copyright (c) 2021, OpenEyes Foundation
* @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
*/
?>

<?php foreach ($templates ?? [] as $i => $template) : ?>
    <tr class="clickable"
        data-id="<?php echo $template->id?>"
        data-uri="OphCiExamination/admin/FreehandDraw/edit/<?=$template->id?>"
    >
        <td><input type="checkbox" name="delete_templates[]" value="<?=$template->id?>"></td>
        <td class="reorder">
            <span>↑↓</span>
            <input type="hidden" name="DrawingTemplate[display_order][]" value="<?= $template->id ?>">

            <?=\CHtml::activeHiddenField($template, "[$i]id");?>
        <td><?php echo $template->name?></td>
        <td><?php echo $template->display_order?></td>
        <td><?= OEHtml::icon($template->active ? 'tick' : 'remove', ['class' => 'small']) ?></td>
    </tr>

<?php endforeach; ?>

