<?php
/**
 * @var $patients CActiveDataProvider
 * @var $display_class string
 * @var $display bool
 */
$sort_field = 'last_name';
$sort_direction = 'ascend';
if (isset($_GET['Patient_sort'])) {
    if (!strpos($_GET['Patient_sort'], '.desc')) {
        $sort_direction = 'ascend';
    } else {
        $sort_direction = 'descend';
    }
    $sort_field = str_replace('.desc', '', $_GET['Patient_sort']);
}

if (!isset($display)) {
    $display = false;
}
if ($patients->itemCount > 0) { ?>
<div class="<?= $display_class ?>"<?= !$display ? ' style="display: none;"' : '' ?>>
    <?php
    //Just create the widget here so we can render it's parts separately
    /** @var $searchResults CListView */
    $searchResults = $this->createWidget(
        'zii.widgets.CListView',
        array(
            'dataProvider' => $patients,
            'itemView' => 'search_results',
            'emptyText' => 'No patients found',
            'viewData' => array(
                'trial' => $this->trialContext
            ),
            'ajaxUpdate' => true,
            'enableSorting' => true,
            'sortableAttributes' => array(
                'last_name',
                'first_name',
                'age',
                'gender',
            )
        )
    );
    $sort = $patients->getSort();
    /**
     * @var $pager LinkPager
     */
    $pager = $this->createWidget(
        'LinkPager',
        array(
            'pages' => $patients->getPagination(),
            'maxButtonCount' => 15,
            'cssFile' => false,
            'nextPageCssClass' => 'oe-i arrow-right-bold medium pad',
            'previousPageCssClass' => 'oe-i arrow-left-bold medium pad',
            'htmlOptions' => array(
                'class' => 'pagination',
            ),
        )
    );
    // Build up the list of sort fields and the relevant ascending/descending sort URLs for each option.
    $sort_fields = array();
    $sort_field_options = array();
    foreach ($sort->attributes as $key => $attribute) {
        $sort_fields[$key] = $attribute['label'];
        $sort_field_options[$key]['data-sort-ascend'] = $sort->createUrl($this, array($key => $sort::SORT_ASC));
        $sort_field_options[$key]['data-sort-descend'] = $sort->createUrl($this, array($key => $sort::SORT_DESC));
    }
    ?>
    <div class="table-sort-order">
        <div class="sort-by">
            Sort by:
            <span class="sort-options">
                        <?= CHtml::dropDownList('sort', $sort_field, $sort_fields, array('id' => 'sort-field', 'options' => $sort_field_options)) ?>
                        <span class="direction">
                            <label class="inline highlight">
                                <?= CHtml::radioButton('sort-options', ($sort_direction === 'ascend'), array('value' => 'ascend')) ?>
                                <i class="oe-i direction-up medium"></i>
                            </label>
                            <label class="inline highlight">
                                <?= CHtml::radioButton('sort-options', ($sort_direction === 'descend'), array('value' => 'descend')) ?>
                                <i class="oe-i direction-down medium"></i>
                            </label>
                        </span>
                    </span>
        </div>
        <?php $pager->run(); ?>
    </div>
    <table id="case-search-results" class="standard last-right">
        <tbody>
        <?= $searchResults->renderItems() ?>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="3"><?php $pager->run(); ?></td>
        </tr>
        </tfoot>
    </table>
<?php } ?>
</div>
<?php
    $patientsID = [];
foreach ($patients->getData() as $i => $SearchPatient) {
    array_push($patientsID, $SearchPatient->id);
}
    $assetManager = Yii::app()->getAssetManager();
    $assetPath = $assetManager->publish(Yii::getPathOfAlias('application.assets'), true, -1);
    Yii::app()->clientScript->registerScriptFile($assetPath . '/js/toggle-section.js');
    $widgetPath = $assetManager->publish('protected/widgets/js');
    Yii::app()->clientScript->registerScriptFile($widgetPath . '/PatientPanelPopupMulti.js');
?>

<script type="text/javascript">
    $(document).ready(renderPopups(<?= json_encode($patientsID) ?>));

    function renderPopups(ids) {
        if (ids[0]) {
            $.ajax({
                'type': "POST",
                'data': "patientsID=" + ids + "&YII_CSRF_TOKEN=" + YII_CSRF_TOKEN,
                'url': "/OECaseSearch/caseSearch/renderPopups",
                success: function(resp) {
                    $("body.open-eyes.oe-grid").append(resp);
                }
            });
            $('body').on('click', '.collapse-data-header-icon', function() {
                $(this).toggleClass('collapse expand');
                $(this).next('div').toggle();
            });
        }
    }
</script>
