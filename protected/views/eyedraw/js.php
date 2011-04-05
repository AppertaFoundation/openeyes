<script type="text/javascript">
	// This set is written by PHP and contains doodle data for this drawing from the database
	var doodleSetDefault =
		[
			{subclass: "Fundus", originX: "0", originY: "0", apexX: "0", apexY: "0", scaleX: "1", scaleY: "1", arc: "0", rotation: "0", order: "0"},
			{subclass: "RRD", originX: "0", originY: "0", apexX: "0", apexY: "105", scaleX: "1", scaleY: "1", arc: "160", rotation: "330", order: "1"},
			{subclass: "UTear", originX: "-212", originY: "-320", apexX: "0", apexY: "-22", scaleX: "1", scaleY: "1", arc: "0", rotation: "327", order: "2"}
		];
		<?$property = 'image_string_'.$side; if ($side and $model->$property) {?>
			var doodleSet<?=get_class($model)?>_<?= $side?>= <?=$model->$property?>;
		<?} else {?>
			var doodleSet<?=get_class($model)?>_<?= $side?>=doodleSetDefault;
		<?}?>

	// Variables assigned to each drawing on this page
	var drawing<?=get_class($model)?>_<?= $side?>;

	function report<?=get_class($model)?>_<?= $side?>()
	{
		report = drawing<?=get_class($model)?>_<?= $side?>.report();
		report = report.replace(/^\s+/, '');
		// ignore the empty selector
		if ('' != report) {
			// grab the text
			appendValue = document.getElementById('<?=get_class($model)?>_description_<?= $side?>').value;
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
			document.getElementById('<?=get_class($model)?>_description_<?= $side?>').value=report;
		}
		return false;
	}	
	function submit<?=get_class($model)?>_<?= $side?>()
	{
		
		document.getElementById('<?=get_class($model)?>_image_string_<?= $side?>').value=drawing<?=get_class($model)?>_<?= $side?>.jsonString();
	}

	function init<?=get_class($model)?>_<?= $side?>()
	{
		// initialise doodlesets
		var canvas<?=get_class($model)?>_<?= $side?> = document.getElementById('canvas<?=get_class($model)?>_<?=$side?>');

		// Create blank posterior segment drawing
		drawing<?=get_class($model)?>_<?= $side?> = new ED.Drawing(canvas<?=get_class($model)?>_<?= $side?>, Eye.<?= ucfirst($side)?>, '<?=get_class($model)?>_<?= $side?>');

		<?if ($writeable) {?>
			// Stop browser stealing double click to select text (TODO Test this in browsers other than Safari)
			canvas<?=get_class($model)?>_<?= $side?>.onselectstart = function () { return false; }
			// Event listeners
			canvas<?=get_class($model)?>_<?= $side?>.addEventListener('mousedown', function(e) {
				var point = new ED.Point(e.pageX - canvas<?=get_class($model)?>_<?= $side?>.offsetLeft, e.pageY - canvas<?=get_class($model)?>_<?= $side?>.offsetTop);
				drawing<?=get_class($model)?>_<?= $side?>.mousedown(point);
			}, false);

			canvas<?=get_class($model)?>_<?= $side?>.addEventListener('mouseup', function(e) {
				var point = new ED.Point(e.pageX - canvas<?=get_class($model)?>_<?= $side?>.offsetLeft, e.pageY - canvas<?=get_class($model)?>_<?= $side?>.offsetTop);
				drawing<?=get_class($model)?>_<?= $side?>.mouseup(point);
			}, false);

			canvas<?=get_class($model)?>_<?= $side?>.addEventListener('mousemove', function(e) {
				var point = new ED.Point(e.pageX - canvas<?=get_class($model)?>_<?= $side?>.offsetLeft, e.pageY - canvas<?=get_class($model)?>_<?= $side?>.offsetTop);
				drawing<?=get_class($model)?>_<?= $side?>.mousemove(point);
			}, false);

			//canvasRPS.addEventListener('keydown',keyDownRPS,true);
			canvas<?=get_class($model)?>_<?= $side?>.focus();
		<?}?>

		// Load doodleSet
		drawing<?=get_class($model)?>_<?= $side?>.load(doodleSet<?=get_class($model)?>_<?= $side?>);

		<?if ($writeable) {?>
		// Use fundus as template (for new drawings)
		drawing<?=get_class($model)?>_<?= $side?>.addDoodle('Fundus');
		<?}?>

		// Draw doodles
		drawing<?=get_class($model)?>_<?= $side?>.drawAllDoodles();
	}

	<?if ($writeable) {?>
	// Mousedown handler for each drawing canvas
	function mousedown<?=get_class($model)?>_<?= $side?>(e)
	{
		var point = new ED.Point(e.pageX - this.offsetLeft, e.pageY - this.offsetTop);
		drawing<?=get_class($model)?>_<?= $side?>.mousedown(point);
	}

	// Mouseup handler for each drawing canvas
	function mouseup<?=get_class($model)?>_<?= $side?>(e)
	{
		var point = new ED.Point(e.pageX - this.offsetLeft, e.pageY - this.offsetTop);
		drawing<?=get_class($model)?>_<?= $side?>.mouseup(point);
	}

	// Mousedown handler for each drawing canvas
	function mousemove<?=get_class($model)?>_<?= $side?>(e)
	{
		var point = new ED.Point(e.pageX - this.offsetLeft, e.pageY - this.offsetTop);
		drawing<?=get_class($model)?>_<?= $side?>.mousemove(point);
	}

	// Key press handler
	function keyDown<?=get_class($model)?>_<?= $side?>(event)
	{
		alert(event.keyCode);

		// Prevent key stroke bubbling up (***TODO*** may need cross browser handling)
		event.stopPropagation();
		event.preventDefault();
	}
	<?}?>

	// Returns true if browser is firefox
	function isFirefox()
	{
		var index = 0;
		var ua = window.navigator.userAgent;
		index = ua.indexOf("Firefox");

		if (index > 0)
		{
			return true;
		}
			else
		{
			return false;
		}
	}
</script>
