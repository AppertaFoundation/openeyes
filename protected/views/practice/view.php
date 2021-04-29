<?php
/* @var $this PracticeController */
/* @var $model Practice */

$this->pageTitle = 'View Practice';
$dataProvided = $dataProvider->getData();
$items_per_page = $dataProvider->getPagination()->getPageSize();
$page_num = $dataProvider->getPagination()->getCurrentPage();
$from = ($page_num * $items_per_page) + 1;
$to = min(($page_num + 1) * $items_per_page, $dataProvider->totalItemCount);
?>
<div class="oe-home">
    <div class="oe-full-header flex-layout">
        <div class="title wordcaps">
            Practice&nbsp;<b>Summary</b>
        </div>
    </div>

    <div class="oe-full-content oe-new-patient flex-layout flex-top">
        <nav class="cols-3">
            <ul>
                <li>
                    <a href="/practice/index">Back to Practices</a>
                </li>
            </ul>
        </nav>
        <section class="box patient-info patient-contacts js-toggle-container">
            <div class="js-toggle-body">
                <table class="standard">
                    <tbody>
                    <tr>
                        <td>
                            <?php echo CHtml::label('Practice Contact', null); ?>
                        </td>
                        <td>
                            <?php echo CHtml::encode($model->contact->getFullName()); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php echo CHtml::label('Practice Address', null); ?>
                        </td>
                        <td>
                            <?php echo CHtml::encode($model->getAddressLines()); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php echo CHtml::label('Code', null); ?>
                        </td>
                        <td>
                            <?php echo CHtml::encode($model->code); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php echo CHtml::label('Phone', null); ?>
                        </td>
                        <td>
                            <?php echo isset($model->phone) ? CHtml::encode($model->phone) : 'Unknown'; ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="align-right">
                <?php if (Yii::app()->user->checkAccess('TaskCreatePractice')) : ?>
              <a  href="<?= $this->createUrl('/practice/update', array('id' => $model->id))?>">
                  <button class="button hint blue pad pro-theme"

                >
                    Update Practice Details
                </button>
              </a>
                <?php endif; ?>
                </div>
            </div>
        </section>
    </div>
    <?php if ($dataProvided) : ?>
        <div class="oe-full-content oe-new-patient">
            <h3 class="box-title">Associated Practitioners</h3>
            <br />
            <div>
                <table id="practice-grid" class="standard">
                    <thead>
                    <tr>
                        <th>Provider Number</th>
                        <th>Practitioner Name</th>
                        <th>Practitioner Phone Number</th>
                        <th>Role</th>
                        <th/>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($dataProvided as $cpa) : ?>
                        <tr id="r<?php echo $cpa->id; ?>" class="clickable">
                            <td><?php echo CHtml::encode($cpa->provider_no); ?></td>
                            <td><?php echo CHtml::encode($cpa->gp->contact->title. ' ' .$cpa->gp->contact->first_name. ' ' .$cpa->gp->contact->last_name); ?></td>
                            <td><?php echo CHtml::encode($cpa->gp->contact->primary_phone); ?></td>
                            <td><?php echo CHtml::encode($cpa->gp->contact->label->name); ?></td>
                            <td/>
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
    <?php endif; ?>
</div>

