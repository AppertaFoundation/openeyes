<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>
<?php
$htmlOptions = array();
$collapse_style = "";
$expand_style = "";
if (!$row->isNewRecord) {
	$htmlOptions['style'] = "display: none;";
	$collapse_style = "display: none;";
}
else {
	$expand_style = "display: none;";
}
$search_field = $params['model']::model()->getAutocompleteField();
?>
<span id="<?= "display_{$params['field']}_{$i}"?>"><?= ($row && $row->{$params['relation']}) ? $row->{$params['relation']}->$search_field : '' ?></span>
<span id="<?= "expand_{$params['field']}_{$i}"?>" style="<?= $expand_style ?>">[e]</span>
<span id="<?= "collapse_{$params['field']}_{$i}"?>" style="<?= $collapse_style ?>">[x]</span>
<input type="hidden" name="<?= "{$params['field']}[$i]" ?>" id="<?="{$params['field']}_{$i}"?>" value="<?= $row->{$params['field']} ?>"/>
<?php
$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
		'name'=> "autocomplete_{$params['field']}[{$i}]",
		'id'=>"autocomplete_{$params['field']}_{$i}",
		'source'=>"js:function(request, response) {
						$.ajax({
							'url': '" . Yii::app()->createUrl('/autocomplete/search') . "',
							'type':'GET',
							'data':{
								'term': request.term,
								'model': '".$params['model']."',
								'field': '".$search_field."'
							},
							'success': function(data) {
								data = $.parseJSON(data);
								response(data);
							},
						});
					}",
		'options'=>array(
			'minLength'=>'2',
			'select'=>"js:function(event, ui) {
				$('#{$params['field']}_{$i}').val(ui.item.id);
				$('#display_{$params['field']}_{$i}').text(ui.item.label);
				$('#autocomplete_{$params['field']}_{$i}').val('');
				return false;
			}",
		),
		'htmlOptions'=>$htmlOptions
	)); ?>
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
	});
</script>