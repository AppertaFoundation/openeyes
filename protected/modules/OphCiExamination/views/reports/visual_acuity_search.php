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

<div class="report-search">
    <form class="report-search-form mdl-color-text--grey-600" action="/report/reportData">
        <input type="hidden" name="report" value="<?= $report->getApp()->getRequest()->getQuery('report'); ?>" />
        <fieldset>
            <div class="mdl-selectfield">
                <label for="visual-acuity-months">Months Post Op</label>
                <select name="months" id="visual-acuity-months" class="browser-default">
                    <?php foreach (range(1, 300) as $month): ?>
                        <option value="<?=$month?>" <?=($month == 4) ? 'selected' : '' ?>><?=$month?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="mdl-selectfield">
                <label for="visual-acuity-methods">Method</label>
                <select name="method" id="visual-acuity-methods" class="browser-default">
                    <option value="">All</option>
                    <option value="best" selected>Best Corrected</option>
                    <?php foreach ($methods as $method):?>
                    <option value="<?=$method['id']?>"><?=$method['name']?></option>
                    <?php endforeach;?>
                </select>
            </div>
            <div>
                <label class="mdl-radio mdl-js-radio mdl-js-ripple-effect" for="visual-acuity-distance">
                    <input checked class="mdl-radio__button" id="visual-acuity-distance" name="type" type="radio" value="distance">
                    <span class="mdl-radio__label">Distance</span>
                </label>
                <label class="mdl-radio mdl-js-radio mdl-js-ripple-effect" for="visual-acuity-near">
                    <input class="mdl-radio__button" id="visual-acuity-near" name="type" type="radio" value="near">
                    <span class="mdl-radio__label">Near</span>
                </label>
            </div>
            <div>
                <button class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored" type="submit" name="action">Submit
                    <i class="material-icons right">send</i>
                </button>
            </div>
        </fieldset>
    </form>
</div>
