<?php
/* @var GpController $this */
/* @var CActiveDataProvider $dataProvider */
/* @var string $search_term */
$dataProvider->getPagination()->setPageSize(30);

$dataProvided = $dataProvider->getData();
$gplabel = \SettingMetadata::model()->getSetting('general_practitioner_label');
$this->pageTitle = $gplabel . 's';
$items_per_page = $dataProvider->getPagination()->getPageSize();
$page_num = $dataProvider->getPagination()->getCurrentPage();
$from = ($page_num * $items_per_page) + 1;
$to = min(($page_num + 1) * $items_per_page, $dataProvider->totalItemCount);
?>
<div class="oe-home oe-allow-for-fixing-hotlist">
    <div class="oe-full-header flex-layout">
        <div class="title wordcaps">
            <b><?= $gplabel ?></b>
        </div>
    </div>

    <div class="oe-full-content flex-top">
        <div class="flex-layout oe-new-patient">
            <div class="cols-3">
                <h2>
                    Practitioners: viewing <?php echo $from ?> - <?php echo $to ?>
                    of <?php echo $dataProvider->totalItemCount ?>
                </h2>
                <div>
                    <?php $form = $this->beginWidget('CActiveForm', array(
                        'id' => 'practitioner-search-form',
                        'method' => 'get',
                        'action' => Yii::app()->createUrl('/gp'),
                    )); ?>
                    <?php echo CHtml::textField(
                        'search_term',
                        $search_term,
                        array('placeholder' => 'Enter search query...')
                    ); ?>
                    <?php $this->endWidget(); ?>
                </div>
            </div>
            <?php if (Yii::app()->user->checkAccess('TaskCreateGp')) : ?>
                <div class="cols-4 column end">
                        <p><?php echo CHtml::link('Create ' . $gplabel, $this->createUrl('/gp/create')); ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <table id="gp-grid" class="standard" >
            <thead>
            <tr>
                <th>Name</th>
                <th>Telephone</th>
                <th>Code</th>
                <th>Role</th>
                <th>Active</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($dataProvided as $gp) : ?>
                <tr id="r<?php echo $gp->id; ?>" class="clickable">
                    <td><?php echo CHtml::encode($gp->getCorrespondenceName()); ?></td>
                    <td><?php echo CHtml::encode($gp->contact->primary_phone); ?></td>
                    <td><?php echo CHtml::encode($gp->nat_id ? $gp->nat_id : ''); ?></td>
                    <td><?php echo CHtml::encode(isset($gp->contact->label) ? $gp->contact->label->name : '') ?></td>
                    <td><i id = 'activeStatus' class="oe-i <?= ($gp->getActiveStatus($gp->id) ? 'tick' : 'remove');?> small"></i></td>
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
        <?php if (Yii::app()->user->checkAccess('TaskCreateGp')) : ?>
            <div class="large-4 column end">
                <div class="row">
                    <div class="large-12 column end">
                        <div class="box generic">
                            <p><?php echo CHtml::link('Add', $this->createUrl('/gp/create'), ['class' => 'button small addUser']); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

</div>
<script type="text/javascript">
    $('#gp-grid tr.clickable').click(function () {
        window.location.href = '<?php echo Yii::app()->controller->createUrl('/gp/view')?>/' + $(this).attr('id').match(/[0-9]+/);
        return false;
    });
</script>
