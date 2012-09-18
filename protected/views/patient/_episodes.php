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
?>
<div id="box_gradient_top"></div>
<div id="box_gradient_bottom">
<div style="height: 20px; float: left;"></div>
<div id="add_episode">
	<img src="<?php echo Yii::app()->createUrl('images/add_event_button.png')?>" alt="Add an event to this episode" />
	<ul id="episode_types">
<?php
	foreach ($eventTypeGroups as $group => $eventTypes) { ?>
		<li class="header"><?php echo $group; ?></li>
<?php	foreach ($eventTypes as $type) {
			$name = ucfirst($type->name); ?>
		<li><a href="<?php
			echo Yii::app()->createUrl(
				'clinical/create',
				array(
					'event_type_id' => $type->id,
					'patient_id' => $model->id,
					'firm_id' => $firm->id
				)
			)
		?>"><img src="<?php echo Yii::app()->createUrl('images/'.$type->name.'.gif')?>" alt="<?php echo $name; ?>" />
			<span><?php echo $name; ?></span>
		</a></li>
<?php
		}
	} ?>
	</ul>
</div>
<div class="clear"></div>
<div id="episodes_sidebar">
<?php
	$this->renderPartial('/clinical/_episodeList',
		array('episodes' => $episodes)
	); ?>
</div>
<div id="episodes_details"><?php
	if ($event === false) {
		$episode = end($episodes);

		$editable = false;
		// View the open episode for this firm's subspecialty, if any
		foreach ($episodes as $ep) {
			if ($ep->firm->serviceSubspecialtyAssignment->subspecialty_id == $firm->serviceSubspecialtyAssignment->subspecialty_id) {
				$episode = $ep;
				$editable = true;
			}
		}

		$this->renderPartial('/clinical/episodeSummary',
			array('episode' => $episode, 'editable' => $editable)
		);
	} ?></div>
</div>
<script type="text/javascript">
	$(function() {
		if ($('#episodes_details').text() == '') {
			var link = $('a[href="<?php echo Yii::app()->createUrl('clinical/view', array('id'=>$event)); ?>"]');
			$.ajax({
				url: '<?php echo Yii::app()->createUrl('clinical/view', array('id'=>$event)); ?>',
				success: function(data) {
					link.parent().addClass('shown');
					$('#episodes_details').show();
					$('#episodes_details').html(data);
				}
			});
		}
	});
	$('#add_episode').click(function() {
		if ($('#episode_types').is(':visible')) {
			$('#episode_types').hide();
		} else {
			$('#episode_types').slideDown({'duration':75});
		}
	});
	$('#add_episode li a').click(function() {
		$('ul.events li.shown').removeClass('shown');
		$.ajax({
			url: $(this).attr('href'),
			type: 'GET',
			success: function(data) {
				$('#episodes_details').show();
				$('#episodes_details').html(data);
			}
		});
		if ($('#episode_types').is(':visible')) {
			$('#episode_types').hide();
		}
		return false;
	});
	$('#episode_types li a').click(function() {
		$('ul.events li.shown').removeClass('shown');
	});
	$(this).undelegate('ul.events li a','click').delegate('ul.events li a','click',function() {
		$('ul.events li.shown').removeClass('shown');
		$(this).parent().addClass('shown');
		$.ajax({
			url: $(this).attr('href'),
			success: function(data) {
				$('#episodes_details').show();
				$('#episodes_details').html(data);
			}
		});
		return false;
	});
	$(this).undelegate('.episode div.title','click').delegate('.episode div.title','click',function() {
		var id = $(this).children('input').val();
		$('ul.events li.shown').removeClass('shown');
		$.ajax({
			url: '<?php echo Yii::app()->createUrl('clinical/episodeSummary'); ?>',
			type: 'GET',
			data: {'id': id},
			success: function(data) {
				$('#episodes_details').show();
				$('#episodes_details').html(data);
			}
		});
		return false;
	});
</script>
