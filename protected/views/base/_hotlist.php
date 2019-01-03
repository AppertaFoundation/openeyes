<?php
$openHotlistItems = UserHotlistItem::model()->getHotlistItems(1);
$closedHotlistItems = UserHotlistItem::model()->getHotlistItems(0, date('Y-m-d'));

?>

<div class="oe-hotlist-panel" id="js-hotlist-panel">
  <div class="patient-activity">
    <div class="patients-open">
      <div class="overview">
        <h3>Open
          <small class="count"><?= count($openHotlistItems) ?></small>
        </h3>
      </div>

      <!-- Open Items -->
      <table class="activity-list open" style="table-layout: fixed;">
        <colgroup>
          <col style="width: 50px;">
          <col style="width: 40%;">
          <col style="width: 30%">
          <col style="width: 50px;">
        </colgroup>
        <tbody>

        <?php foreach ($openHotlistItems as $hotlistItem): ?>
            <?php echo $this->renderPartial('//base/_hotlist_item', array('hotlistItem' => $hotlistItem)); ?>
        <?php endforeach; ?>
        </tbody>
      </table>

    </div>

    <!-- Closed Items. users can select date to view all patients closed on that date -->
    <div class="patients-closed">

      <div class="overview flex-layout">
        <h3>Closed: <span class="for-date" id="js-pickmeup-closed-date">Today</span>
          <small class="count"><?= count($closedHotlistItems) ?></small>
        </h3>
        <div class="closed-search">
          <span class="closed-date" id="js-hotlist-closed-today">Today</span>
          <span class="closed-date" id="js-hotlist-closed-select">Select date</span>
          <div id="js-pickmeup-datepicker" style="display: none;"></div>
        </div>
      </div>

      <table class="activity-list closed" style="table-layout: fixed;">
        <colgroup>
          <col style="width: 50px;">
          <col style="width: 40%;">
          <col style="width: 30%">
          <col style="width: 50px;">
        </colgroup>
        <tbody>
        <?php foreach ($closedHotlistItems as $hotlistItem): ?>
            <?php echo $this->renderPartial('//base/_hotlist_item', array('hotlistItem' => $hotlistItem)); ?>
        <?php endforeach; ?>
        </tbody>
      </table>

    </div>
  </div>
</div>
