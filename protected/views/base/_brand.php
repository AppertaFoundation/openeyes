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

$logoUrl = Yii::app()->assetManager->getPublishedUrl(Yii::getPathOfAlias('application.assets.nxblu'), true) . '/dist/svg/oe-logo.svg';
$settings = new SettingMetadata();
$tech_support_provider = Yii::App()->params['tech_support_provider'] ? htmlspecialchars(Yii::App()->params['tech_support_provider']) : htmlspecialchars($settings->getSetting('tech_support_provider'));
$tech_support_url = Yii::App()->params['tech_support_url'] ? htmlspecialchars(Yii::App()->params['tech_support_url']) : htmlspecialchars($settings->getSetting('tech_support_url'));
$training_hub_text = Yii::App()->params['training_hub_text'] ? htmlspecialchars(Yii::App()->params['training_hub_text']) : htmlspecialchars($settings->getSetting('training_hub_text'));
$training_hub_url = Yii::App()->params['training_hub_url'] ? htmlspecialchars(Yii::App()->params['training_hub_url']) : htmlspecialchars($settings->getSetting('training_hub_url'));
?>
<div class="oe-logo" id="js-openeyes-btn">
  <svg viewBox="0 0 300.06 55.35" class="oe-openeyes">
    <use xlink:href="<?= $logoUrl . '#openeyes-logo' ?>"></use>
  </svg>
</div>

<div class="oe-product-info" id="js-openeyes-info" style="display: none;">
  <h3>OpenEyes</h3>

  <div class="select-oe-theme">
    <button type="button" id="js-set-theme-light" class="light-theme">Light theme</button>
    <button type="button" id="js-set-theme-dark" class="dark-theme">Pro theme</button>
</div>

<div class="group">
			<h4>Zoom mode (Keys for Win <small class="fade">or</small> Mac)</h4>
			<p>
				<b class="fade">Larger</b>: Ctrl <small class="fade">/</small> ⌘ <small class="fade">and</small> <b>+</b>
				<br><b class="fade">Smaller</b>: Ctrl <small class="fade">/</small> ⌘ <small class="fade">and</small> <b>-</b>
				<br><b class="fade">100% reset</b>: Ctrl <small class="fade">/</small> ⌘ <small class="fade">and</small> <b>0</b>
			</p>
		</div>

  <div class="group">
    <h4>Feedback</h4>
    <p>Send us <a href="<?= Yii::app()->params['feedback_link'] ?>">feedback or suggestions.</a></p>
  </div>

    <?php
    if ($training_hub_text && $training_hub_url) { ?>
        <div class="group">
            <h4>Training documentation</h4>
            <p>
                <a href="<?= $training_hub_url ?>"
                   target=blank><?= $training_hub_text ?></a>
            </p>
        </div>
    <?php } ?>

  <div class="group">
    <h4>Legal</h4>

    <p>OpenEyes is released under the AGPL3 license and is free to download and use.</p>
    <p>OpenEyes is maintained by the <a href="http://apperta.org/" target="_blank">Apperta Foundation</a>. find out more at <a href='https://openeyes.org.uk' target='blank'>openeyes.org.uk</a></p>
    <p>Technical support is provided by <a href="<?= $tech_support_url ?>" target="_blank"><?= $tech_support_provider ?></a>.</p>
  </div>

  <div class="group">
    <h4>Support</h4>
    <p>
    <span class="large-text"> Need Help?&nbsp;
        <?php  $purifier = new CHtmlPurifier(); ?>
        <?php if (SettingMetadata::model()->getSetting('helpdesk_phone') || SettingMetadata::model()->getSetting('helpdesk_email')) : ?>
            <?= SettingMetadata::model()->getSetting('helpdesk_phone') ? $purifier->purify(SettingMetadata::model()->getSetting('helpdesk_phone')) : null ?>
            <?= SettingMetadata::model()->getSetting('helpdesk_email') ? $purifier->purify(SettingMetadata::model()->getSetting('helpdesk_email')) : null ?>
            <?= SettingMetadata::model()->getSetting('helpdesk_hours') ? "<br/>(" . $purifier->purify(SettingMetadata::model()->getSetting('helpdesk_hours')) . ")" : null ?>
        <?php elseif ($tech_support_provider) : ?>
          <a href="<?= $tech_support_url ?>" target="_blank"><?= $tech_support_provider ?></a>
        <?php endif; ?>
    </p>
  </div>
  <div class="group">
    <h4>Version: <?= Yii::App()->params['oe_version'] ?></h4>
    <h4>&copy; OpenEyes <?= date('Y') ?></h4>
    <p>
      <a href="<?= Yii::app()->createUrl('site/debuginfo') ?>" id="support-info-link">
        Served by <?= trim(gethostname()) ?>
      </a>
    </p>
  </div>
  <div class="group">
    <p>
      Execution time: <span class="js-execution-time"></span>s<br />
      Memory usage: <span class="js-memory-usage"></span>
    </p>
  </div>
</div>

<script>
  $(function() {

    // swap the stylesheet, when the user picks a different theme,
    const darkBtn = document.getElementById("js-set-theme-dark");
    const lightBtn = document.getElementById("js-set-theme-light");

    /** Change the theme class on <html> */
    const switchTheme = ( theme ) => {
        document.documentElement.className = `theme-${theme}`;
        <?php if (!Yii::app()->user->isGuest) : ?>
          // Change the user's theme setting if they are logged in
          $.ajax({
            'type': 'GET',
            'url': "<?= Yii::app()->createUrl('/profile/changeDisplayTheme') ?>",
            'data': {
              'display_theme': theme
            }
          });
        <?php endif; ?>
    };

    darkBtn.onclick = () => switchTheme("dark");
    lightBtn.onclick = () => switchTheme("light");

    $('#support-info-link').click(function(e) {
      e.preventDefault();
      new OpenEyes.UI.Dialog({
        url: this.href,
        title: 'Support Information'
      }).open();
    });

    document.querySelector('.js-execution-time').innerHTML = execution_time;
    document.querySelector('.js-memory-usage').innerHTML = memory_usage;
  });

</script>
