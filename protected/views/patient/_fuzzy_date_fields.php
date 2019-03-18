<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later
 * version. OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more
 * details. You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled
 * COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<div class="data-group">
    <div class="cols-3 column">
        <select class="fuzzy_day">
            <option value="00">- Day -</option>
            <?php for ($i = 1;$i <= 31;++$i) {?>
                <option value="<?= $i?>"<?= ($i == $sel_day) ? ' selected' : ''?>><?= $i?></option>
            <?php }?>
        </select>
    </div>
    <div class="cols-4 column">
        <select class="fuzzy_month">
            <option value="00">- Month- </option>
            <?php foreach (array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December') as $i => $month) {?>
                <option value="<?= $i + 1?>"<?= ($i + 1 == $sel_month) ? ' selected' : ''?>><?= $month?></option>
            <?php }?>
        </select>
    </div>
    <div class="cols-3 column">
        <select class="fuzzy_year">
            <option value="0000">- Year -</option>
            <?php for ($i = date('Y') - 102;$i <= date('Y');++$i) {?>
                <option value="<?= $i?>"<?= ($i == $sel_year) ? ' selected' : ''?>><?= $i?></option>
            <?php }?>
        </select>
    </div>
    <div class="cols-1 column end">
      <span class="js-has-tooltip fa oe-i info small right" style="margin-top:3px"
            data-tooltip-content="Day, Month and Year fields are optional."></span>
    </div>
</div>
