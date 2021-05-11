<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2013
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

<div class='<?=$div_wrapper_class?>'>
<?php if (!$get_row) {
    if ($filter_fields) { ?>
        <form method="get">
            <?php foreach ($filter_fields as $filter_field) { ?>
                <div class="data-group">
                    <div class="cols-2 column"><label for="<?= $filter_field['field'] ?>"><?= CHtml::encode($model::model()->getAttributeLabel($filter_field['field'])); ?></label></div>
                    <div class="cols-5 column end"><?=
                        CHtml::dropDownList(
                            $filter_field['field'],
                            $filter_field['value'],
                            $filter_field['choices'] ?? SelectionHelper::listData($filter_field['model']),
                            array('empty' => '-- Select --', 'class' => 'generic-admin-filter')
                                                   );
                                                    ?></div>
                </div>
            <?php } ?>
        </form>
    <?php }
    if ($filters_ready) { ?>
        <?= CHtml::beginForm() ?>

        <table class="standard generic-admin <?= ($display_order) ? 'sortable' : ''?> ">
            <thead>
                <tr>
                    <?php if ($display_order) { ?>
                        <th>Order</th>
                        <th><?= CHtml::checkBox("select-all")?></th>
                        <?php
                    }
                    if (!$label_extra_field) : ?>
                        <th><?= $model::model()->getAttributeLabel($label_field) ?></th>
                    <?php endif;?>
                    <?php foreach ($extra_fields as $field) {?>
                        <th>
                            <?=\CHtml::hiddenField('_extra_fields[]', $field['field'])?>
                            <?php echo $model::model()->getAttributeLabel($field['field'])?>
                        </th>
                    <?php }?>
                    <?php if ($model::model()->hasAttribute('active')) {?>
                        <th>Active</th>
                    <?php } else {?>
                        <th>Actions</th>
                    <?php }
                    if ($model::model()->hasAttribute('default')) {?>
                        <th>Default</th>
                    <?php }
                    if ($is_mapping) {
                        foreach ($model::model()->enumerateSupportedLevels() as $level) {?>
                        <th>
                            Assigned to current <?= $model::model()->getModelSuffixForLevel($level) ?>
                        </th>
                        <?php }
                    } ?>
                </tr>
            </thead>
            <tbody>
    <?php }
}
?>

<?php foreach ($items as $i => $row) {
    $this->render('_generic_admin_row', array('i' => $i, 'row' => $row, 'label_field' => $label_field, 'extra_fields' => $extra_fields, 'model' => $model, 'display_order' => $display_order, 'label_extra_field' => $label_extra_field, 'input_class' => $input_class));
}

if (!$get_row && $filters_ready) {
    if (!$this->new_row_url) {
        $this->render('_generic_admin_row', array('row_class' => 'newRow', 'row_style' => 'display: none;', 'disabled' => true,
                            'i' => '{{key}}', 'row' => new $model(), 'label_field' => $label_field, 'extra_fields' => $extra_fields, 'model' => $model, 'display_order' => $display_order, 'input_class' => $input_class));
    } ?>
            </tbody>
            <?php if ($model::model()->hasAttribute('default')) {?>
                <tr>
                    <td class="generic-admin-no-default">
                        No default
                    </td>
                    <td>
                        <?=\CHtml::radioButton('default', !$has_default, array('value' => 'NONE'))?>
                    </td>
                </tr>
            <?php } ?>
                <tfoot class="pagination-container">

                <tr>
                    <td colspan="10">
                        <?php if (!$this->cannot_add) {
                            echo CHtml::submitButton('Add', ['name' => 'admin-add', 'id' => 'et_admin-add', 'class' => 'generic-admin-add button large', 'data-model' => $model, 'data-new-row-url' => @$this->new_row_url]);
                        }?>&nbsp;
                        <?php if (!$this->cannot_save) {
                            echo \CHtml::submitButton('Save', ['name' => 'admin-save', 'id' => 'et_admin-save', 'class' => 'generic-admin-save button large']);
                        }?>&nbsp;
                        <?php if ($is_mapping) {
                            echo \CHtml::hiddenField('return_url', $this->return_url);
                            echo \CHtml::hiddenField('model', $this->model);
                            $supported_levels = $model::model()->enumerateSupportedLevels();
                            $structured_levels = array();
                            foreach ($supported_levels as $level) {
                                $level_prefix = $model::model()->getModelSuffixForLevel($level);
                                $structured_levels[] = ['value' => $level, 'label' => ucfirst($level_prefix)];
                            }

                            echo \CHtml::submitButton(
                                'Add selected to current '.$level_prefix,
                                [
                                    'name' => 'admin-map-add',
                                    'id' => 'et_admin-map-add',
                                    'class' => 'generic-admin-save button large',
                                    'formaction' => '/admin/addMapping',
                                ]);
                            echo \CHtml::submitButton(
                                'Remove selected from current '.$level_prefix,
                                [
                                    'name' => 'admin-map-remove',
                                    'id' => 'et_admin-map-remove',
                                    'class' => 'generic-admin-save button large',
                                    'formaction' => '/admin/removeMapping',
                                ]);

                            if (count($supported_levels) > 1) {
                                echo CHtml::dropDownList('mapping_level', '', CHtml::listData($structured_levels, 'value', 'label'));
                            } else {
                                echo CHtml::hiddenField('mapping_level', $supported_levels[0]);
                            }
                        }?>
                    </td>
                </tr>
                </tfoot>

        </table>
        <div>
        </div>
    <?= CHtml::endForm() ?>
<?php } ?>
    <script>
        $( document ).ready(function(){
            $('#mapping_level').change(
                function(val) {
                    let displayString = $('#mapping_level option:selected').text();
                    $('#et_admin-map-add').val("Add selected to current " + displayString);
                    $('#et_admin-map-remove').val("Remove selected from current " + displayString);
                });
        });
    </script>
</div>