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
<div id="div_LetterString_name" class="row field-row">
	<div class="large-2 column">
		<label for="LetterString_name">Body:</label>
	</div>
	<div class="large-5 column end">
		<?php echo  CHtml::activeTextArea($model, 'body')?>
	</div>
</div>
<div class="row field-row">
	<div class="large-8 large-offset-2 column">
		<div class="row field-row">
			<div class="large-3 column">
				<label for="shortcode">
					Add shortcode:
				</label>
			</div>
			<div class="large-6 column end">
				<?php echo CHtml::dropDownList('shortcode', '', CHtml::listData(PatientShortcode::model()->findAll(array('order' => 'description asc')), 'code', 'description'), array('empty' => '- Select -'))?>
			</div>
		</div>
	</div>
</div>

