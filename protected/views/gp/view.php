<?php
/* @var $this GpController */
/* @var $model Gp */

$this->pageTitle = 'View Practitioner';
$dataProvided = $dataProvider->getData();
$items_per_page = $dataProvider->getPagination()->getPageSize();
$page_num = $dataProvider->getPagination()->getCurrentPage();
$from = ($page_num * $items_per_page) + 1;
$to = min(($page_num + 1) * $items_per_page, $dataProvider->totalItemCount);

?>
<div class="oe-home oe-allow-for-fixing-hotlist">
    <div class="oe-full-header flex-layout">
        <div class="title wordcaps">
            <b>Practitioner Summary</b>
        </div>
    </div>
    <div class="oe-full-content oe-new-patient flex-layout flex-top">
        <div class="cols-6 box patient-info js-toggle-container">
            <nav>
                <ul>
                    <li>
                        <a href="/gp/index">Back to GP</a>
                    </li>
                </ul>
            </nav>
            <br />
            <h3 class="box-title">Contact Information</h3>
            <div class="js-toggle-body">
                <table class="standard">
                    <tbody>
                    <tr>
                        <td>
                            Name:
                        </td>
                        <td>
                            <?php echo CHtml::encode($model->getCorrespondenceName()); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Phone Number:
                        </td>
                        <td>
                            <?php echo isset($model->contact->primary_phone) ? CHtml::encode($model->contact->primary_phone) : 'Unknown'; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            National Id:
                        </td>
                        <td>
                            <?php echo isset($model->nat_id) ? CHtml::encode($model->nat_id) : 'Unknown'; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Role:
                        </td>
                        <td>
                            <?php echo CHtml::encode(isset($model->contact->label) ? $model->contact->label->name : ''); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Active:</td>
                        <td>
                            <i id = 'activeStatus' class="oe-i <?= ($model->getActiveStatus($model->id) ? 'tick' : 'remove');?> small"></i>
                        </td>
                    </tr>
                    </tbody>
                    <!--Add the address row here when GPs get tied directly to practices rather than through patient records.-->
                </table>
            </div>
            <a href="#" class="toggle-trigger toggle-hide js-toggle">
                <span class="icon-showhide">Show/hide this section</span>
            </a>
        </div>
        <?php if (Yii::app()->user->checkAccess('TaskCreateGp')) : ?>
            <div class="large-4 column end">
                <div class="box generic">
                    <div class="row">
                        <div class="large-12 column end">
                            <p><?php echo CHtml::link(
                                'Update Practitioner Details',
                                $this->createUrl('/gp/update', array('id' => $model->id))
                               ); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <?php if ($dataProvided) : ?>
        <div class="oe-full-content oe-new-patient">
            <h3 class="box-title">Associated Practices</h3>
            <br />
            <div>
                <table id="practice-grid" class="standard">
                    <thead>
                    <tr>
                        <th>Provider Number</th>
                        <th>Practice Contact</th>
                        <th>Practice Address</th>
                        <th>Code</th>
                        <th>Telephone</th>
                        <th/>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($dataProvided as $cpa) : ?>
                        <tr id="r<?php echo $cpa->id; ?>" class="clickable">
                            <td><?php echo CHtml::encode($cpa->provider_no); ?></td>
                            <td><?php echo CHtml::encode($cpa->practice->contact->first_name); ?></td>
                            <td><?php echo CHtml::encode($cpa->practice->getAddressLines()); ?></td>
                            <td><?php echo CHtml::encode($cpa->practice->code); ?></td>
                            <td><?php echo CHtml::encode($cpa->practice->phone); ?></td>
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
