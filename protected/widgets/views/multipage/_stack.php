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
 */
?>
<div class="multipage-stack<?= $this->stack_class ? ' ' . $this->stack_class : null ?>">
    <?php foreach ($this->images as $id => $image) :
        $page_num = $id + 1;?>
        <?php if ($this->full_width) : ?>
            <img
                id="multipage-img-mp_8-p<?= $page_num ?>"
                src="<?= $image->getImageUrl() ?>"
                width="100%"
                height="auto"
                alt="p<?= $page_num ?>"/>
        <?php else : ?>
        <img
            id="multipage-img-mp_8-p<?= $page_num ?>"
            src="<?= $image->getImageUrl() ?>"
            alt="p<?= $page_num ?>"/>
        <?php endif; ?>
    <?php endforeach; ?>
</div>