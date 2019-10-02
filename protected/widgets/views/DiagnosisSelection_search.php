<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
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
<div class="data-group diagnosis-selection<?php echo !$nowrapper ? ' flex-layout flex-left' : '';?> ">
	<?php if (!$nowrapper && $label) {?>
		<div class="cols-<?php echo $layoutColumns['label'];?> column">
			<label for="<?php echo "{$class}_{$field}";?>">
				<?php echo $element->getAttributeLabel($field)?>:
			</label>
		</div>
	<?php }?>
	<div class="cols-<?php if ($label) { echo $layoutColumns['field']; }else{?>12<?php }?> column end">
		<div class="data-group collapse in flex-layout flex-top">
			<div class="cols-10 column">
				<div class="dropdown-row">
					<?php echo (!empty($options) || !empty($dropdownOptions)) ? CHtml::dropDownList("{$class}[$field]", $element->$field, $options, empty($dropdownOptions) ? array('empty' => 'Select') : array_merge($dropdownOptions, array('style' => 'width : 100%'))) : ''?>
                </div>
                <?php if($searchBtn){ ?>
                <div class="autocomplete-row" style="display: none">
            <?php
            $this->widget('application.widgets.AutoCompleteSearch',['field_name' => "{$class}_{$field}_searchbox"]);
            ?>
        </div>
                <?php }?>
			</div>
			<div class="cols-2 column">
				<div class="postfix">
                    <?php if($searchBtn){ ?>
					<button class="oe-i search pad-left" id="<?php echo $class.'_'.$field.'_search'?>" style="height: 28px; width: 28px;" type="button">
						<span class="icon-button-small-search" ></span>
						<span style="display: none">Search</span>
					</button>
                    <?php }?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php if($searchBtn){ ?>
<script type="text/javascript" src="<?= Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.widgets.js') . '/AutoCompleteSearch.js', false, -1); ?>"></script>
<script type="text/javascript">
    OpenEyes.UI.AutoCompleteSearch.init({
        input: $('<?="#{$class}_{$field}_searchbox";?>'),
        url: '<?=Yii::app()->createUrl('/disorder/autocomplete');?>',
        maxHeight: '200px',
        onSelect: function(){
            let AutoCompleteResponse =  OpenEyes.UI.AutoCompleteSearch.getResponse();
            var matched = false;
            $('<?="#".$class. "_".$field;?>').children('option').map(function() {
                if ($(this).val() == AutoCompleteResponse.id) {
                    matched = true;
                }
            });
            if (!matched) {
                $('<?="#".$class. "_".$field;?>').append('<option value=\"' + AutoCompleteResponse.id + '\">'+AutoCompleteResponse.value+'</option>');
            }
            $('<?="#".$class. "_".$field;?>').val(AutoCompleteResponse.id).trigger('change');
            $('<?= "#".$class."_".$field."_searchbox";?>').parent().addClass('hide');
        }
    });
	$(document).ready(function() {
		var searchButton = $('#<?php echo $class.'_'.$field.'_search'?>');
		var searchBox = $('#<?php echo $class.'_'.$field.'_searchbox'?>');
		searchButton.on('click', function(e) {
			e.preventDefault();
			(searchBox.parent()).parent().toggle();
            searchBox.closest('.patient-activity').show();
            searchBox.focus();
		});
	});

</script>
<?php }?>