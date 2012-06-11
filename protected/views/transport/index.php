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
		<h2>Transport</h2>

		<div class="fullWidth fullBox clearfix">
		<div id="waitinglist_display">
			<h3>TCIs for today onwards.</h3>
			<?php /*<h3>Patients booked, cancelled and rescheduled on <span id="current_date"><?php echo date('j M Y')?></span></h3>*/ ?>

				<?php /*<div id="tciControls">
					<a id="tci_previous" href="#">Previous day</a> - 
					<a id="tci_next" href="#">Next day</a>
				</div>*/ ?>
				<button type="submit" class="classy blue venti btn_download" style="margin-right: 10px; margin-top: 20px; margin-bottom: 20px; float: right;"><span class="button-span button-span-blue">Download CSV</span></button>
				<button type="submit" class="classy blue tall btn_print" style="margin-right: 10px; margin-top: 20px; margin-bottom: 20px; float: right;"><span class="button-span button-span-blue">Print list</span></button>
				<button type="submit" class="classy blue tall btn_confirm" style="margin-right: 10px; margin-top: 20px; margin-bottom: 20px; float: right;"><span class="button-span button-span-blue">Confirm</span></button>

				<div id="searchResults" class="whiteBox">
					<form id="transport_form" method="post" action="/transport">
						<label for="date_from">
							From:
						</label>
						<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
							'name' => 'date_from',
							'id' => 'date_from',
							'options' => array(
								'showAnim'=>'fold',
								'dateFormat'=>Helper::NHS_DATE_FORMAT_JS
							),
							'value' => @$_REQUEST['date_from'],
							'htmlOptions' => array('style' => "width: 95px;"),
						))?>
						<label for="date_to">
							To:
						</label>
						<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
							'name' => 'date_to',
							'id' => 'date_to',
							'options' => array(
								'showAnim'=>'fold',
								'dateFormat'=>Helper::NHS_DATE_FORMAT_JS
							),
							'value' => @$_REQUEST['date_to'],
							'htmlOptions' => array('style' => "width: 95px;"),
						))?>
						<button type="submit" class="classy blue mini btn_filter auto"><span class="button-span button-span-blue">Filter</span></button>
						<button type="submit" class="classy blue mini btn_viewall"><span class="button-span button-span-blue">View all</span></button>
						<img src="/img/ajax-loader.gif" id="loader" style="display: none;" />
						<div style="height: 0.4em;"></div>
						<label>
							Include:
						</label>
						&nbsp;
						<input type="checkbox" name="include_bookings" class="filter" value="1"<?php if (@$_REQUEST['include_bookings']){?> checked="checked"<?php }?> /> Bookings
						<input type="checkbox" name="include_reschedules" class="filter" value="1"<?php if (@$_REQUEST['include_reschedules']){?> checked="checked"<?php }?> /> Reschedules
						<input type="checkbox" name="include_cancellations" class="filter" value="1"<?php if (@$_REQUEST['include_cancellations']){?> checked="checked"<?php }?> /> Cancellations
					</form>
					<form id="csvform" method="post" action="/transport/downloadcsv">
						<input type="hidden" name="date_from" value="<?php echo @$_REQUEST['date_from']?>" />
						<input type="hidden" name="date_to" value="<?php echo @$_REQUEST['date_to']?>" />
						<input type="hidden" name="include_bookings" value="<?php echo (@$_REQUEST['include_bookings'] ? 1 : 0)?>" />
						<input type="hidden" name="include_reschedules" value="<?php echo (@$_REQUEST['include_reschedules'] ? 1 : 0)?>" />
						<input type="hidden" name="include_cancellations" value="<?php echo (@$_REQUEST['include_cancellations'] ? 1 : 0)?>" />
					</form>
					<?php echo $this->renderPartial('/transport/_pagination')?>
					<?php echo $this->renderPartial('/transport/_list',array('bookings' => $bookings))?>
					<?php echo $this->renderPartial('/transport/_pagination')?>
				</div> <!-- #searchResults -->
				<button type="submit" class="classy blue venti btn_download" style="margin-right: 10px; margin-top: 20px; margin-bottom: 20px; float: right;"><span class="button-span button-span-blue">Download CSV</span></button>
				<button type="submit" class="classy blue tall btn_print" style="margin-right: 10px; margin-top: 20px; margin-bottom: 20px; float: right;"><span class="button-span button-span-blue">Print list</span></button>
				<button type="submit" class="classy blue tall btn_confirm" style="margin-right: 10px; margin-top: 20px; margin-bottom: 20px; float: right;"><span class="button-span button-span-blue">Confirm</span></button>
				<div>
					<?php
					$times = Yii::app()->params['transport_csv_intervals'];
					if ($times && is_array($times)) {
						?>
						<span id="digest_title"<?php if (time() < strtotime(date('Y-m-d ').$times[0])) {?> style="display: none;"<?php }?>>Activity digests:</span>
						<?php foreach ($times as $i => $time) {?>
							<a rel="<?php echo $time?>" <?php if (time() < strtotime(date('Y-m-d ').$time)) {?> style="display: none;"<?php }?> href="/transport/digest/<?php echo date('Ymd')."_".str_replace(':','',$time).".csv"?>">
								<?php echo $time?>&nbsp;
							</a>
						<?php }?>
					<?php }?>
				</div>
			</div> <!-- #waitinglist_display -->
		</div> <!-- .fullWidth -->
<script type="text/javascript">
	var tci_date = new Date;

	$('#tci_previous').click(function() {
		tci_date = new Date(tci_date.getTime() - (86400 * 1000));
		update_tcis();
		return false;
	});

	function update_tcis() {
		$.ajax({
			type: "POST",
			url: "/transport/list",
			data: "date="+tci_date.toDateString(),
			success: function(html) {
				$('#current_date').html(tci_date.getDate()+' '+tci_date.toDateString().replace(/^[a-zA-Z]{3} /,'').replace(/ [0-9]+ [0-9]+$/,'')+' '+tci_date.getFullYear());
				$('#searchResults').html(html);

				var show_digest = false;

				$('a[href^="/transport/digest"]').map(function() {
					var hour = $(this).attr('rel').replace(/:[0-9]+$/,'');
					var min = $(this).attr('rel').replace(/^[0-9]+:/,'');

					var now = new Date;
					var limit = new Date(tci_date.getFullYear(), tci_date.getMonth(), tci_date.getDate(), hour, min, 0, 0);

					if (now.getTime() >= limit.getTime()) {
						$(this).show();
						show_digest = true;
					} else {
						$(this).hide();
					}
				});

				if (show_digest) {
					$('#digest_title').show();
				} else {
					$('#digest_title').hide();
				}
			}
		});
	}

	$('#tci_next').click(function() {
		tci_date = new Date(tci_date.getTime() + (86400 * 1000));
		update_tcis();	
		return false;
	});

	$('button.btn_confirm').click(function() {
		if (!$(this).hasClass('inactive')) {
			disableButtons();

			$.ajax({
				type: "POST",
				url: "/transport/confirm",
				data: $('input[name^="cancelled"]:checked').serialize()+"&"+$('input[name^="booked"]:checked').serialize(),
				success: function(html) {
					if (html == "1") {
						$('input:checked').map(function() {
							if ($(this).attr('class') != 'filter') {
								$(this).parent().parent().attr('class','waitinglistGrey');
								$(this).attr('checked',false);
							}
						});
					} else {
						alert("Something went wrong trying to confirm the transport item.\n\nPlease try again or contact OpenEyes support.");
					}
					enableButtons();
					return false;
				}
			});
		}

		return false;
	});

	$('button.btn_print').click(function() {
		if (!$(this).hasClass('inactive')) {
			disableButtons();
			printContent(null);
			enableButtons();
		}
		return false;
	});

	$('button.btn_viewall').click(function() {
		if (!$(this).hasClass('inactive')) {
			disableButtons();
			$('#loader').show();
			window.location.href = '/transport';
		}
		return false;
	});

	$('button.btn_filter').click(function() {
		if (!$(this).hasClass('inactive')) {
			disableButtons();
			$('#loader').show();
			return true;
		}
		return false;
	});
	
	$('button.btn_download').click(function() {
		if (!$(this).hasClass('inactive')) {
			$('#csvform').submit();
		}
	});

	$('#date_from').bind('change',function() {
		$('#date_to').datepicker('option','minDate',$('#date_from').datepicker('getDate'));
	});

	$('#date_to').bind('change',function() {
		$('#date_from').datepicker('option','maxDate',$('#date_to').datepicker('getDate'));
	});
</script>
