<?php
/**
 * @var TrialController $this
 *
 * @var Trial $trial
 * @var TrialPermission $permission
 * @var CActiveDataProvider $dataProvider
 * @var bool $renderTreatmentType
 * @var string $title
 * @var int $sort_by
 * @var int $sort_dir
 */
?>
<?php
    $dataProvided = $dataProvider->getData();
    $items_per_page = $dataProvider->getPagination()->getPageSize();
    $page_num = $dataProvider->getPagination()->getCurrentPage();
    $from = ($page_num * $items_per_page) + 1;
    $to = min(($page_num + 1) * $items_per_page, $dataProvider->totalItemCount);
?>
        <h2 class=""><?= $title; ?></h2>
        <select class="js-trails-sort-selector">
            <option selected disabled value="" style="display: none;">
                Sorting by <?= $sort_by?> <?= $sort_dir ? 'descending' : 'ascending'?>&nbsp;
            </option>
            <?php
            $columns = array(
                'Name',
                'Sex',
                'Age',
                'Ethnicity',
                'External Reference',
                'Accepted/Rejected Date'
            );


            $sortableColumns = array('Name', 'Sex', 'Age', 'Ethnicity', 'External Reference', 'Accepted/Rejected Date');

            if ($trial->trialType->code === TrialType::INTERVENTION_CODE && !$trial->is_open && $renderTreatmentType) {
                $columns[] = 'Treatment Type';
                $sortableColumns[] = 'Treatment Type';
            }

            foreach ($columns as $field) : ?>
                <?php
                $new_sort_dir = ($field === $sort_by) ? 1 - $sort_dir : 0;
                $sort_symbol = '';
                if ($field === $sort_by) {
                    $sort_symbol = $sort_dir === 1 ? '&#x25BC;' /* down arrow */ : '&#x25B2;'; /* up arrow */
                }
                ?>
                <option
                        value="<?=
                        $this->createUrl(
                            'view',
                            array(
                                'id' => $trial->id,
                                'sort_by' => $field,
                                'sort_dir' => $new_sort_dir,
                                'page_num' => $page_num,
                            )
                               ) ?>"
                >
                    <?= CHtml::link(
                        $field . $sort_symbol,
                        $this->createUrl(
                            'view',
                            array(
                                'id' => $trial->id,
                                'sort_by' => $field,
                                'sort_dir' => $new_sort_dir,
                                'page_num' => $page_num,
                            )
                        )
                    ); ?>
                </option>
            <?php endforeach; ?>
        </select>

    <table class="standard">
        <colgroup>
            <col class="cols-4">
            <col class="cols-2">
            <col class="cols-2">
        </colgroup>
        <tbody>
        <?php /* @var Trial $trial */
        foreach ($dataProvided as $i => $trialPatient) {
            $this->renderPartial('/trialPatient/_view', array(
                'data' => $trialPatient,
                'renderTreatmentType' => $renderTreatmentType,
                'permission' => $permission,
            ));
        }
        ?>
        </tbody>
        <tfoot class="pagination-container">
        <tr>
            <td colspan="9">
                <div class="pagination">
                    <?php
                    $this->widget('LinkPager', array(
                        'pages' => $dataProvider->getPagination(),
                        'maxButtonCount' => 15,
                        'cssFile' => false,
                        'nextPageCssClass' => 'oe-i arrow-right-bold medium pad',
                        'previousPageCssClass' => 'oe-i arrow-left-bold medium pad',
                        'htmlOptions' => array(
                            'class' => 'pagination',
                        ),
                    ));
                    ?>
                </div>
            </td>
        </tr>
        </tfoot>
    </table>
