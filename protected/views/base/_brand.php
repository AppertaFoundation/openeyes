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

$logoUrl = Yii::app()->assetManager->getPublishedUrl(Yii::getPathOfAlias('application.assets.newblue'), true) . '/svg/oe-logo.svg';
$settings = new SettingMetadata();
$tech_support_provider = Yii::App()->params['tech_support_provider'] ? htmlspecialchars(Yii::App()->params['tech_support_provider']) : htmlspecialchars($settings->getSetting('tech_support_provider'));
$tech_support_url = Yii::App()->params['tech_support_url'] ? htmlspecialchars(Yii::App()->params['tech_support_url']) : htmlspecialchars($settings->getSetting('tech_support_url'))
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
        <?php if (Yii::app()->params['helpdesk_phone'] || Yii::app()->params['helpdesk_email']) : ?>
            <?= Yii::app()->params['helpdesk_phone'] ? $purifier->purify(Yii::app()->params['helpdesk_phone']) : null ?>
            <?= Yii::app()->params['helpdesk_email'] ? $purifier->purify(Yii::app()->params['helpdesk_email']) : null ?>
            <?= Yii::app()->params['helpdesk_hours'] ? "<br/>(" . $purifier->purify(Yii::app()->params['helpdesk_hours']) . ")" : null ?>
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

  });
</script>
