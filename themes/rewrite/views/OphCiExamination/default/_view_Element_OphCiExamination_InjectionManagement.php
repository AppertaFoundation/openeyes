	<div class="left">
		<table class="subtleWhite normalText">
			<tbody>
				<tr>
					<td width="30%"><?php echo CHtml::encode($element->getAttributeLabel('injection_status_id'))?></td>
					<td><span class="big"><?php echo $element->injection_status ?></span></td>
				</tr>
				<?php if ($element->injection_status && $element->injection_status->deferred) { ?>
					<tr>
						<td width="30%"><?php echo CHtml::encode($element->getAttributeLabel('injection_deferralreason_id'))?></td>
						<td><span class="big"><?php echo $element->getInjectionDeferralReason() ?></span></td>
					</tr>
				<?php }?>
			</tbody>
		</table>
	</div>
