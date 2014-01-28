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
<div class="row field-row">
	<div class="large-<?php echo 12 - $layoutColumns['label'];?> large-offset-<?php echo $layoutColumns['label'];?> column">
		<?php echo EventAction::button($buttonOptions['submit'], 'save', array(), array('class' => 'button small'))->toHtml()?>
		<?php if ($buttonOptions['cancel']) {
			$cancelHtmlOptions = array('class' => 'warning button small');
			if (@$buttonOptions['cancel-uri']) {
				$cancelHtmlOptions['data-uri'] = $buttonOptions['cancel-uri'];
			}
			echo EventAction::button($buttonOptions['cancel'], 'cancel', array(), $cancelHtmlOptions)->toHtml();
		}?>
		<?php if ($buttonOptions['delete']) {
			echo EventAction::button($buttonOptions['delete'], 'delete', array(), array('class' => 'warning button small'))->toHtml();
		}?>
		<img class="loader" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
	</div>
</div>
