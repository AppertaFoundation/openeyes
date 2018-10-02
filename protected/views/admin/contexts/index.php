<?php
/**
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<div class="cols-9">
    <div class="row divider">
        <form id="context-search-form" action="#" method="post">
            <table class="standard">
                <colgroup>
                    <col class="cols-4">
                    <col class="cols-1">
                    <col class="cols-7">

                </colgroup>
                <tr>
                    <td><?= CHtml::textField('search[query]', $search['query'], [
                            'placeholder' => 'Search Id, PAS Code, Name (case sensitive)',
                            'class' => 'cols-full',
                        ]); ?>
                    </td>
                    <td>
                        <select name="search[active]" id="search_active">
                            <option value="" selected="selected">All</option>
                            <option value="1">Only Active</option>
                            <option value="0">Exclude Active</option>
                        </select>
                    </td>
                    <td>
                        <input type="hidden" name="YII_CSRF_TOKEN"
                               value="<?php echo Yii::app()->request->csrfToken ?>"/>
                        <button class="blue hint" id="search-button" formmethod="post" type="submit">Search</button>
                    </td>
                </tr>
            </table>
        </form>
    </div>

    <form id="admin_firms">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken ?>"/>
        <table class="standard cols-full">
            <thead>
            <th><input type="checkbox" name="selectall" id="selectall"/></th>
            <th>Id</th>
            <th>PAS Code</th>
            <th>Name</th>
            <th>Subspecialty</th>
            <th>Consultant</th>
            <th>Service Enabled</th>
            <th>Context Enabled</th>
            <th>Active</th>
            </thead>
            <tbody>
            <?php foreach ($firms as $firm) : ?>
                <tr class="clickable" data-id="<?php echo $firm->id ?>"
                    data-uri="admin/editFirm/<?php echo $firm->id ?>">
                    <td><input type="checkbox" name="firms[]" value="<?php echo $firm->id ?>"/></td>
                    <td><?php echo $firm->id ?></td>
                    <td><?php echo $firm->pas_code ?></td>
                    <td><?php echo $firm->name ?></td>
                    <td><?php echo ($firm->serviceSubspecialtyAssignment) ?
                            $firm->serviceSubspecialtyAssignment->subspecialty->name : 'None' ?></td>
                    <td><?php echo ($firm->consultant) ? $firm->consultant->fullName : 'None' ?></td>

                    <td><?php echo ($firm->can_own_an_episode) ?
                            ('<i class="oe-i tick small"></i>') :
                            ('<i class="oe-i remove small"></i>'); ?></td>
                    <td><?php echo ($firm->runtime_selectable) ?
                            ('<i class="oe-i tick small"></i>') :
                            ('<i class="oe-i remove small"></i>'); ?></td>
                    <td><?php echo ($firm->active) ?
                            ('<i class="oe-i tick small"></i>') :
                            ('<i class="oe-i remove small"></i>'); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot class="pagination-container">
            <tr>
                <td colspan="5">
                    <?= \CHtml::button(
                        'Add',
                        [
                            'data-uri' => '/admin/addFirm',
                            'class' => 'button large',
                            'name' => 'add',
                            'id' => 'et_add']
                    ); ?>
                </td>
                <td colspan="4">
                    <?php $this->widget('LinkPager', ['pages' => $pagination]); ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </form>
</div>