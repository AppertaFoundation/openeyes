<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>
<?php if ($cbs_by_type = $this->patient->getDistinctCommissioningBodiesByType()) {
	foreach (CommissioningBodyType::model()->findAll() as $cbt) {
		if (array_key_exists($cbt->id, $cbs_by_type)) { ?>
			<section class="box patient-info js-toggle-container">
				<h3 class="box-title"><?= $cbt->name ?>(s):</h3>
				<a href="#" class="toggle-trigger toggle-hide js-toggle">
					<span class="icon-showhide">
						Show/hide this section
					</span>
				</a>
				<div class="js-toggle-body">
					<?php foreach ($cbs_by_type[$cbt->id] as $cb) { ?>
						<div class="row data-row">
							<div class="large-4 column">
								<div class="data-label"><?= $cb->getTypeShortName() ?>:</div>
							</div>
							<div class="large-8 column">
								<div class="data-value"><?= $cb->name ?></div>
							</div>
						</div>
					<?php } ?>
				</div>
			</section>
		<?php }?>
	<?php } ?>
<?php } ?>
