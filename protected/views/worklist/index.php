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
    <div class="row">
      <?php $this->renderPartial('//site/change_site_and_firm', array('returnUrl' => Yii::app()->request->url, 'mode' => 'static')); ?>
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