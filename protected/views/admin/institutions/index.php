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
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<div class="cols-7">
    <div class="row divider">
        <form>
            <table class="cols-full">
                <colgroup>
                    <col class="cols-6">
                    <col class="cols-3">
                </colgroup>

                <tbody>
                <tr class="col-gap">
                    <td>
                        <input class="cols-full" autocomplete="off"
                               placeholder="Name, ID, Pas Code, First name, Last name, Subspeciality Name" type="text"
                               value="" name="search[name][value]" id="search_name_value">
                    </td>
                    <td>
                        <select name="search[active]" id="search_active">
                            <option value="" selected="selected">All</option>
                            <option value="1">Only Active</option>
                            <option value="0">Exclude Active</option>
                        </select>
                    </td>
                    <td>
                        <button class="blue hint" name="save" formmethod="get" type="submit">Search</button>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>

    <form id="admin_institutions">
        <table class="standard">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Remote ID</th>
                <th>Short name</th>
                <th>Primary Logo</th>
                <th>Secondary Logo</th>
            </tr>
            </thead>

            <tbody>
            <?php
            foreach ($institutions as $i => $institution) { ?>
                <tr class="clickable" data-id="<?php echo $institution->id ?>"
                    data-uri="admin/editinstitution?institution_id=<?php echo $institution->id ?>">
                    <td><?php echo $institution->id ?></td>
                    <td><?php echo $institution->name ?></td>
                    <td><?php echo $institution->remote_id ?></td>
                    <td><?php echo $institution->short_name ?></td>
                    <td>
                            <?php
                            if (($institution->logo) && ($institution->logo->primary_logo)) {
                                echo 'Custom';
                            } else {
                                echo 'Default';
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            if (($institution->logo) && ($institution->logo->secondary_logo)) {
                                echo 'Custom';
                            } else {
                                echo 'Default';
                            }
                            ?>
                        </td>
                </tr>
            <?php } ?>
            </tbody>

            <tfoot class="pagination-container">
            <tr>
                <td colspan="3">
                    <?= \CHtml::button(
                        'Add',
                        [
                            'class' => 'button large',
                            'name' => 'add',
                            'id' => 'et_add'
                        ]
                    ); ?>
                </td>
                <td colspan="3">
                    <?php $this->widget('LinkPager', ['pages' => $pagination]); ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </form>
</div>