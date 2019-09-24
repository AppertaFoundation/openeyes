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

/** @var Medication $medication */
?>
<h3>Alternative Terms</h3>
<table class="standard" id="medication_alternative_terms_tbl">
    <thead>
    <tr>
        <th width="cols-11">Term</th>
        <th width="cols-1">Action</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($medication->medicationSearchIndexes as $row_key => $medicationSearchIndex) : ?>
        <?php
        $id = is_null($medicationSearchIndex->id) ? -1 : $medicationSearchIndex->id;
        ?>
        <tr data-key="<?=$row_key?>">
            <td>
                <?php if ($id != -1) { ?>
                <input type="hidden" name="MedicationSearchIndex[<?=$row_key?>][id]" value="<?=$id?>" />
                <?php } ?>
                <?php echo CHtml::textField("MedicationSearchIndex[".$row_key."][alternative_term]", $medicationSearchIndex->alternative_term, array('class' => 'cols-full')); ?>
            </td>
            <td>
                <a href="javascript:void(0);" class="js-delete-alt-term"><i class="oe-i trash"></i></a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot class="pagination-container">
    <tr>
        <td colspan="3">
            <div class="flex-layout flex-right">
                <button class="button hint green js-add-alt-term" type="button"><i class="oe-i plus pro-theme"></i></button>
            </div>
        </td>
    </tr>
    </tfoot>
</table>

<script id="alt_terms_row_template" type="x-tmpl-mustache">
    <tr data-key="{{ key }}">
        <td>
            <?php echo CHtml::textField('MedicationSearchIndex[{{key}}][alternative_term]', null, array('class' => 'cols-full')); ?>
        </td>
        <td>
            <a href="javascript:void(0);" class="js-delete-alt-term"><i class="oe-i trash"></i></a>
        </td>
    </tr>
</script>
<script>
    $(function(){
        let $table = $('#medication_alternative_terms_tbl');
        $table.on("click", ".js-add-alt-term", function (e) {
            let key = OpenEyes.Util.getNextDataKey('#medication_alternative_terms_tbl tbody tr', 'key');
            let template = $('#alt_terms_row_template').html();
            let rendered = Mustache.render(template, {"key": key});
            $table.find('tbody').append(rendered);
        });

        $table.on("click", ".js-delete-alt-term", function (e) {
            $(e.target).closest("tr").remove();
        });
    });
</script>
