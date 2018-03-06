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
$logoUrl = Yii::app()->assetManager->getPublishedUrl(Yii::getPathOfAlias('application.assets.newblue')) . '/svg/oe-logo.svg';
$commit = preg_replace('/[\s\t].*$/s', '', @file_get_contents(Yii::app()->basePath . '/../.git/FETCH_HEAD'));
$commitDate = exec("git show -s --format=%cD $commit");
?>
<div class="oe-logo" id="js-openeyes-btn">
  <svg viewBox="0 0 300.06 55.35" class="oe-openeyes">
    <use xlink:href="<?= $logoUrl . '#openeyes-logo'; ?>"></use>
  </svg>
</div>

<div class="oe-product-info" id="js-openeyes-info">
  <h3>OpenEyes 3.0</h3>


  <div class="group">
    <h4>Theme</h4>

    <p>
      <a href="#" id="js-theme-dark" class="theme-picker" data-theme="dark"
         style="display: block; margin-bottom: 4px;">Dark theme (recommended)</a>
      <a href="#" id="js-theme-light" class="theme-picker" data-theme="light">Light theme (default)</a>
    </p>
  </div>

  <div class="group">
    <h4>Tour &amp; Feedback</h4>

    <p>Learn about OpenEyes 3.0 - take the <a href="#">OE Feature Tour</a></p>
    <p>Send us <a href="#">feedback or suggestions.</a></p>
  </div>

  <div class="group">
    <h4>Legal</h4>

    <p>OpenEyes is released under the AGPL3 license and is free to download and use.</p>
    <p>OpenEyes is maintained by the <a href="https://openeyes.org.uk/" target="_blank">OpenEyes Foundation</a>.</p>
    <p>Technical support is provided by <a href="https://www.abehr.com/" target="_blank">ABEHRdigital</a>.</p>
  </div>

  <div class="group">
    <h4>Support</h4>
    <p>
      <span class="large-text"> Need Help?
          <?php if (Yii::app()->params['helpdesk_email']) { ?>
    <p><?php echo Yii::app()->params['helpdesk_email'] ?></p>
      <?php } ?>
      <?php if (Yii::app()->params['helpdesk_phone']) { ?>
        <p><?php echo Yii::app()->params['helpdesk_phone'] ?></p>
      <?php } ?>
    </span>
    </p>
  </div>
  <div class="group">
    <h4>&copy; OpenEyes <?php
        echo date('Y', $time = strtotime($commitDate)); ?></h4>
  </div>

</div>
<script>

  $(function () {
    $('.theme-picker').click(function () {
      var theme = $(this).data('theme');
      var old_css_path = theme === 'dark' ? lightThemeFilePath : darkThemeFilePath;
      var new_css_path = theme === 'dark' ? darkThemeFilePath : lightThemeFilePath;

      $('link[href*="' + old_css_path + '"]').attr('href', new_css_path);

        <?php if (!Yii::app()->user->isGuest): ?>
      $.ajax({
        'type': 'GET',
        'url': "<?= Yii::app()->createUrl('/profile/changeDisplayTheme') ?>",
        'data': {'display_theme': theme}
      });
        <?php endif; ?>
    });
  });
</script>
