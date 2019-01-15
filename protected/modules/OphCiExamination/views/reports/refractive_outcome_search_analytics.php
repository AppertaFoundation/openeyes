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

    <form class="report-search-form mdl-color-text--grey-600" action="/report/reportData" style="display: none">
        <input type="hidden" name="report" value="<?= $report->getApp()->getRequest()->getQuery('report'); ?>" />
        <fieldset>
            <div id="search-form-to-side-bar">
            <div class="mdl-selectfield">
                <h3>Months Post Op</h3>
                <select name="months" id="refractive-outcome-months" style="font-size: 1em; width: inherit">
                    <?php foreach (range(0, 300) as $month): ?>
                        <option value="<?=$month?>"><?=($month) ? $month : 'All'?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="checkbox-select">
                <h3>Procedures</h3>
                <label  for="refractive-outcome-proc-all">
                    <input type="checkbox" id="refractive-outcome-proc-all" class="mdl-checkbox__input" name="procedures[]" value="all" checked>
                    <span >All</span>
                </label>
                <?php
                foreach ($procedures as $id => $procedure):?>
                    <label  class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="refractive-outcome-proc-<?= $id?>">
                        <input type="checkbox" id="refractive-outcome-proc-<?= $id?>" class="mdl-checkbox__input refractive_outcome_specific_procedure" name="procedures[]" value="<?= $id?>">
                        <span><?= $procedure?></span>
                    </label>
                <?php endforeach;?>
            </div>
            </div>
            <div>
                <button type="submit" name="action">Submit</button>
            </div>
        </fieldset>
    </form>