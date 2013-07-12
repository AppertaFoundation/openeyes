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

?><script type="text/javascript">
	// This set is written by PHP and contains doodle data for this drawing from the database
	var doodleSetDefault =
		[
			{subclass: "Fundus", originX: "0", originY: "0", apexX: "0", apexY: "0", scaleX: "1", scaleY: "1", arc: "0", rotation: "0", order: "0"},
		];
		<?php
			$property = 'image_string_'.$side;

			if ($side and $model->$property) {
		?>
			var doodleSet<?php echo get_class($model)?>_<?php echo $side?>= <?php echo $model->$property?>;
		<?php
			} else {
		?>
			var doodleSet<?php echo get_class($model)?>_<?php echo $side?>=doodleSetDefault;
		<?php } ?>

	// Variables assigned to each drawing on this page
	var drawing<?php echo get_class($model)?>_<?php echo $side?>;

	function report<?php echo get_class($model)?>_<?php echo $side?>()
	{
		report = drawing<?php echo get_class($model)?>_<?php echo $side?>.report();
		report = report.replace(/^\s+/, '');
		// ignore the empty selector
		if ('' != report) {
			// grab the text
			appendValue = document.getElementById('<?php echo get_class($model)?>_description_<?php echo $side?>').value;
			// if we're adding onto existing text, add a comma
			if (appendValue && '' != appendValue) {
				report = report.charAt(0).toUpperCase() + report.slice(1);
				var appendme = appendValue; appendValue = appendme.charAt(0).toUpperCase() + appendme.slice(1);
				report = report + ', ' + appendValue;
			// otherwise just make sure the first letter is capitalized
			} else {
				report = report.charAt(0).toUpperCase() + report.slice(1);
			}
			// add it to the textarea
			document.getElementById('<?php echo get_class($model)?>_description_<?php echo $side?>').value=report;
		}
		return false;
	}
	function submit<?php echo get_class($model)?>_<?php echo $side?>()
	{

		document.getElementById('<?php echo get_class($model)?>_image_string_<?php echo $side?>').value=drawing<?php echo get_class($model)?>_<?php echo $side?>.jsonString();
	}

	function init<?php echo get_class($model)?>_<?php echo $side?>()
	{
		// initialise doodlesets
		var canvas<?php echo get_class($model)?>_<?php echo $side?> = document.getElementById('canvas<?php echo get_class($model)?>_<?php echo $side?>');

		// Create blank posterior segment drawing
		drawing<?php echo get_class($model)?>_<?php echo $side?> = new ED.Drawing(canvas<?php echo get_class($model)?>_<?php echo $side?>, Eye.<?php echo  ucfirst($side)?>, '<?php echo get_class($model)?>_<?php echo $side?>');

		<?php
			if ($writeable) {
		?>
			// Stop browser stealing double click to select text (TODO Test this in browsers other than Safari)
			canvas<?php echo get_class($model)?>_<?php echo $side?>.onselectstart = function () { return false; }
			// Event listeners
			canvas<?php echo get_class($model)?>_<?php echo $side?>.addEventListener('mousedown', function(e) {
				var point = new ED.Point(e.pageX - canvas<?php echo get_class($model)?>_<?php echo $side?>.offsetLeft, e.pageY - canvas<?php echo get_class($model)?>_<?php echo $side?>.offsetTop);
				drawing<?php echo get_class($model)?>_<?php echo $side?>.mousedown(point);
			}, false);

			canvas<?php echo get_class($model)?>_<?php echo $side?>.addEventListener('mouseup', function(e) {
				var point = new ED.Point(e.pageX - canvas<?php echo get_class($model)?>_<?php echo $side?>.offsetLeft, e.pageY - canvas<?php echo get_class($model)?>_<?php echo $side?>.offsetTop);
				drawing<?php echo get_class($model)?>_<?php echo $side?>.mouseup(point);
			}, false);

			canvas<?php echo get_class($model)?>_<?php echo $side?>.addEventListener('mousemove', function(e) {
				var point = new ED.Point(e.pageX - canvas<?php echo get_class($model)?>_<?php echo $side?>.offsetLeft, e.pageY - canvas<?php echo get_class($model)?>_<?php echo $side?>.offsetTop);
				drawing<?php echo get_class($model)?>_<?php echo $side?>.mousemove(point);
			}, false);

			//canvasRPS.addEventListener('keydown',keyDownRPS,true);
			canvas<?php echo get_class($model)?>_<?php echo $side?>.focus();
		<?php } ?>

		// Load doodleSet
		drawing<?php echo get_class($model)?>_<?php echo $side?>.load(doodleSet<?php echo get_class($model)?>_<?php echo $side?>);

		<?php
			if ($writeable) {
		?>
		// Use fundus as template (for new drawings)
		drawing<?php echo get_class($model)?>_<?php echo $side?>.addDoodle('Fundus');
		<?php } ?>

		// Draw doodles
		drawing<?php echo get_class($model)?>_<?php echo $side?>.drawAllDoodles();
	}

	<?php
		if ($writeable) {
	?>
	// Mousedown handler for each drawing canvas
	function mousedown<?php echo get_class($model)?>_<?php echo $side?>(e)
	{
		var point = new ED.Point(e.pageX - this.offsetLeft, e.pageY - this.offsetTop);
		drawing<?php echo get_class($model)?>_<?php echo $side?>.mousedown(point);
	}

	// Mouseup handler for each drawing canvas
	function mouseup<?php echo get_class($model)?>_<?php echo $side?>(e)
	{
		var point = new ED.Point(e.pageX - this.offsetLeft, e.pageY - this.offsetTop);
		drawing<?php echo get_class($model)?>_<?php echo $side?>.mouseup(point);
	}

	// Mousedown handler for each drawing canvas
	function mousemove<?php echo get_class($model)?>_<?php echo $side?>(e)
	{
		var point = new ED.Point(e.pageX - this.offsetLeft, e.pageY - this.offsetTop);
		drawing<?php echo get_class($model)?>_<?php echo $side?>.mousemove(point);
	}

	// Key press handler
	function keyDown<?php echo get_class($model)?>_<?php echo $side?>(event)
	{
		alert(event.keyCode);

		// Prevent key stroke bubbling up (***TODO*** may need cross browser handling)
		event.stopPropagation();
		event.preventDefault();
	}
	<?php } ?>

	// Returns true if browser is firefox
	function isFirefox()
	{
		var index = 0;
		var ua = window.navigator.userAgent;
		index = ua.indexOf("Firefox");

		if (index > 0) {
			return true;
		} else {
			return false;
		}
	}
</script>
