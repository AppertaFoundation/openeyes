<?php
/* @var TrialController $this */
/* @var CActiveDataProvider $dataProvider */
/* @var int $sort_by */
/* @var int $sort_dir */

?>

<div class="worklist-group trial-list">

    <?php
    $dataProvided = $dataProvider->getData();
    $items_per_page = $dataProvider->getPagination()->getPageSize();
    $page_num = $dataProvider->getPagination()->getCurrentPage();
    $from = ($page_num * $items_per_page) + 1;
    $to = min(($page_num + 1) * $items_per_page, $dataProvider->totalItemCount);
    ?>

  <div class="worklist-summary flex-layout">
    <h2>
        <?= $title ?>
    </h2>
  </div>

  <table class="standard clickable-rows js-trial-list">
    <colgroup>
      <col class="cols-4">
      <col class="cols-1">
      <col class="cols-1">
      <col class="cols-3">
      <col class="cols-1">
    </colgroup>
    <thead>
    <?php foreach (array('Name', 'Date Started', 'Date Closed', 'Owner', 'Status') as $i => $field) { ?>
      <th id="patient-grid_c<?php echo $i; ?>">
          <?php
            $new_sort_dir = ($i === $sort_by) ? 1 - $sort_dir : 0;
            $sort_symbol = '';
            if ($i === $sort_by) {
                $sort_symbol = $sort_dir === 1 ? '&#x25BC;' /* down arrow */ : '&#x25B2;'; /* up arrow */
            }

            echo CHtml::link(
              $field . $sort_symbol,
              Yii::app()->createUrl('/OETrial/trial/index',
                  array('sort_by' => $i, 'sort_dir' => $new_sort_dir, 'page_num' => $page_num))
          );
            ?>
      </th>
    <?php } ?>
    </thead>
    <tbody>
        <?php /* @var Trial $trial */
        foreach ($dataProvided as $i => $trial) : ?>
        <tr id="r<?php echo $trial->id; ?>" class="clickable">
          <td><?php echo CHtml::encode($trial->name); ?></td>
          <td><?php echo $trial->getStartedDateForDisplay(); ?></td>
          <td><?php echo $trial->getClosedDateForDisplay(); ?></td>
          <td><?php echo CHtml::encode($trial->ownerUser->getFullName()); ?></td>
          <td><?php echo $trial->is_open ? 'Open' : 'Closed' ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>

    <tfoot class="pagination-container">
    <tr>
      <td colspan="7">
            <?php
            $this->widget('LinkPager', array(
              'pages' => $dataProvider->getPagination(),
              'maxButtonCount' => 15,
              'cssFile' => false,
              'selectedPageCssClass' => 'current',
              'hiddenPageCssClass' => 'unavailable',
              'htmlOptions' => array(
                  'class' => 'pagination',
              ),
            ));
            ?>
      </td>
    </tr>
    </tfoot>
  </table>

</div>
