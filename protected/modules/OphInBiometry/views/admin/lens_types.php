<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<div class="admin box">
    <h2>Lens types</h2>
    <form id="admin_lens_types">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken?>" />
        <table class="standard">
            <thead>
                <tr>
                    <th><input type="checkbox" name="selectall" id="selectall" /></th>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Position</th>
                    <th>Description</th>
                    <th>A constant</th>
                    <th>SF/pACD</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($lens_types as $i => $lens_type) {?>
                    <tr class="clickable" data-id="<?php echo $lens_type->id?>" data-uri="OphInBiometry/admin/editLensType/<?php echo $lens_type->id?>">
                        <td><input type="checkbox" name="lens_types[]" value="<?php echo $lens_type->id?>" /></td>
                        <td><?php echo $lens_type->id?></td>
                        <td><?php echo $lens_type->name?></td>
                        <td><?php echo $lens_type->position->name?></td>
                        <td><?php echo $lens_type->description?></td>
                        <td><?php echo $lens_type->acon?></td>
                        <td><?php echo $lens_type->sf ? $lens_type->sf : $lens_type->pACD?></td>
                    </tr>
                <?php }?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="9">
                        <?php echo EventAction::button('Add', 'add_lens_type', null, array('class' => 'small'))->toHtml()?>
                        <?php echo EventAction::button('Delete', 'delete_lens_type', null, array('class' => 'small'))->toHtml()?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </form>
</div>
