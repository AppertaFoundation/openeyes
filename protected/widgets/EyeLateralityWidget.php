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
class EyeLateralityWidget extends CWidget
{

    public $laterality;
    public $eye;
    public $size = 'small';
    public $pad = 'pad';

    public function run()
    {
        $left = false;
        $right = false;

        if ($this->eye !== null) {
            $left = $this->eye->id & Eye::LEFT;
            $right = $this->eye->id & Eye::RIGHT;
        } else {
            switch (strtolower($this->laterality)) {
                case 'left':
                case 'l':
                    $left = true;
                    $right = false;
                    break;
                case 'right':
                case 'r':
                    $left = false;
                    $right = true;
                    break;
                case 'b':
                case 'bilateral':
                case 'both':
                    $left = true;
                    $right = true;
                    break;
            }
        }

        $this->beginWidget('CondenseHtmlWidget');
        ?>
      <span class="oe-eye-lat-icons">
        <i class="oe-i laterality <?= $this->size . ' ' . $this->pad . ' ' . ($right ? 'R' : 'NA') ?>"></i>
        <i class="oe-i laterality <?= $this->size . ' ' . $this->pad . ' ' . ($left ? 'L' : 'NA') ?>"></i>
      </span>
        <?php
        $this->endWidget();
    }
}
