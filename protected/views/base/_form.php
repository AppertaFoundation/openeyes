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
<?php
$uri = preg_replace('/^\//', '', preg_replace('/\/$/', '', $_SERVER['REQUEST_URI']));

if (!Yii::app()->user->isGuest) {
    $user = User::model()->findByPk(Yii::app()->user->id);
    if (!preg_match('/^profile\//', $uri)) {
        if (!$user->has_selected_firms && !$user->global_firm_rights && empty(Yii::app()->session['shown_reminder'])) {
            Yii::app()->session['shown_reminder'] = true;
            $this->widget('SiteAndFirmWidgetReminder');
        }
    }
    if (empty(Yii::app()->session['user'])) {
        Yii::app()->session['user'] = User::model()->findByPk(Yii::app()->user->id);
    }
    $user = Yii::app()->session['user'];
    $menuHelper = new MenuHelper(Yii::app()->params['menu_bar_items'], Yii::app()->user, $uri);
    $navIconUrl = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.assets.newblue.svg') . '/oe-nav-icons.svg');
    ?>

  <div class="oe-user">
    <ul class="oe-user-profile-context">
      <li><em>User</em><?= $user->first_name . ' ' . $user->last_name; ?>
          <?php if (Yii::app()->params['profile_user_can_edit']) { ?>
            <a href="<?= Yii::app()->createUrl('/profile'); ?>">profile</a>
          <?php } ?>
      </li>

      <li><em>Site</em><?= Site::model()->findByPk($this->selectedSiteId)->short_name; ?></li>
      <li>
        <em><?= Firm::contextLabel() ?></em><?= Firm::model()->findByPk($this->selectedFirmId)->getNameAndSubspecialty(); ?>
        <span class="change-firm"><a href="#" data-window-title="Select a new Site and/or <?= Firm::contextLabel() ?>">change</a></span>
      </li>
    </ul>
  </div>
  <div class="oe-nav">
    <ul class="oe-big-icons">
      <li class="oe-nav-btn">
        <a class="icon-btn" href="/">
          <svg viewBox="0 0 80 40" class="icon home">
            <use xlink:href="<?= $navIconUrl . '#home-icon'; ?>"></use>
          </svg>
        </a>
      </li>
        <?= $menuHelper->render($navIconUrl) ?>
      <li class="oe-nav-btn">
        <a class="icon-btn" href="<?= Yii::app()->createUrl('/site/logout'); ?>">
          <svg viewBox="0 0 80 40" class="icon-logout">
            <use xlink:href="<?= $navIconUrl . '#logout-icon'; ?>"></use>
          </svg>
          <img src="" class="icon-logout"/>
        </a>
      </li>
    </ul>
  </div>


<?php } ?>
