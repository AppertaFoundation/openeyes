<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>
<div class="box admin">
	<div class="row">
		<div class="large-8 column">
			<h2>Internal Referral Settings</h2>
		</div>
		<div class="large-4 column">
		</div>
	</div>

	<form id="internal_referral_settings">
		<input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken?>" />
		<table class="grid">
			<thead>
				<tr>
					<th>Setting</th>
					<th>Value</th>
				</tr>
			</thead>
			<tbody>
				<?php
                foreach ($settings as $metadata) {?>
					<tr class="clickable" data-key="<?php echo $metadata->key?>">
						<td><?php echo $metadata->name?></td>
						<td><?php echo $metadata->getSettingName()?></td>
					</tr>
				<?php }?>
			</tbody>
		</table>
	</form>
    <br>
    <div id="internal_referral_to_location">
        <div class="row">
            <div class="large-8 column">
                <h3>Add sites to the To Location dropdown</h3>
            </div>
            <div class="large-4 column">
            </div>
        </div>

        <div class="row">
            <div class="large-1 column">Site:</div>
            <div class="large-4 column">
                 <?php echo CHtml::dropDownList('to_location_site_id', null, Site::model()->getListForCurrentInstitution(), array('empty' => '- select -'))?>
            </div>
            <div class="large-3 end column right">
                <img class="loader right" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
                <span class="right saved hidden" style="font-size:13px; color:#19b910">Saved</span>
                <span class="right error hidden" style="font-size:13px"">Error, try again later</span>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="large-8 column">
                <table class="grid" id="to_location_sites_grid">
                    <thead>
                    <tr>
                        <th style="width:400px">Site</th>
                        <th>action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($to_locations as $to_location) {?>
                        <tr class="site-row">
                            <td>
                                <?php echo $to_location->site->short_name; ?>
                                <?php echo CHtml::activeHiddenField($to_location, '[]site_id'); ?>
                            </td>
                            <td class="site_action">
                                <a href="javascript:void(0)" class="remove">remove</a>
                            </td>
                        </tr>
                    <?php }?>

                    <tr class="no-sites <?php echo $to_locations ? 'hidden' : ''?>"><td colspan="2">No sites</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="large-4 column"></div>
        </div>
    </div>
</div>
