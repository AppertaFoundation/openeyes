<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */


Yii::app()->clientScript->registerCssFile(Yii::app()->createUrl('css/elements/ElementLetterOut/1.css'), 'screen, projection');
Yii::app()->clientScript->registerCssFile(Yii::app()->createUrl('css/elements/ElementLetterOut/1_print.css'), 'print');
?>
<div id="ElementLetterOut_layout">
<img src="<?php echo Yii::app()->createUrl('img/elements/ElementLetterOut/Letterhead.png')?>" alt="Moorfields logo" border="0" />
<?php

if ($siteId = Yii::app()->request->cookies['site_id']->value) {
	$site = Site::model()->findByPk($siteId);

	if (isset($site)) {?>
		<div class="ElementLetterOut_siteDetails">
			<?php
			echo $site->name . "<br />\n";
			echo $site->address1 . "<br />\n";
			if (isset($site->address2)) {
				echo $site->address2 . "<br />\n";
			}
			if (isset($site->address3)) {
				echo $site->address3 . "<br />\n";
			}
			echo $site->postcode . "<br />\n";
			echo "<br />\n";
			echo "Tel: " . $site->telephone . "<br />\n";
			echo "Fax: " . $site->fax . "<br />\n";
			?>
		</div>
	<?php }
}
?>
	<br /><br /><br /><br /><br /><br />
	<p class="ElementLetterOut_to"><?php echo nl2br(CHtml::encode($data->to_address)); ?></p>
	<p class="ElementLetterOut_date"><?php echo CHtml::encode($data->date); ?></p>
	<p class="ElementLetterOut_dear"><?php echo CHtml::encode($data->dear); ?></p>
	<p class="ElementLetterOut_re"><?php echo CHtml::encode($data->re); ?></p>
	<p class="ElementLetterOut_text"><?php echo nl2br(CHtml::encode($data->value)); ?></p>
	<p><?php echo nl2br(CHtml::encode($data->from_address)) ?></p>
	<p class="ElementLetterOut_cc"><?php echo nl2br(CHtml::encode($data->cc)); ?></p>
</div>
