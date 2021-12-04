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

$navIconUrl = Yii::app()->getAssetManager()->getPublishedUrl(Yii::getPathOfAlias('application.assets.newblue'), true) . '/dist/svg/oe-nav-icons.svg';
?>

<?php if ($flash_messages = Yii::app()->user->getFlashes()) { ?>
    <?php
    ksort($flash_messages);
    foreach ($flash_messages as $flash_key => $flash_message) {
        $parts = explode('.', $flash_key);
        $class = isset($parts[0]) ? $parts[0] : 'info';
        $iconClass = ($class === 'warning') ? 'triangle' : $class;
        $id = isset($parts[1]) ? $parts[1] : $parts[0];
        ?>
      <div id="flash-<?php echo $id; ?>" class="alert-box <?php echo $class ?>">
          <?php echo $flash_message; ?>
      </div>
        <?php
    }
    ?>
<?php } ?>
