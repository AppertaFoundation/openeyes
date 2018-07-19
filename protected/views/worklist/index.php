<?php
/**
 * @var Worklist[] $worklists
 */
?>

<div class="oe-full-header flex-layout">
  <div class="title wordcaps">Worklists</div>
</div>

<div class="oe-full-content oe-worklists flex-layout flex-top">

  <nav class="oe-full-side-panel">
    <p>Automatic Worklists</p>
    <div class="data-group">
      <?php $this->renderPartial('//site/change_site_and_firm', array('returnUrl' => Yii::app()->request->url, 'mode' => 'static')); ?>
    </div>
    <h3>Filter by Date</h3>
    <div class="flex-layout">
      <input id="worklist-date-from" class="cols-5" placeholder="from" type="text" value="<?= @$_GET['date_from'] ?>">
      <input id="worklist-date-to" class="cols-5" placeholder="to" type="text" value="<?= @$_GET['date_to'] ?>">
    </div>
    <h3>Select list</h3>
    <ul>
      <li><a href="#">All</a></li>
        <?php foreach ($worklists as $worklist): ?>
          <li><a href="#worklist_<?= $worklist->id ?>"><?= $worklist->name ?></a></li>
        <?php endforeach; ?>
    </ul>
  </nav>
  <main class="oe-full-main">
      <?php foreach ($worklists as $worklist): ?>
          <?php echo $this->renderPartial('_worklist', array('worklist' => $worklist)); ?>
      <?php endforeach; ?>
  </main>
</div>
<script type="text/javascript">
  $(function() {

    pickmeup('#worklist-date-from', {
      format: 'd b Y',
      hide_on_select: true,
      date: $('#worklist-date-from').val(),
      default_date: false,
    });
    pickmeup('#worklist-date-to', {
      format: 'd b Y',
      hide_on_select: true,
      date: $('#worklist-date-to').val(),
      default_date: false,
    });

    $('#worklist-date-from, #worklist-date-to').on('pickmeup-change change', function(){
      window.location.href = jQuery.query
        .set('date_from', $('#worklist-date-from').val())
        .set('date_to', $('#worklist-date-to').val());
    });
  });
</script>