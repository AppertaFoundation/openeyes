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

        $user_auth = Yii::app()->params['user_auth'];
        if (
            $user_auth && $user_auth->institutionAuthentication->user_authentication_method == "LOCAL"
            && PasswordUtils::testStatus('stale', $user_auth) && empty(Yii::app()->session['shown_pw_reminder'])
        ) {
            Yii::app()->session['shown_pw_reminder'] = true;
            $this->widget('PasswordStaleWidgetReminder');
        }

        if (empty(Yii::app()->session['shown_version_reminder']) && Yii::app()->user->checkAccess('admin')) {
            Yii::app()->session['shown_version_reminder'] = true;
            $this->widget('VersionCheckWidgetReminder');
        }
    }
    if (empty(Yii::app()->session['user'])) {
        Yii::app()->session['user'] = User::model()->findByPk(Yii::app()->user->id);
    }

    $user = Yii::app()->session['user'];

    $menuHelper = new MenuHelper(Yii::app()->params['menu_bar_items'], Yii::app()->user, $uri);
    $navIconUrl = Yii::app()->assetManager->getPublishedUrl(Yii::getPathOfAlias('application.assets.newblue'), true) . '/dist/svg/oe-nav-icons.svg';
    if ($as_clinic) { ?>
        <div class="clinic-context">
            <div class="favourite-btn js-favourite"></div>
            <div class="details">
                <div class="context">
                    <?= Firm::model()->findByPk($this->selectedFirmId)->getNameAndSubspecialty() ?>
                    <span class="lists"></span>
                </div>
                <div class="date-range"></div>
            </div>
        </div>
    <?php } else { ?>
        <div class="oe-user-banner">
            <?php $this->renderPartial('//base/_banner_watermark'); ?>
        </div>
    <?php } ?>    <div class="oe-user">
        <ul class="oe-user-profile-context">
            <li><?= $user->first_name . ' ' . $user->last_name; ?>
                <?php if (Yii::app()->params['profile_user_can_edit']) { ?>
                    <a href="<?= Yii::app()->createUrl('/profile'); ?>">profile</a>
                <?php } ?>
            </li>
            <li id="user-profile-site-institution"><?= Site::model()->findByPk($this->selectedSiteId)->short_name . ' (' .
                    Institution::model()->findByPk($this->selectedInstitutionId)->short_name . ')' ?></li>
            <li>
                <?= Firm::model()->findByPk($this->selectedFirmId)->getNameAndSubspecialty() ?>
                <a id="change-firm" href="#" data-window-title="Select a new Site and/or <?= Firm::contextLabel() ?>">change</a>
            </li>
        </ul>
    </div>
    <div class="oe-nav">
        <ul class="oe-big-icons">
            <li class="oe-nav-btn">
                <a class="icon-btn" href="/" id="js-home-btn">
                    <svg viewBox="0 0 80 40" class="icon home">
                        <use xlink:href="<?= $navIconUrl . '#home-icon'; ?>"></use>
                    </svg>
                </a>
            </li>
            <?= $menuHelper->render($navIconUrl) ?>
            <!--            The exclude admin structure parameter list has elements that can be excluded from the admin sidebar, if Worklist is excluded from that, then it can be removed from the home screen too-->
            <?php if (!in_array("Worklist", Yii::app()->params['exclude_admin_structure_param_list'])) : ?>
                <li class="oe-nav-btn">
                    <?php if (!isset($this->layout) || $this->layout !== 'worklist') : ?>
                    <a class="icon-btn" href="<?= Yii::app()->createUrl('worklist/view') ?>">
                    <?php else : ?>
                    <a class="nav-js-btn icon-btn" id="js-nav-worklist-btn" onclick="return false;">
                    <?php endif; ?>
                        <svg viewBox="0 0 80 40" class="icon clinic ">
                            <use xlink:href="<?= $navIconUrl . '#clinic-icon' ?>"></use>
                        </svg>
                    </a>
                    <?php
                    if (isset($this->layout) && $this->layout === 'worklist') {
                        $this->renderPartial('//base/_worklist_filters_panel');
                    }
                    ?>
                </li>
            <?php endif; ?>
            <li class="oe-nav-btn js-hotlist-panel-wrapper">
                <a class="nav-js-btn icon-btn" id="js-nav-hotlist-btn" onclick="return false;" data-fixable="<?= $this->fixedHotlist ? 'true' : 'false' ?>">
                    <svg viewBox="0 0 80 40" class="icon hotlist">
                        <use xlink:href="<?= $navIconUrl . '#hotlist-icon' ?>"></use>
                    </svg>
                </a>
                <?php $this->renderPartial('//base/_hotlist'); ?>
            </li>
            <li class="oe-nav-btn">
                <a id="js-logout-btn" class="icon-btn" href="<?= Yii::app()->createUrl('/site/logout'); ?>">
                    <svg viewBox="0 0 80 40" class="icon logout">
                        <use xlink:href="<?= $navIconUrl . '#logout-icon'; ?>"></use>
                    </svg>
                    <img src="" class="icon-logout" />
                </a>
            </li>
        </ul>
    </div>

    <script type="text/javascript">
        $("#js-logout-btn").click(function() {
            window.stop();
            return true;
        });
    </script>

<?php } ?>
