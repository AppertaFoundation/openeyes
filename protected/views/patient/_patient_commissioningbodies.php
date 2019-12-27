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
<?php if ($cbs_by_type = $this->patient->getDistinctCommissioningBodiesByType()) {
    foreach (CommissioningBodyType::model()->findAll() as $cbt) {
        if (array_key_exists($cbt->id, $cbs_by_type)) { ?>
            <section class="element patient-info js-toggle-container">
                <h3 class="element-header"><?= $cbt->name ?>(s):</h3>
                <div class="js-toggle-body">
                    <?php foreach ($cbs_by_type[$cbt->id] as $cb) { ?>
                        <div class="data-group">
                            <div class="cols-4 column">
                                <div class="data-label"><?= $cb->getTypeShortName() ?>:</div>
                            </div>
                            <div class="cols-8 column">
                                <div class="data-value"><?= $cb->name ?></div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </section>
        <?php }?>
    <?php } ?>
<?php } ?>
