<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>



<?php if (!$lensType_lens) : ?>
    <div class="row divider">
        <div class="alert-box issue"><b>No results found</b></div>
    </div>
<?php endif; ?>

<div class="row divider cols-9">
    <form id="procedures_search" method="post">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken ?>"/>
        <table class="cols-full">
            <colgroup>
                <col class="cols-10">
                <col class="cols-1" span="2">
                <col class="cols-1">
            </colgroup>
            <tbody>
            <tr class="col-gap">
                <td>
                    <?= \CHtml::textField(
                        'search[query]',
                        $search['query'],
                        [
                            'class' => 'cols-full',
                            'placeholder' => "ID, Name, Display name, Description, A constant"
                        ]
                    ); ?>
                </td>
                <td>
                    <?= \CHtml::dropDownList(
                        'search[active]',
                        $search['active'],
                        [
                            1 => 'Only Active',
                            0 => 'Exclude Active',
                        ],
                        ['empty' => 'All']
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


<div class="cols-9">
    <form id="admin_lensTypes" method="post">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken ?>"/>

        <table class="standard">
            <thead>
            <tr>
                <th><input type="checkbox" name="selectall" id="selectall"/></th>
                <th>ID</th>
                <th>Name</th>
                <th>Display Name</th>
                <th>Description</th>
                <th>Position</th>
                <th>Comments</th>
                <th>A constant</th>
                <th>SF/pACD</th>
                <th>a0</th>
                <th>a1</th>
                <th>a2</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($lensType_lens as $key => $lens) { ?>
                <tr id="$key" class="clickable" data-id="<?= $lens->id ?>"
                    data-uri="OphInBiometry/lensTypeAdmin/edit/<?= $lens->id ?>?returnUri=">
                    <td>
                        <input type="checkbox" name="select[]" value="<?= $lens->id ?>"
                               id="select[<?= $lens->id ?>]"/>
                    </td>
                    <td><?= $lens->id ?></td>
                    <td><?= $lens->name ?></td>
                    <td><?= $lens->display_name ?></td>
                    <td><?= $lens->description ?></td>
                    <td><?= $lens->position->name ?? '' ?></td>
                    <td><?= $lens->comments ?></td>
                    <td><?= $lens->acon ?></td>
                    <td><?= $lens->sf ? $lens->sf : $lens->pACD ?></td>
                    <td><?= $lens->a0 ?></td>
                    <td><?= $lens->a1 ?></td>
                    <td><?= $lens->a2 ?></td>
                    <td>
                        <?= ($lens->active) ?
                            ('<i class="oe-i tick small"></i>') :
                            ('<i class="oe-i remove small"></i>'); ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
            <tfoot class="pagination-container">
            <tr>
                <td colspan="4">
                    <?= \CHtml::submitButton(
                        'Add',
                        [
                            'class' => 'button large',
                            'data-uri' => '/OphInBiometry/lensTypeAdmin/edit',
                            'name' => 'add',
                            'id' => 'et_add'
                        ]
                    ); ?>
                    <!-- Does not delete the lens type: sets it as INACTIVE -->
                    <?= \CHtml::submitButton(
                        'Deactivate Lens Type',
                        [
                            'class' => 'button large',
                            'data-uri' => '/OphInBiometry/lensTypeAdmin/delete',
                            'name' => 'delete',
                            'data-object' => 'lensTypes',
                            'id' => 'et_delete'
                        ]
                    ); ?>
                </td>
                <td colspan="3">
                    <?php $this->widget(
                        'LinkPager',
                        ['pages' => $pagination]
                    ); ?>
                </td>
            </tr>
            </tfoot>
        </table>
</div>

<?php
if (Yii::app()->params['opnote_lens_migration_link'] == 'on') {
    ?>
    <div class="admin box">
        <a href="/OphInBiometry/MergeLensData">Merge operation note cataract element lens data</a>
    </div>
    <?php
}
?>
