<?php
/* @var TrialController $this */
/* @var CActiveDataProvider $dataProvider */
/* @var int $sort_by */
/* @var int $sort_dir */

?>

<div class="worklist-group searched-trial-list" style="display: none" >

    <?php
    $dataProvided = $dataProvider->getData();

    ?>

  <div class="worklist-summary flex-layout">
    <h2>
        <?= $title ?>
    </h2>
  </div>

  <table class="standard clickable-rows js-trial-list" id="search-table-<?php echo str_replace(' ', '-', strtolower($title));?>">
    <colgroup>
      <col class="cols-4">
      <col class="cols-1">
      <col class="cols-1">
      <col class="cols-3">
      <col class="cols-1">
      <col class="cols-1">
    </colgroup>
    <thead>
    <?php
    foreach (array('Name', 'Date Started', 'Date Closed', 'Owner', 'Status','Intervention') as $i) { ?>
    <th id="trials-search-list-<?php echo $i;?>">
        <a href="#">
        <?php
        echo $i;
        ?>
        </a>
        <a class="trials-search-list-a"></a>
    </th>
    <?php }?>
    </thead>
    <tbody>
        <?php /* @var Trial $trial */
        foreach ($dataProvided as $i => $trial) :
            $filter_date_start = $trial->started_date ? (new DateTime($trial->started_date))->format('Y-m-d') : '';
            $filter_date_closed = $trial->closed_date ? (new DateTime($trial->closed_date))->format('Y-m-d') : '';
            ?>
        <tr id="r<?php echo $trial->id; ?>" class="clickable" data-hidden-label='hide' data-trial-name="<?php echo $trial->name; ?>" data-trial-description="<?php echo $trial->description ?>"
            data-trial-start="<?= $filter_date_start ?>" data-trial-closed="<?= $filter_date_closed ?>">
          <td><?php echo CHtml::encode($trial->name); ?></td>
          <td><?php echo $trial->getStartedDateForDisplay(); ?></td>
          <td><?php echo $trial->getClosedDateForDisplay(); ?></td>
          <td><?php echo CHtml::encode($trial->ownerUser->getFullName()); ?></td>
          <td><?php echo $trial->is_open ? 'Open' : 'Closed' ?></td>
          <td><?php echo ($trial->trialType->name == "Intervention")? 'Yes':'No'; ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
      <tfoot class="pagination-container">
              <td colspan="7">
                  <div class="pagination"></div>
              </td>
      </tfoot>
  </table>

</div>
