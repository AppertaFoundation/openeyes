<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2020, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<style>
    ul.add-options.js-search-results{
        max-height: 100px;
        overflow-y: auto;
    }
    .js-search-results li:hover{
        background-color: white;
        color: #141e2b;
        cursor: pointer;
    }
</style>
<?php $is_admin = Yii::app()->user->checkAccess('admin'); ?>
<div class="cols-8">

    <div class="row divider">
        <h2><?= $laser_procedure->id ? 'Edit' : 'Add' ?> laser procedure</h2>
    </div>

    <?php $form = $this->beginWidget('BaseEventTypeCActiveForm', [
        'id' => 'procedure-form',
        'enableAjaxValidation' => false,
        'layoutColumns' => [
            'label' => 6,
            'field' => 6,
        ],
    ]) ?>

    <table>
        <colgroup>
            <col class="cols-8">
            <col class="cols-8" >
        </colgroup>
        <tbody>
        <?= CHtml::activeHiddenField($laser_procedure, 'id') ?>
        <tr>
            <td>Laser Procedure</td>
            <td>
                <?= CHtml::activeTextField($laser_procedure->procedure ?? Procedure::model(),
                    'term',
                    ['class' => 'procedures-search-autocomplete cols-full',
                        'disabled' => !$is_admin]) ?>
                <input type="hidden" name="Procedure[id]" value="<?=$laser_procedure->id?>">
                <input type="hidden" name="Procedure[proc_id]" value="<?=$laser_procedure->procedure_id?>">
                <input type="hidden" name="Procedure[mode]" value="original">
                <ul id="Procedure_term_list" class="add-options js-search-results"></ul>
            </td>
        </tr>
        <tr>
            <td>Institutions</td>
            <td>
                <?php if ($is_admin) {
                    echo $form->multiSelectList(
                        $laser_procedure,
                        'OphTrLaser_LaserProcedure[institutions]',
                        'institutions',
                        'id',
                        Institution::model()->getList(false),
                        null,
                        ['class' => 'cols-full', 'empty' => '-- Add --', 'nowrapper' => true]);
                } elseif (!$laser_procedure->id) {
                    echo Institution::model()->getCurrent()->name;
                } else {
                    $institutions = CHtml::listData($laser_procedure->institutions, 'id', 'name');
                    echo $institutions ? implode("<br>", $institutions) : 'All Institutions';
                } ?>
            </td>
        </tr>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="2">
                <?php if ($is_admin || !$laser_procedure->id) {
                    echo \CHtml::submitButton(
                        'Save',
                        [
                            'class' => 'button large',
                            'name' => 'save',
                            'id' => 'et_save'
                        ]
                    );
                }
                if ($laser_procedure->id) {
                    echo CHtml::submitButton(
                        'Delete',
                        [
                            'class' => 'button large',
                            'name' => 'delete_laser_procedure',
                            'id' => 'et_delete_laser_procedure',
                            'formaction' => '/OphTrLaser/admin/deleteLaserProcedure/' . $laser_procedure->id,
                        ]
                    );
                }
                echo \CHtml::submitButton(
                    'Cancel',
                    [
                        'data-uri' => '/OphTrLaser/admin/manageLaserProcedures',
                        'class' => 'button large',
                        'name' => 'cancel',
                        'id' => 'et_cancel'
                    ]
                ); ?>
            </td>
        </tr>
        </tfoot>
    </table>

    <?php $this->endWidget() ?>

</div>

<script>
    $(document).ready(function () {
        for (let i = 0; i < all_procs.length; i++) {
            all_procs[i]['index'] = i;
        }

        function closeSearchResults() {
            let proc_term = $('#' + this.dataset.target);
            let input = proc_term.siblings('input.procedures-search-autocomplete');
            const term = laser_procs[input.data('index')] ? laser_procs[input.data('index')]['term'] :'';
            proc_term.html('');
            proc_term.siblings('input.procedures-search-autocomplete').val(term);
        }

        function selectProc(e){
            e.stopPropagation();
            let proc_term = $('#' + this.dataset.target);
            document.getElementById(this.dataset.target).value = this.innerText;
            proc_term.siblings('input[name$="[proc_id]"]').val(this.dataset.id);
            let proc = laser_procs[proc_term.data('index')];
            proc_term.siblings('input[name$="[mode]"]').val('edit');

            if(laser_procs[proc_term.data('index')]){
                // edit existing procedure list
                proc_term.siblings('input[name$="[mode]"]').val('edit')
                proc['index'] = this.dataset.itemIndex;
                all_procs[this.dataset.itemIndex]['id'] = proc['id'];
                laser_procs.splice(proc_term.data('index'), 1, all_procs[this.dataset.itemIndex])
                all_procs.splice(this.dataset.itemIndex, 1, proc);
            } else {
                // add new procedure to the list
                laser_procs.push(all_procs[this.dataset.itemIndex]);
                all_procs.splice(this.dataset.itemIndex, 1);
            }
            $(this).parent().html('');
        }

        function autoComplete(e) {
            let input_id = $(this).attr('id');
            let input_val = $(this).val().trim();
            let ul_id = input_id + '_list';

            if (e.key.length !== 1) {
                if (e.key.toLowerCase() !== 'backspace') {
                    return;
                }
            }

            $('ul.add-options.js-search-results').html('');
            if (!$(this).val().trim() || $(this).val().trim().length < 3) {
                return;
            }

            const result = all_procs.filter(function (item) {
                return item.term.toLowerCase().includes(input_val.toLowerCase())
            });
            if (result.length) {
                let close = document.createElement('div');
                let icon = document.createElement('i');
                icon.classList.add(...['oe-i', 'remove-circle', 'small']);
                close.appendChild(icon);
                close.classList.add(...['close-icon-btn', 'close-search-result']);
                close.style.float = 'right';
                close.style.cursor = 'pointer';
                close.dataset.target = ul_id;
                $(close).off('click').on('click', closeSearchResults);
                $('#' + ul_id).append(close);
                for (let i in result) {
                    let li = document.createElement('li');
                    li.innerText = result[i]['term'];
                    li.dataset.id = result[i]['procedure_id'];
                    li.dataset.itemIndex = result[i]['index'];
                    li.dataset.target = input_id;
                    li.style.padding = '5px';
                    li.class = ul_id + '_item';
                    $(li).off('click').on('click', selectProc)
                    $('#' + ul_id).append(li);
                }
            } else {
                // Procedure not found
                let div = document.createElement('div');
                let listed = laser_procs.filter(function(proc){
                    return proc['term'].toLowerCase().includes(input_val.toLowerCase())
                });
                const msg_box_style = listed.length > 0 ? ['alert-box', 'alert'] : ['alert-box', 'warning'];
                const msg = listed.length > 0 ? 'Possible match(es) found from the listed procedures: ' + listed.map(item => item.term).join(', ') : 'No procedure matched';
                div.classList.add(...msg_box_style)
                div.innerText = msg;
                $('#' + ul_id).append(div);
            }

        }

        $(document).off('keyup').on('keyup', 'input.procedures-search-autocomplete', autoComplete);

    });

</script>