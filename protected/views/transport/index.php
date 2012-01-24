		<h2>Transport</h2>

		<div class="fullWidth fullBox clearfix">
		<div id="waitinglist_display">
			<h3>Patients booked, cancelled and rescheduled on <span id="current_date"><?php echo date('j M Y')?></span></h3>

				<div id="tciControls">
					<a id="tci_previous" href="#">Previous day</a> - 
					<a id="tci_next" href="#">Next day</a>
				</div>

				<div id="searchResults" class="whiteBox">
					<?php echo $this->renderPartial('/transport/_list',array('bookings' => $bookings))?>
				</div> <!-- #searchResults -->
				<button type="submit" class="classy blue grande" style="float: right;"><span class="button-span button-span-blue">Print</span></button>
				<button type="submit" class="classy blue grande" style="margin-right: 20px; float: right;"><span class="button-span button-span-blue">Confirm</span></button>
				<div>
					<?php
					$times = Yii::app()->params['transport_csv_intervals'];
					?>
					<span id="digest_title"<?php if (time() < strtotime(date('Y-m-d ').$times[0])) {?> style="display: none;"<?php }?>>Activity digests:</span>
					<?php foreach ($times as $i => $time) {?>
						<a rel="<?php echo $time?>" <?php if (time() < strtotime(date('Y-m-d ').$time)) {?> style="display: none;"<?php }?> href="/transport/digest/<?php echo date('Ymd')."_".str_replace(':','',$time).".csv"?>">
							<?php echo $time?>&nbsp;
						</a>
					<?}?>
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
</script>
