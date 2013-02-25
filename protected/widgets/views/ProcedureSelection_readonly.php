<div class="eventDetail<?php if ($last) {?> eventDetailLast<?php }?>" id="typeProcedure"<?php if ($hidden) {?> style="display: none;"<?php }?>>
	<div class="label"><?php echo $label?>:</div>
	<div class="data split limitWidth">
		<div class="left">
			<?php
			$totalDuration = 0;
			?>
			<div id="procedureList_<?php echo $identifier?>" class="eventHighlight big" style="width:auto; line-height:1.6;<?php if (empty($selected_procedures)){?> display: none;<?php }?>">
				<h4>
					<?php
					if (!empty($selected_procedures)) {
						foreach ($selected_procedures as $procedure) {?>
							<div class="procedureItem">
								<span class="middle<?php echo (!$durations) ? " noDuration" : ""; ?>">
									<?php
										$totalDuration += $procedure['default_duration'];
										echo CHtml::hiddenField('Procedures_'.$identifier.'[]', $procedure['id']);
										echo "<span>".$procedure['term'];
										if ($short_version) {
											echo '</span> - <span>'.$procedure['short_format'];
										}
										echo "</span>";
									?>
								</span>
								<?php if ($durations) {?>
									<span class="right">
										<?php echo $procedure['default_duration']?> mins
									</span>
								<?php } ?>
							</div>
						<?php	}
					}
					?>
				</h4>
				<div class="extraDetails grid-view"<?php if (empty($selected_procedures) || !$durations){?> style="display: none;"<?php }?>>
					<table class="grid">
						<tfoot>
							<tr>
								<th>Calculated Total Duration:</th>
								<th id="projected_duration_<?php echo $identifier?>"><?php echo $totalDuration?> mins</th>
								<th>Estimated Total Duration:</th>
								<th><input type="text" value="<?php echo $total_duration?>" id="<?php echo $class?>_total_duration_<?php echo $identifier?>" name="<?php echo $class?>[total_duration_<?php echo $identifier?>]" style="width: 60px;"></th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
