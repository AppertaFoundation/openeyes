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
 *
 * @var $total_pages int
 */
?>
<nav class="multipage-nav">
    <?php if ($this->inline_nav) : ?>
        <?php for ($i = 0; $i < $this->total_pages; $i++) :
            $page_num = $i + 1 ?>
            <div class="page-num-btn" data-page="<?= $i ?>"><?= "{$page_num}/{$total_pages}"?></div>
        <?php endfor; ?>
    <?php else : ?>
        <h3><?= $this->nav_title ?></h3>
        <div class="page-jump">
            <?php for ($i = 0; $i < $total_pages; $i++) :
                $page_num = $i + 1
                ?>
                <div class="page-num-btn" data-page="<?= $i ?>"><?= $page_num ?></div>
            <?php endfor; ?>
            <div class="page-scroll">
                <div class="page-scroll-btn up" id="js-scroll-btn-up"></div>
                <div class="page-scroll-btn down" id="js-scroll-btn-down"></div>
            </div>
        </div>
    <?php endif; ?>
</nav>
