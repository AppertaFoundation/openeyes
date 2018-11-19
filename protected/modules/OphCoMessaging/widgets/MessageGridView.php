<?php

Yii::import('zii.widgets.grid.CGridView');

class MessageGridView extends CGridView
{
    /**
     * @override CBaseListView::run() without wrapper <div>
     */
    public function run()
    {
        $this->registerClientScript();
        $this->renderContent();
        $this->renderKeys();
    }

    /**
     * @override CGridView::renderTableHeader() with following changes
     *  + colgroups have been added to force minimum width of columns
     *  + pager has been added as the last three columns of the table headers
     */
    public function renderTableHeader()
    {
        ?>
        <colgroup>
            <col span="3">
            <col class="cols-2">
        </colgroup>
        <?php
        echo "<thead>\n";

        if ($this->filterPosition === self::FILTER_POS_HEADER) {
            $this->renderFilter();
        }

        // Only render column headings up the messages header...
        echo "<tr>\n";
        $column_count = count($this->columns);
        for ($i = 0; $i < $column_count; ++$i) {
            $column = $this->columns[$i];
            if ($column->id === 'message') {
                break;
            }
            $column->renderHeaderCell();
        }

        // Then use the leftover columns to render the link pager
        echo '<th colspan="' . ($column_count - $i) . '">';
        $this->widget('LinkPager', ['pages' => $this->dataProvider->getPagination()]);
        echo '</th>';

        echo "</tr>\n";

        if ($this->filterPosition === self::FILTER_POS_BODY) {
            $this->renderFilter();
        }

        echo "</thead>\n";
    }
}
