<?php
/**
 * @var UserHotlistItem $hotlistItem
 */

$core_api = new CoreAPI();
?>

<tr class="js-activity-<?= $hotlistItem->is_open ? 'open' : 'closed' ?>-patient js-has-tooltip"
    data-id="<?= $hotlistItem->id ?>"
    data-patient-href="<?= $core_api->generateEpisodeLink($hotlistItem->patient) ?>"
    data-tooltip-content="<div style='text-align: center'>Last modified <?= $hotlistItem->getIntervalString() ?> ago</div>"
    style="white-space: nowrap;">
  <td><?= $hotlistItem->patient->hos_num ?></td>
  <td><?= $hotlistItem->patient->getHSCICName() ?></td>

  <td>
      <?php if ($hotlistItem->is_open): ?>
        <span class="duration js-hotlist-comment-readonly js-has-tooltip"
              data-tooltip-content="<?= nl2br($hotlistItem->user_comment) ?>">
            <?= substr($hotlistItem->user_comment, 0,
                30) . (strlen($hotlistItem->user_comment) > 30 ? '...' : '') ?></span>
      <?php endif; ?>
    <i class="oe-i <?= $hotlistItem->user_comment ? 'comments-added' : 'comments' ?> medium pro-theme js-add-hotlist-comment"></i>
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
      <?= CHtml::activeTextArea($hotlistItem, 'user_comment',
          array('placeholder' => 'Comments', 'style' => 'width: 100%;', 'class' => 'autosize')) ?>
  </td>
</tr>
