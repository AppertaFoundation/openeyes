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

<?php $this->renderPartial('//base/_messages'); ?>
<div class="cols-8">
    <form id="admin_manage_lasers">
        <table class="standard">
            <thead>
            <tr>
                <th>Name</th>
                <th>Type</th>
                <th>Wavelength</th>
                <th>Institution</th>
                <th>Site</th>
                <th>Active</th>
                <th>Edit</th>
            </tr>
            </thead>
            <tbody>
            <?php if (isset($model_list)) {
                foreach ($model_list as $i => $model) { ?>
                <tr class="clickable" data-id="<?php echo $model->id ?>"
                    data-uri="OphTrLaser/admin/editLaser/<?php echo $model->id ?>">
                    <td><?php echo $model->name ?></td>
                    <td><?php echo $model->type->name ?></td>
                    <td><?php echo $model->wavelength ?></td>
                    <td><?php echo $model->institution ? $model->institution->name : 'N/A' ?></td>
                    <td><?php echo $model->site->name ?></td>
                    <td><i class="oe-i <?=($model->active ? 'tick' : 'remove');?> small"></i></td>
                    <td>
                            <?=\CHtml::link(
                                'Edit',
                                '/OphTrLaser/admin/editLaser/' . $model->id,
                                ['class' => 'small event-action']
                            ) ?>
                    </td>
                </tr>
                <?php } } ?>
            </tbody>
            <tfoot class="pagination-container">
            <tr>
                <td colspan="6">
                    <?=\CHtml::button(
                        'Add',
                        [
                            'class' => 'button large',
                            'type' => 'button',
                            'name' => 'add',
                            'data-uri' => '/OphTrLaser/admin/addLaser',
                            'id' => 'et_add'
                        ]
                    ); ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </form>
</div>