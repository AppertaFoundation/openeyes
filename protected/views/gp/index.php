<?php
/* @var GpController $this */
/* @var CActiveDataProvider $dataProvider */
/* @var string $search_term */
$dataProvided = $dataProvider->getData();
$this->pageTitle = 'Practitioners';
$items_per_page = $dataProvider->getPagination()->getPageSize();
$page_num = $dataProvider->getPagination()->getCurrentPage();
$from = ($page_num * $items_per_page) + 1;
$to = min(($page_num + 1) * $items_per_page, $dataProvider->totalItemCount);
?>

<h1 class="badge">Practitioners</h1>

<div class="row data-row">
  <div class="large-8 column">
    <div class="box generic">
      <div class="row">
        <div class="large-6 column">
          <h2>
            Practitioners: viewing <?php echo $from ?> - <?php echo $to ?>
            of <?php echo $dataProvider->totalItemCount ?>
          </h2>
        </div>
        <div class="large-4 column">
            <?php $form = $this->beginWidget('CActiveForm', array(
                'id' => 'practitioner-search-form',
                'method' => 'get',
                'action' => Yii::app()->createUrl('/gp'),
            )); ?>
            <?php echo CHtml::textField('search_term', $search_term,
                array('placeholder' => 'Enter search query...')); ?>
            <?php $this->endWidget(); ?>
        </div>
      </div>
      <table id="gp-grid" class="grid">
        <thead>
        <tr>
          <th>Name</th>
          <th>Telephone</th>
          <th>Role</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($dataProvided as $gp): ?>
          <tr id="r<?php echo $gp->id; ?>" class="clickable">
            <td><?php echo CHtml::encode($gp->getCorrespondenceName()); ?></td>
            <td><?php echo CHtml::encode($gp->contact->primary_phone); ?></td>
            <td><?php echo CHtml::encode(isset($gp->contact->label) ? $gp->contact->label->name : '') ?></td>
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
    <?php if (Yii::app()->user->checkAccess('TaskCreateGp')): ?>
      <div class="large-4 column end">
        <div class="row">
          <div class="large-12 column end">
            <div class="box generic">
              <p><?php echo CHtml::link('Create Practitioner', $this->createUrl('/gp/create')); ?></p>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
</div>


<script type="text/javascript">
  $('#gp-grid tr.clickable').click(function () {
    window.location.href = '<?php echo Yii::app()->controller->createUrl('/gp/view')?>/' + $(this).attr('id').match(/[0-9]+/);
    return false;
  });
</script>

