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
<div class="oe-home">
    <div class="oe-full-header flex-layout">
        <div class="title wordcaps">
            <b>Practices</b>
        </div>
    </div>
    <div class="oe-full-content oe-new-patient">
        <div>
            <table id="practice-grid" class="standard">
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
                    <?php if (Yii::app()->user->checkAccess('TaskCreatePractice')): ?>
                    <td>
                        <a href="<?=$this->createUrl('/practice/create')?>">
                        <button class="button hint green">
                            <i class="oe-i plus pad pro-theme"></i>
                            Create Practice
                        </button>
                        </a>
                    </td>
                    <?php endif; ?>
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
</div>


<script type="text/javascript">
    $('#practice-grid tr.clickable').click(function () {
        window.location.href = '<?php echo Yii::app()->controller->createUrl('/practice/view')?>/' + $(this).attr('id').match(/[0-9]+/);
        return false;
    });
</script>

