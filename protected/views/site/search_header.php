<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<?php if (($tabs = Yii::app()->params['search_tabs'])): ?>
	<div class="row">
		<div class="large-8 large-centered column panel">
			<ul class="inline-list tabs search">
				<?php
                    $tabs[] = array(
                        'title' => 'OpenEyes search',
                        'url' => '/',
                        'position' => 0,
                    );

                    usort($tabs, function ($a, $b) { return ($a['position'] < $b['position']) ? -1 : 1; });
                ?>
				<?php foreach ($tabs as $tab): ?>
					<li<?php if ($tab['url'] == Yii::app()->request->requestUri) echo ' class="selected"' ?> >
						<a href="<?= $tab['url'] ?>"><?= CHtml::encode($tab['title']) ?></a>
					</li>
				<?php endforeach ?>
			</ul>
		</div>
	</div>
<?php endif ?>
