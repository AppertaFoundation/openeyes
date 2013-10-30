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
<?php if ($isEditable) {?>
	<div class="ed_toolbar">
		<button class="ed_img_button" disabled="disabled" id="moveToFront<?php echo $idSuffix?>" title="Move to front" onclick="<?php echo $drawingName?>.moveToFront(); return false;">
			<img src="<?php echo $imgPath?>moveToFront.gif" />
		</button>
		<button class="ed_img_button" disabled="disabled" id="moveToBack<?php echo $idSuffix?>" title="Move to back" onclick="<?php echo $drawingName?>.moveToBack(); return false;">
			<img src="<?php echo $imgPath?>moveToBack.gif" />
		</button>
		<button class="ed_img_button" disabled="disabled" id="deleteSelectedDoodle<?php echo $idSuffix?>" title="Delete" onclick="<?php echo $drawingName?>.deleteSelectedDoodle(); return false;">
			<img src="<?php echo $imgPath?>deleteSelectedDoodle.gif" />
		</button>
		<button class="ed_img_button" disabled="disabled" id="lock<?php echo $idSuffix?>" title="Lock" onclick="<?php echo $drawingName?>.lock(); return false;">
			<img src="<?php echo $imgPath?>lock.gif" />
		</button>
		<button class="ed_img_button" id="unlock<?php echo $idSuffix?>" title="Unlock" onclick="<?php echo $drawingName?>.unlock(); return false;">
			<img src="<?php echo $imgPath?>unlock.gif" />
		</button>
	</div>
  <?php if ($isEditable && count($doodleToolBarArray) > 0) {
    foreach ($doodleToolBarArray as $row => $rowItems) {?>
			<div class="ed_toolbar">
				<?php foreach ($rowItems as $item) {?>
					<button class="ed_img_button" id="<?php echo $item['classname'].$idSuffix?>" title="<?php echo $item['title']?>" onclick="<?php echo $drawingName?>.addDoodle('<?php echo $item['classname']?>'); return false;">
						<img src="<?php echo $imgPath.$item['classname']?>.gif" />
					</button>
				<?php }?>
			</div>
		<?php }
	}?>
<?php }?>
<!-- Uncomment following line to re-enable doodle hover tooltips once layer bug is fixed (OE-1583) -->
<!-- <span id="canvasTooltip"></span> -->
<canvas id="<?php echo $canvasId?>" class="<?php if ($isEditable) { echo 'edit'; } else { echo 'display'; }?>" width="<?php echo $width?>" height="<?php echo $height?>" tabindex="1"<?php if ($canvasStyle) {?> style="<?php echo $canvasStyle?>"<?php }?>></canvas>
<input type="hidden" id="<?php echo $inputId?>" name="<?php echo $inputName?>" value='<?php echo $this->model[$this->attribute]?>' />
