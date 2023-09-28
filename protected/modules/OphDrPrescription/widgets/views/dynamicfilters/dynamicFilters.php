<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2023, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<?php $parameters = $this->getSearchParameters();?>

<h4><?=$this->title?></h4>
<p id="dynamic-criteria-initial" <?= $parameters ? 'style="display: none;"' : null ?>>Select criteria for search...</p>
<table id="dynamic-param-list" class="standard normal-text last-right" data-test="dynamic-param-list">
    <tbody>
    <?php
    foreach ($parameters as $parameter) {
        $this->render('dynamicfilters/_tr', $parameter);
    }
    ?>
    </tbody>
</table>
<div class="flex-layout flex-right row">
    <button
            id="add-to-advanced-search-filters"
            class="button hint green js-add-select-btn openeyes-ui-adderdialog-open-btn"
            data-popup="add-to-search-queries"
            data-test="add-advanced-search-filter"
            type="button"
    >
        Add criteria
    </button>
</div>
<script type="text/template" id="dynamic-advanced-signatory-filter">
    <?=$this->render('dynamicfilters/_tr', [
        'main_label' => '{{label}}',
        'key' => '{{key}}',
        'parameter_name' => '{{parameter_name}}',
        'type_value' => '{{type_value}}',
        'value_label' => '{{value_label}}',
        'value_value' => '{{value_value}}',
        'operation_label' => '{{operation_label}}',
        'operation_value' => '{{operation_value}}',
    ]);?>
</script>
<script>

    const dynamic_filter = new DynamicFilters({
        prefix: "<?=$this->prefix;?>",
        adder_dialog_item_set: [
            <?php foreach ($this->adder_itemset as $item_set_group) :?>
            new OpenEyes.UI.AdderDialog.ItemSet(
                <?= CJSON::encode($item_set_group['itemset']) ?>,
                <?= CJSON::encode($item_set_group['options'] ?? []) ?>
            ),
            <?php endforeach?>
        ]
    });
</script>

