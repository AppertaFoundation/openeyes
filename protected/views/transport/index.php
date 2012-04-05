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

				<div id="searchResults" class="whiteBox">
					<?php echo $this->renderPartial('/transport/_list',array('bookings' => $bookings))?>
				</div> <!-- #searchResults -->
				<!-- Disabled until form finished 
				<button type="submit" class="classy blue grande" style="float: right;" id="btn_print"><span class="button-span button-span-blue">Print</span></button>
				 -->
				<button type="submit" class="classy blue grande" style="margin-right: 20px; float: right;" id="btn_confirm"><span class="button-span button-span-blue">Confirm</span></button>
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

	$('#btn_confirm').click(function() {
		$.ajax({
			type: "POST",
			url: "/transport/confirm",
			data: $('input[name^="cancelled"]:checked').serialize()+"&"+$('input[name^="booked"]:checked').serialize(),
			success: function(html) {
				update_tcis();
				return false;
			}
		});

		return false;
	});

	$('#btn_print').click(function() {
		var booked = $('input[name^="booked"]:checked').map(function(i,n) {
			return $(n).val();
		}).get();
		if (booked.length == 0) {
			alert("No items selected for printing.");
		} else {
			printUrl('/transport/print', {'booked': booked});
		}
		return false;
	});
</script>
