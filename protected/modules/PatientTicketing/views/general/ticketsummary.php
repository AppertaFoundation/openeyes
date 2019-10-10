<div class="data-group">
    <div class="cols-4 column">
        <div class="data-label">Priority:
        </div>
    </div>
    <div class="cols-8 column">
        <div class="data-value" style="color: <?= $ticket->priority->colour?>">
            <?= $ticket->priority->name ?>
        </div>
    </div>
</div>
<div class="data-group">
    <div class="cols-4 column">
        <div class="data-label">Current Queue:
        </div>
    </div>
    <div class="cols-8 column">
        <div class="data-value">
            <?php echo $ticket->currentQueue->name ?>
        </div>
    </div>
</div>
