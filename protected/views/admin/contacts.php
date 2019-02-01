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

<div class="cols-9">

    <?php if (!$contacts) : ?>
        <div class="row divider">
            <div class="alert-box issue"><b>No results found</b></div>
        </div>
    <?php endif; ?>

    <div class="row divider">
        <form id="admin_contacts_search">
            <table class="cols-full">
                <colgroup>
                    <col class="cols-6">
                    <col class="cols-3" span="2">
                </colgroup>
                <tbody>
                <tr class="col-gap">
                    <td>
                        <?=\CHtml::textField(
                            'q',
                            (isset($_GET['q']) ? $_GET['q'] : ''),
                            ['class' => 'cols-full', 'placeholder' => "Name"]
                        ); ?>
                    </td>
                    <td>
                        <?=\CHtml::dropDownList(
                            'label',
                            isset($_GET['label']) ? $_GET['label'] :'',
                            CHtml::listData(
                                ContactLabel::model()->active()->findAll(
                                    ['order' => 'name']
                                ),
                                'id',
                                'name'
                            ),
                            ['empty' => '- Any label -']
                        ) ?>
                    </td>
                    <td>
                        <button class="blue hint" name="search"
                            type="submit" id="et_search">Search</button>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
    <table class="standard">
        <thead>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>First name</th>
            <th>Last name</th>
            <th>Qualifications</th>
            <th>Label</th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($contacts['contacts'] as $i => $contact) {?>
            <tr class="clickable" data-id="<?php echo $contact->id?>"
                data-uri="admin/editContact?contact_id=<?php echo $contact->id?>">
                <td><?php echo $contact->id?></td>
                <td><?php echo $contact->title?></td>
                <td><?php echo $contact->first_name?></td>
                <td><?php echo $contact->last_name?></td>
                <td><?php echo $contact->qualifications?></td>
                <td><?php echo $contact->label ?
                        $contact->label->name :
                        'None'?></td>
            </tr>
        <?php }?>
        </tbody>
        <tfoot class="pagination-container">
        <tr>
            <td colspan="2">
                <?=\CHtml::submitButton(
                    'Add',
                    [
                        'class' => 'button large',
                        'id' => 'et_add'
                    ]
                );?>
            </td>
            <td colspan="4">
                <?php $this->widget(
                    'LinkPager',
                    ['pages' => $contacts['pagination']]
                ); ?>
            </td>
        </tr>
        </tfoot>
    </table>
</div>