<?php
/**
 * @var UserHotlistItem $hotlistItem
 */
?>

<tr class="js-activity-<?= $hotlistItem->is_open ? 'open' : 'closed' ?>-patient js-has-tooltip"
    data-id="<?= $hotlistItem->id ?>"
    data-event-id="<?= $hotlistItem->event_id ?>"
    data-event-href="<?php echo Yii::app()->createUrl('/' . $hotlistItem->event->eventType->class_name . '/default/view/' . $hotlistItem->event_id) ?>"
    data-tooltip-content="<div style='text-align: center'>Last modified <?= $hotlistItem->getIntervalString() ?> ago</div>">
  <td><?= $hotlistItem->patient->hos_num ?></td>
  <td><?= $hotlistItem->patient->getHSCICName() ?></td>

  <td>
    <!--<span class="duration green"><?= $hotlistItem->getIntervalString() ?></span>-->
    <span class="duration js-hotlist-comment-readonly"><?= substr($hotlistItem->user_comment, 0,
            20) . (strlen($hotlistItem->user_comment) > 20 ? '...' : '') ?></span>
    <button class="button js-add-hotlist-comment <?= $hotlistItem->user_comment ? 'selected' : '' ?>" type="button">
      <i class="oe-i comments small-icon"></i>
    </button>
    <i class="oe-i-e <?= $hotlistItem->event->eventType->getEventIconCssClass() ?>"></i>
      <?php if ($hotlistItem->is_open): ?>
        <i class="oe-i remove-circle medium pro-theme pad js-close-hotlist-item"></i>
      <?php else: ?>
        <i class="oe-i plus-circle medium pro-theme pad js-open-hotlist-item"></i>
      <?php endif; ?>
  </td>
</tr>
<tr class="js-hotlist-comment"
    data-id="<?= $hotlistItem->id ?>"
    style="display: none">
  <td colspan="3">
      <?= CHtml::activeTextField($hotlistItem, 'user_comment',
          array('placeholder' => 'Comments', 'style' => 'width: 100%;')) ?>
  </td>
</tr>
