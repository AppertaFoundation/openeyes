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
<div class="data-group">
    <div class="cols-<?php echo 12 - $layoutColumns['label']; ?> large-offset-<?php echo $layoutColumns['label']; ?> column">
        <?= CHtml::htmlButton($buttonOptions['submit'], ['name' => 'save', 'class' => 'button large', 'type' => 'submit', 'id' => 'et_save']); ?>
        <?php if ($buttonOptions['cancel']) {
            $cancelHtmlOptions = array('class' => 'button large');
            if (@$buttonOptions['cancel-uri']) {
                $cancelHtmlOptions['data-uri'] = $buttonOptions['cancel-uri'];
            }
            echo CHtml::submitButton(
                $buttonOptions['cancel'],
                [
                    'data-uri' => isset($buttonOptions['cancel-uri']) ? $buttonOptions['cancel-uri'] : '',
                    'class' => 'button large',
                    'name' => 'cancel',
                    'id' => 'et_cancel',
                ]
            );
        } ?>
        <?php if ($buttonOptions['delete']) {
            echo CHtml::htmlButton($buttonOptions['delete'],
                [
                    'name' => 'delete',
                    'class' => 'button large',
                    'id' => 'et_delete',
                    'data-uri' => $buttonOptions['delete-uri'] ?? ''
                ]);
        } ?>
        <?php
        if (isset($buttonOptions['add-snippet'])) {
            // GenericAdmin and FormActions is so great that I cannot add extra buttons dynamically.... so let's just hardcode here
            //@TODO: get rid of the GenericAdmin (+FormActions) to not to risk devs mental health

            echo EventAction::link('Add Snippet', $buttonOptions['add-snippet'], array(), array('type' => 'button', 'class' => 'button large'))->toHtml();
        } ?>
        <img class="loader" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif') ?>"
             alt="loading..." style="display: none;"/>
    </div>
</div>
