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

<div class="cols-5">
    <form id="admin_drugs">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken ?>"/>
        <table class="standard">
            <thead>
            <tr>
                <th><input type="checkbox" name="selectall" id="selectall"/></th>
                <th>Name</th>
                <th>Active</th>
            </tr>
            </thead>

            <tbody>
            <?php foreach (OphTrOperationnote_PostopDrug::model()->findAll(array('order' => 'active DESC, name')) as $i => $drug) { ?>
                <tr class="clickable" data-id="<?php echo $drug->id ?>"
                    data-uri="OphTrOperationnote/admin/editPostOpDrug/<?php echo $drug->id ?>">
                    <td><input type="checkbox" name="drugs[]" value="<?php echo $drug->id ?>"/></td>
                    <td><?php echo ($drug->active) ? ($drug->name) : ('<s>' . $drug->name . '</s>'); ?></td>
                    <td><?php echo ($drug->active) ? ('<i class="oe-i tick small"></i>') : ('<i class="oe-i remove small"></i>'); ?></td>
                </tr>
            <?php } ?>
            </tbody>

            <tfoot class="pagination-container">
            <tr>
                <td colspan="3">
                    <?=\CHtml::button(
                        'Add',
                        [
                            'class' => 'button large',
                            'name' => 'add',
                            'type' => 'submit',
                            'data-uri' => '/OphTrOperationnote/admin/addPostOpDrug',
                            'id' => 'et_add'
                        ]
                    ); ?>
                    <?=\CHtml::button(
                        'Delete',
                        [
                            'data-uri' => '/OphTrOperationnote/admin/deletePostOpDrugs',
                            'data-object' => 'drug',
                            'class' => 'button large',
                            'type' => 'submit',
                            'name' => 'delete',
                            'id' => 'et_delete',
                        ]
                    ); ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </form>
</div>
