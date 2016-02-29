<div class="row data-row">
	<div class="large-4 column">
		<div class="data-label">Priority:
		</div>
	</div>
	<div class="large-8 column">
		<div class="data-value" style="color: <?= $ticket->priority->colour?>">
			<?= $ticket->priority->name ?>
		</div>
	</div>
</div>
<div class="row data-row">
	<div class="large-4 column">
		<div class="data-label">Current Queue:
		</div>
	</div>
	<div class="large-8 column">
		<div class="data-value">
			<?php echo $ticket->currentQueue->name ?>
		</div>
	</div>
</div>
