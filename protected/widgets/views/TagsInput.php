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

    $div_id = @$htmlOptions['div_id'];
    $div_class = isset($htmlOptions['div_class']) ? $htmlOptions['div_class'] : 'eventDetail';
?>
<div id="<?php echo $div_id ?>" class="<?php echo $div_class ?> data-group widget"<?php if ($hidden) {
    ?> style="display: none;"<?php
         }?>>
    <div class="cols-<?php echo $layoutColumns['label'];?> column">
        <label for="<?php echo $field?>">
            <?php echo $label; ?>:
        </label>
    </div>
    <div class="cols-<?php echo $layoutColumns['field'];?> column end">
        <div class="oe-tagsinput-wrapper">
            <input
                name="<?=\CHtml::modelName($element)."[$field]"; ?>" id="tags"
                value="<?php echo implode(',', $this->default_tags); ?>"
                type="text" class="tagsinput"
                <?php if ($this->autocomplete_url) {
                    echo 'data-autocomplete-url = "'.$this->autocomplete_url.'"';
                } ?>
            />
        </div>
    </div>
</div>

<?php
    $assetManager = Yii::app()->getAssetManager();
$widgetPath = $assetManager->getPublishedPathOfAlias('application.widgets.js');
    $assetManager->registerScriptFile('components/jquery.tagsinput/src/jquery.tagsinput.js');
    Yii::app()->clientScript->registerScriptFile($widgetPath . '/TagsInput.js');
?>
