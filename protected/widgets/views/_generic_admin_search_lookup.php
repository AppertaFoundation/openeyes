<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<?php
$ishidden = false;
$collapse_style = '';
$expand_style = '';
if (!$row->isNewRecord) {
    $ishidden = true;
    $collapse_style = 'display: none;';
} else {
    $expand_style = 'display: none;';
}
$search_field = $params['model']::model()->getAutocompleteField();
?>
<span id="<?= "display_{$params['field']}_{$i}"?>"><?= ($row && $row->{$params['relation']}) ? $row->{$params['relation']}->$search_field : '' ?></span>
<span id="<?= "expand_{$params['field']}_{$i}"?>" style="<?= $expand_style ?>">[e]</span>
<span id="<?= "collapse_{$params['field']}_{$i}"?>" style="<?= $collapse_style ?>">[x]</span>
<input type="hidden" name="<?= "{$params['field']}[$i]" ?>" id="<?="{$params['field']}_{$i}"?>" value="<?= $row->{$params['field']} ?>"/>

<div <?php if ($ishidden) {
    echo 'style="display:none"';
     } ?>>
    <?php
        $this->widget(
            'application.widgets.AutoCompleteSearch',
            ['field_name' => "autocomplete_{$params['field']}[{$i}]"]
        );
        ?>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('#<?= "expand_{$params['field']}_{$i}"?>').on('click', function() {
            $('#<?= "autocomplete_{$params['field']}_{$i}" ?>').show();
            $(this).hide();
            $('#<?= "collapse_{$params['field']}_{$i}"?>').show();
        });
        $('#<?= "collapse_{$params['field']}_{$i}"?>').on('click', function() {
            $('#<?= "autocomplete_{$params['field']}_{$i}" ?>').hide();
            $(this).hide();
            $('#<?= "expand_{$params['field']}_{$i}"?>').show();
        });

        OpenEyes.UI.AutoCompleteSearch.init({
            input: $(`[id="autocomplete_${'<?=$params['field'][$i]?>'}"]`),
            url: '/autocomplete/search',
            params: {
                'model': function () {return "<?= $params['model'] ?>"},
                'field': function () {return "<?= $search_field ?>"}
            },
            maxHeight: '200px',
            onSelect: function() {
                let response = OpenEyes.UI.AutoCompleteSearch.getResponse();
                let input = OpenEyes.UI.AutoCompleteSearch.getInput();

                $('#<?= "{$params['field']}[{$i}]" ?>').val(response.id);
                $('#<?= "display_{$params['field']}[{$i}]" ?>').text(response.label);
                $('#<?= "autocomplete_{$params['field']}[{$i}]" ?>').val('');
            }
        });
    });
</script>