<?php
/**
 * OpenEyes.
 *
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

<?php if (!$investigations) : ?>
    <div class="row divider">
        <div class="alert-box issue"><b>No results found</b></div>
    </div>
<?php endif; ?>

<div class="row divider cols-full">
    <form id="investigations_search" method="post">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken ?>"/>
        <table class="cols-full">
            <colgroup>
                <col class="cols-10">
                <col class="cols-1">
                <col class="cols-1">
            </colgroup>
            <tbody>
            <tr class="col-gap">
                <td>
                    <?=\CHtml::textField(
                        'search[query]',
                        $search['query'],
                        [
                            'class' => 'cols-full',
                            'placeholder' => "Name, Snomed Code, Snomed Term, ECDS Code, Specialty"
                        ]
                    ); ?>
                </td>
                <td>
                    <button class="blue hint"
                            type="submit" id="et_search">Search
                    </button>
                </td>
            </tr>
            </tbody>
        </table>
    </form>
</div>


<form id="admin_investigations" method="post">
    <input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken ?>"/>

    <table class="standard">
        <thead>
        <tr>
            <th><input type="checkbox" name="selectall" id="selectall"/></th>
            <th>Name</th>
            <th>Snomed Code</th>
            <th>Snomed Term</th>
            <th>ECDS Code</th>
            <th>Specialty</th>
        </tr>
        </thead>
        <tbody>
        <?php

        foreach ($investigations as $key => $investigation) { ?>
            <tr id="$key" class="clickable" data-id="<?php echo $investigation->id ?>"
                data-uri="oeadmin/investigation/edit/<?php echo $investigation->id ?>?returnUri=">
                <td>
                    <?php if ($this->isInvestigationDeletable($investigation)) : ?>
                        <input type="checkbox" name="select[]" value="<?php echo $investigation->id ?>" id="select[<?=$investigation->id ?>]"/>
                    <?php endif; ?>
                </td>
                <td><?php echo $investigation->name ?></td>
                <td><?php echo $investigation->snomed_code ?></td>
                <td><?php echo $investigation->snomed_term ?></td>
                <td><?php echo $investigation->ecds_code ?></td>
                <?php
                if ($investigation->specialty_id !== null) {
                    $specialty_name = Specialty::model()->findByPk($investigation->specialty_id)->name ;
                    ?>
                    <td><?php echo $specialty_name?></td>
                <?php } ?>

            </tr>
        <?php } ?>
        </tbody>

        <tfoot class="pagination-container">
        <tr>
            <td colspan="4">
                <?=\CHtml::button(
                    'Add',
                    [
                        'class' => 'button large',
                        'data-uri' => '/oeadmin/investigation/edit',
                        'name' => 'add',
                        'id' => 'et_add',
                    ]
                ); ?>
                <?=\CHtml::submitButton(
                    'Delete',
                    [
                        'class' => 'button large disabled',
                        'data-uri' => '/oeadmin/investigation/delete',
                        'name' => 'delete',
                        'data-object' => 'investigations',
                        'id' => 'et_delete',
                        'disabled' => true,
                    ]
                ); ?>
            </td>
            <td colspan="9">
                <?php $this->widget(
                    'LinkPager',
                    ['pages' => $pagination]
                ); ?>
            </td>
        </tr>
        </tfoot>
    </table>
</form>

<script>
    $(document).ready(function () {

        /**
         * Deactivate button when no checkbox is selected.
         */
        $(this).on('change', $('input[type="checkbox"]'), function (e) {
            var checked_boxes = $('#admin_investigations').find('table.standard tbody input[type="checkbox"]:checked');

            if (checked_boxes.length <= 0) {
                $('#et_delete').attr('disabled', true).addClass('disabled');
            } else {
                $('#et_delete').attr('disabled', false).removeClass('disabled');
            }
        });
    });
</script>


