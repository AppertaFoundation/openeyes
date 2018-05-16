<?php
/**
 * @var UserHotlistItem $hotlistItem
 */

$core_api = new CoreAPI();
?>

<tr class="js-hotlist-<?= $hotlistItem->is_open ? 'open' : 'closed' ?>-patient"
    data-id="<?= $hotlistItem->id ?>"
    data-patient-href="<?= $core_api->generateEpisodeLink($hotlistItem->patient) ?>"
    style="white-space: nowrap;">
  <td><?= $hotlistItem->patient->hos_num ?></td>
  <td style="overflow: hidden;">
    <a href="<?= $core_api->generateEpisodeLink($hotlistItem->patient) ?>">
        <?= $hotlistItem->patient->getHSCICName() ?>
    </a>
  </td>
  <td style="overflow: hidden">
    <a class="js-hotlist-comment-readonly js-add-hotlist-comment">
        <?= substr($hotlistItem->user_comment, 0, 30) .
        (strlen($hotlistItem->user_comment) > 30 ? '...' : '') ?></a>
  </td>
  <td>
    <i class="oe-i <?= $hotlistItem->user_comment ? 'comments-added active' : 'comments' ?> medium pro-theme js-add-hotlist-comment"></i>
      <?php if ($hotlistItem->is_open): ?>
        <i class="oe-i remove-circle medium pro-theme pad js-close-hotlist-item"></i>
      <?php else: ?>
        <i class="oe-i plus-circle medium pro-theme pad js-open-hotlist-item"></i>
      <?php endif; ?>
  </td>
</tr>
<tr class="hotlist-comment js-hotlist-comment"
    data-id="<?= $hotlistItem->id ?>"
    style="display: none">
  <td colspan="4">
      <?= CHtml::activeTextArea($hotlistItem, 'user_comment',
          array('placeholder' => 'Comments', 'class' => 'cols-full autosize')) ?>
  </td>
</tr>
