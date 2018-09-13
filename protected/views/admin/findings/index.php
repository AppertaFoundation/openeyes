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

<!--    --><?php //echo $this->renderPartial('_form_errors', array('errors' => $errors)) ?>
    <?php
    $form = $this->beginWidget(
        'BaseEventTypeCActiveForm',
        [
            'id' => 'adminform',
            'enableAjaxValidation' => false,
            'focus' => '#username',
            'layoutColumns' => array(
                'label' => 2,
                'field' => 4,
            ),
        ]
    ) ?>

<form id="findings" method="POST">
    <input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken ?>"/>
        <div class="cols-7">
        <table class="standard cols-full" id="finding-table">
            <colgroup>
                <col class="cols-1">
                <col class="cols-4">
                <col class="cols-4">
                <col class="cols-1">
                <col class="cols-1">
            </colgroup>

            <thead>
            <tr>
                <th>Order</th>
                <th>Name</th>
                <th>Subspecialties</th>
                <th>Requires Description</th>
                <th>Active</th>
            </tr>
            </thead>

            <tbody class="sortable">
            <?php foreach ($findings as $key => $finding) : ?>
                <tr id="<?=$key?>">
                    <td class="reorder">&uarr;&darr;
                        <!--<input type="hidden" name="finding_ids[<?/*=$key;*/?>]" value="<?php /*echo $finding->id */?>">-->
                        <?=CHtml::activeHiddenField($finding, "[$key]display_order", ['class' => "js-display-order"]);?>
<!--                        --><?//=CHtml::activeHiddenField($finding, "[$key]id");?>
                    </td>
                    <td>
                        <?php echo CHtml::activeTextField(
                            $finding,
                            "[$key]name",
                            [
                                'class' => 'cols-full',
                                'autocomplete' => Yii::app()->params['html_autocomplete']
                            ]
                        ); ?>
                    </td>
                    <?php
                        $this->widget('application.widgets.MultiSelectDropDownList', [
                            'options' => [
                                'label' => 'Subspecialty:',
                                'dropDown' => [
                                    'name' => null,
                                    'id' => 'subspecialties',
                                    'data' => \CHtml::listData($subspecialty, 'id', 'name'),
                                    'htmlOptions' => ['empty' => 'All Subspecialties'],
                                    'selectedItemsInputName' => "subspecialty-ids[$key][]",
                                    'selectedItems' => \Yii::app()->request->getpost('subspecialties', null)
                                ],],
                            'template' => "<td class='js-multiselect-dropdown-wrapper'>{DropDown}<div class='list-filters js-multiselect-dropdown-list-wrapper'>{List}</div></td>"
                        ]);
                    ?>
                    <td>
                        <?php echo CHtml::activeCheckBox(
                            $finding,
                            "[$key]requires_description"
                        ) ?>
                    </td>
                    <td>
                        <?php echo CHtml::activeCheckBox(
                            $finding,
                            "[$key]active"
                        ) ?>
                    </td>
                </tr>
            <?php endforeach;?>
            </tbody>

            <tfoot>
            <tr>
                <td colspan="6">
                    <?php echo CHtml::htmlButton(
                        'Add',
                        [
                            'class' => 'small secondary',
                            'name' => 'add',
                            'type' => 'submit',
                            'id' => 'et_admin-add'
                        ]
                    );?>
                    <?php echo CHtml::button(
                        'Save',
                        [
                            'class' => 'generic-admin-save small primary button header-tab',
                            'name' => 'save',
                            'type' => 'submit',
                            'id' => 'et_admin-save'
                        ]
                    ); ?>
                </td>
            </tr>
            </tfoot>
        </table>
        </div>
</form>



<?php $this->endWidget() ?>

<script type="text/javascript">
    $(document).ready(function() {
        $('.sortable').sortable({
            stop: function(e, ui) {
                $('#finding-table tbody tr').each(function(index, tr) {
                    $(tr).find('.js-display-order').val(index);
                });
            }
        });
    });

    $('#et_sort').on('click', function() {
        $('#definition-list').attr('action', $(this).data('uri')).submit();
    })

    $('#et_admin-add').on('click', function() {
        $('#definition-list').attr('action', $(this).data('uri')).submit();
    })
</script>
