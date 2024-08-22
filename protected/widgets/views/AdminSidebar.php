<?php

/**
 * (C) OpenEyes Foundation, 2018
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
<nav class="oe-full-side-panel admin-panels">
    <!-- expand collapse all groups -->
    <div class="js-groups">
        <span class="js-expand-all"><i class="oe-i plus"></i></span>
        <span class="js-collapse-all"><i class="oe-i minus"></i></span>
    </div>

    <?php

    // Sort the menu items array alphabetically
    ksort($menu_items);

    foreach ($menu_items as $box_title => $box_items) :
        $state = $this->getGroupState($box_title);
        ?>
    <div class="groupings">
        <div class="collapse-group" data-collapse="<?= $state; ?>">
            <div class="collapse-group-icon"><i class="oe-i <?= $state === 'expanded' ? 'minus' : 'plus'; ?>"></i></div>
            <h3 class="collapse-group-header"><?= $box_title ?></h3>
            <ul class="admin-nav collapse-group-content" <?= $state === 'expanded' ? '' : 'style="overflow: hidden; display: none;"' ?>>
                <?php
                // Sort the sub-menu items alphabetically
                ksort($box_items);
                foreach ($box_items as $name => $data) : ?>
                    <li>
                        <a class="<?= (Yii::app()->getController()->request->requestUri == $data ? 'selected' : '')?>" href="<?= $data ?>"><?= $name ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php endforeach; ?>
</nav>
