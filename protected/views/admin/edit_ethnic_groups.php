<?php
$groups_with_children_ids = array_reduce($groups, static function ($ids, $group) {
    if ($group->id_assignment) {
        $ids[$group->id_assignment] = $group->id_assignment;
    }

    return $ids;
}, []);


echo $this->renderPartial('//admin/_form_errors', array('errors' => $errors));
?>

<div>
    <?php $form = $this->beginWidget('CActiveForm'); ?>
    <table class="standard sortable">
        <colgroup>
            <col class="cols-1" />
            <col class="cols-3" />
            <col class="cols-1" />
            <col class="cols-3" />
            <col class="cols-1" />
            <col class="cols-1" />
        </colgroup>
        <thead>
            <th>Order</th>
            <th>Name</th>
            <th>Code</th>
            <th>Parent</th>
            <th>Describe needs</th>
            <th></th>
        </thead>
        <tbody class='js-group-rows'>
        <?php foreach ($groups as $index => $group) { ?>
            <tr>
                <td>
                    &uarr;&darr;
                    <?= $form->hiddenField($group, '[' . $index . ']id') ?>
                    <?= $form->hiddenField($group, '[' . $index . ']display_order') ?>
                </td>
                <td><?= $form->textField($group, '[' . $index . ']name', ['class' => 'cols-full']) ?></td>
                <td><?= $form->textField($group, '[' . $index . ']code', ['maxlength' => 2]) ?></td>
                <td><?= $form->dropDownList($group, '[' . $index . ']id_assignment', \CHtml::listData($parent_groups, 'id', 'name'), ['empty' => '- None -']) ?></td>
                <td><?= $form->checkBox($group, '[' . $index . ']describe_needs') ?></td>
                <td>
                    <button class="button js-delete-group<?= in_array($group->id, $groups_with_children_ids) ? ' disabled' : '' ?>">Delete</button>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
    <button class="button js-add-group">Add</button>
    <input class="button" type="submit" value="Save" />
    <?php $this->endWidget(); ?>
</div>
<script>
$(document).ready(function() {
    function setDisplayOrders(rows) {
        $(rows).each(function(index, tr) {
            index++;
            $(tr).find("[name$='display_order]']").val(index);
        });

        $('.js-delete-group').off('click').on('click', function(e) {
            e.preventDefault();

            $(this).parents('tr').remove();
        });
    }

    $('.sortable tbody').sortable({
        stop: function(e, ui) {
            setDisplayOrders('.sortable tbody tr');
        }
    });

    let insertIndex = <?= count($groups) ?>;

    $('.js-add-group').on('click', function(e) {
        e.preventDefault();

        const rows = $('.js-group-rows');

        const template = $('#js-ethnic-group-template').text();
        const element = Mustache.render(template, { index: insertIndex });

        rows.append(element);
        insertIndex = insertIndex + 1;

        setDisplayOrders(rows.children());
    });

    setDisplayOrders($('.js-group-rows'));
});
</script>
<script type="text/template" id="js-ethnic-group-template">
<tr>
    <td>
         &uarr;&darr;
        <input type="hidden" name="EthnicGroup[{{index}}][id]" value="" />
        <input type="hidden" name="EthnicGroup[{{index}}][display_order]" value="" />
    </td>
    <td><input class="cols-full" name="EthnicGroup[{{index}}][name]" /></td>
    <td><input maxlength="2" name="EthnicGroup[{{index}}][code]" /></td>
    <td>
        <select>
            <option value="">- None -</option>
        <?php foreach ($parent_groups as $parent) { ?>
            <option value="<?= $parent->id ?>"><?= $parent->name ?></option>
        <?php } ?>
        </select>
    </td>
    <td>
        <input name="EthnicGroup[{{index}}][describe_needs]" value="0" type="hidden" />
        <input name="EthnicGroup[{{index}}][describe_needs]" value="1" type="checkbox" />
    </td>
    <td>
        <button class="button js-delete-group<?= in_array($group->id, $groups_with_children_ids) ? ' disabled' : '' ?>">Delete</button>
    </td>
</tr>
</script>
