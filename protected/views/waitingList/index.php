		<h2>Partial bookings waiting List</h2>

		<div class="fullWidth fullBox clearfix">
			<div id="whiteBox">
				<p><strong>Use the filters below to find patients:</strong></p>
			</div>

		<div id="waitinglist_display">
			<form method="post" action="/waitingList/search" id="waitingList-filter">
				<div id="search-options">

					<div id="main-search" class="grid-view">
						<h3>Search partial bookings waiting lists by:</h3>
					        <table>
					        <tbody>
					        	<tr>
					                <th>Service:</th>
					                <th>Firm:</th>

					                <th>Type:</th>

					        	</tr>
					        	<tr  class="even">
					                <td>
					                	<?php
										        echo CHtml::dropDownList('specialty-id', '', Specialty::model()->getList(),
										                array('empty'=>'All specialties', 'ajax'=>array(
										                        'type'=>'POST',
										                        'data'=>array('specialty_id'=>'js:this.value'),
										                        'url'=>Yii::app()->createUrl('waitingList/filterFirms'),
										                        'success'=>"js:function(data) {
										                                if ($('#specialty-id').val() != '') {
										                                        $('#firm-id').attr('disabled', false);
										                                        $('#firm-id').html(data);
										                                } else {
										                                        $('#firm-id').attr('disabled', true);
										                                        $('#firm-id').html(data);
										                                }
										                        }",
                					))); ?>
													<br/>
													<div style="height: 0.8em;"></div>
													Hospital no: <input type="text" size="12" name="hos_num" value="<?=@$_REQUEST['patient_no']?>" />
									</td>
					                <td>

					                	<?php
										        echo CHtml::dropDownList('firm-id', '', array(),
                									array('empty'=>'All firms', 'disabled'=>(empty($firmId))));
                						?>
					                </td>
					                <td>
					                	<?php
									        echo CHtml::dropDownList('status', '', ElementOperation::getLetterOptions())
									    ?>
									</td>
									<td>
										<button type="submit" class="classy green tall" style="float: right;"><span class="button-span button-span-green">Search</span></button>
									</td>
								</tr>
								</tbody>
							</table>
					        </div> <!-- #main-search -->
					        <!-- extra search currently just used as padding but could be used like Theatre Management for extra filtering -->
					        <div id="extra-search" class="eventDetail clearfix">
					        	<h5>Search Results:</h5>

								<!--<div class="data">
									no extra search filters
								</div>-->
							</div> <!-- #extra-search -->
					</form>
				</div> <!-- #search-options -->

				<div id="searchResults" class="whiteBox">
				</div> <!-- #searchResults -->
			</div> <!-- #waitinglist_display -->
		</div> <!-- .fullWidth -->
<script type="text/javascript">
        $('#waitingList-filter button[type="submit"]').click(function() {
                $.ajax({
                        'url': '<?php echo Yii::app()->createUrl('waitingList/search'); ?>',
                        'type': 'POST',
                        'data': $('#waitingList-filter').serialize(),
                        'success': function(data) {
                                $('#searchResults').html(data);
                                return false;
                        }
                });
                return false;
        });
</script>
