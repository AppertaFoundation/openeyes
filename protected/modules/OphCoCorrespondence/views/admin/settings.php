<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<form id="internal_referral_settings">
    <input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken ?>"/>
    <div class="cols-5">
        <table class="standard">
            <thead>
            <tr>
                <th>Setting</th>
                <th>Value</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($settings as $metadata) { ?>
                <tr class="clickable" data-key="<?php echo $metadata->key ?>">
                    <td><?php echo $metadata->name ?></td>
                    <td><?php echo $metadata->getSettingName() ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</form>

<br>

<div id="internal_referral_to_location">
    <div class="data-group">
        <div class="cols-8 column">
            <h3>Add sites to the 'To Location' dropdown</h3>
        </div>
        <div class="cols-4 column">
        </div>
    </div>

    <div class="data-group">
        <div class="cols-4 column">&nbsp
        </div>
        <div class="cols-4 end column right">
            <img class="loader right" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif') ?>"
                 alt="loading..." style="display: none;"/>
            <span class="right saved hidden" style="font-size:13px; color:#19b910">Saved</span>
            <span class="right error hidden" style="font-size:13px"">Error, try again later</span>
        </div>
    </div>

    <div class="data-group">
        <div class="cols-5 column">
            <table class="standard" id="to_location_sites_grid">
                <thead>
                <tr>
                    <th>Site</th>
                    <th>Location Code (XML)</th>
                    <th class="text-center">Is Active</th>
                </tr>
                <colgroup>
                    <col class="cols-1">
                    <col class="cols-6">
                    <col class="cols-1">
                </colgroup>
                </thead>
                <tbody>
                <?php
                foreach ($sites as $index => $site) { ?>
                    <?php

                    $to_location = isset($site->toLocation) ? $site->toLocation : null;
                    if (!$to_location) {
                        $to_location = new OphCoCorrespondence_InternalReferral_ToLocation();
                        $to_location->site_id = $site->id;
                    }
                    ?>

                    <?php
                    //@TODO : move this to API
                    $criteria = new CDbCriteria();
                    $criteria->join = 'JOIN ophcocorrespondence_internal_referral_to_location l ON t.to_location_id = l.id';
                    $criteria->addCondition('l.site_id = :site_id');
                    $criteria->params = array(':site_id' => $site->id);

                    $letter_count = ElementLetter::model()->count($criteria);
                    ?>

                    <tr class="site-row">
                        <td>
                            <?php echo $site->short_name; ?>
                            <?=\CHtml::activeHiddenField($to_location, "[$index]id"); ?>
                            <?=\CHtml::activeHiddenField($to_location, "[$index]site_id"); ?>
                        </td>
                        <td>
                            <?=\CHtml::activeTextField(
                                $site,
                                "[$index]location_code",
                                ["disabled" => "disabled", "class" => "cols-full"]
                            ); ?>
                        </td>
                        <td class="text-center">
                            <?=\CHtml::activeCheckBox($to_location, "[$index]is_active"); ?>
                        </td>
                    </tr>
                <?php } ?>

                <tr class="no-sites <?php echo $to_locations ? 'hidden' : '' ?>">
                    <td colspan="2">No sites</td>
                </tr>
                </tbody>

                <tfoot>
                <tr>
                    <td colspan="5">
                        <?=\CHtml::submitButton(
                            'Save',
                            [
                                'class' => 'classy blue button large primary event-action',
                                'name' => 'save',
                                'id' => 'save_to_location_table'
                            ]
                        ); ?>
                    </td>
                </tr>
                </tfoot>

                <!--<button type="button" class="classy blue mini small" id="save_to_location_table"><span class="button-span button-span-blue">Save</span></button>-->

            </table>
        </div>
        <div class="cols-4 column"></div>
    </div>
</div>