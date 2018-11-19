<?php
/* @var $this PracticeController */
/* @var $dataProvider CActiveDataProvider */
$dataProvided = $dataProvider->getData();
$this->pageTitle = 'Practices';
$items_per_page = $dataProvider->getPagination()->getPageSize();
$page_num = $dataProvider->getPagination()->getCurrentPage();
$from = ($page_num * $items_per_page) + 1;
$to = min(($page_num + 1) * $items_per_page, $dataProvider->totalItemCount);
?>

<h1 class="badge">Practices</h1>

<div class="row data-row">
  <div class="large-8 column">
    <div class="box generic">
      <h2>
        Practices: viewing <?php echo $from ?> - <?php echo $to ?>
        of <?php echo $dataProvider->totalItemCount ?>
      </h2>
      <table id="practice-grid" class="grid">
        <thead>
        <tr>
          <th>Practice Contact</th>
          <th>Practice Address</th>
          <th>Code</th>
          <th>Telephone</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($dataProvided as $practice): ?>
          <tr id="r<?php echo $practice->id; ?>" class="clickable">
            <td><?php echo CHtml::encode($practice->contact->getFullName()); ?></td>
            <td><?php echo CHtml::encode($practice->getAddressLines()); ?></td>
            <td><?php echo CHtml::encode($practice->code); ?></td>
            <td><?php echo CHtml::encode($practice->phone); ?></td>
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
  </div>
    <?php if (Yii::app()->user->checkAccess('TaskCreatePractice')): ?>
      <div class="large-4 column end">
        <div class="row">
          <div class="large-12 column end">
            <div class="box generic">
              <p><?php echo CHtml::link('Create Practice', $this->createUrl('/practice/create')); ?></p>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
</div>


<script type="text/javascript">
  $('#practice-grid tr.clickable').click(function () {
    window.location.href = '<?php echo Yii::app()->controller->createUrl('/practice/view')?>/' + $(this).attr('id').match(/[0-9]+/);
    return false;
  });
</script>

