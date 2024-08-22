<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * (C) Apperta Foundation CIC 2014 - Present
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://openeyes.apperta.org
 *
 * @author OpenEyes <openeyes@apperta.org>
 * @copyright Copyright (c) 2024 Apperta Foundation CIC
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

$logoUrl = Yii::app()->assetManager->getPublishedUrl(Yii::getPathOfAlias('application.assets.newblue'), true) . '/dist/svg/oe-logo.svg';
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

  <div class="group">
    <h4>Theme</h4>

    <p>
      <a href="#" id="js-theme-dark" class="theme-picker" data-theme="dark" style="display: block; margin-bottom: 4px;">Dark theme (recommended)</a>
      <a href="#" id="js-theme-light" class="theme-picker" data-theme="light">Light theme (default)</a>
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
    <p>OpenEyes is under the custodianship of the <a href="http://apperta.org/" target="_blank">Apperta Foundation</a>. Find out more at <a href='https://openeyes.apperta.org' target='blank'>openeyes.apperta.org</a></p>
    <p>No warranty is provided by any part, implied or otherwise, for use of this software for versions deployed without an Accredited Professional Services Parter.</p>
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
    <h4>Copyright&#169; Apperta Foundation CIC <?= date('Y') ?></h4>
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
    $('.theme-picker').click(function() {
      var theme = $(this).data('theme');

      var $light_theme = $('link[data-theme="light"]');
      var $dark_theme = $('link[data-theme="dark"]');

      // hide all elements: for a split second, all elements are shown without formatting (no css is used)
      $('.open-eyes').hide();
      // change css for current theme
      $light_theme.prop('media', theme === 'light' ? '' : 'none');
      $dark_theme.prop('media', theme === 'dark' ? '' : 'none');
      // show all elements
      setTimeout(function() {
        $('.open-eyes').show();
      }, 100);

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
    });

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
