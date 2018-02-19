<?php

Yii::import('zii.widgets.grid.CGridView');

class MessageGridView extends CGridView
{
    public $is_hidden;
    public $message_type;

    /**
     * @override CBaseListView::run() without wrapper <div>
     */
    public function run()
    {
        $this->registerClientScript();
        $this->renderContent();
        $this->renderKeys();
    }
/*
    /**
     * Renders the data items for the grid view.
     * /
    public function renderItems()
    {
        if ($this->dataProvider->getItemCount() > 0 || $this->showTableOnEmpty) {
            $style = $this->is_hidden ? 'display: none' : '';
            echo "<table class=\"{$this->itemsCssClass}\" id=\"{$this->message_type}\" style=\"{$style}\">\n";
            $this->renderTableHeader();
            ob_start();
            $this->renderTableBody();
            $body = ob_get_clean();
            $this->renderTableFooter();
            echo $body; // TFOOT must appear before TBODY according to the standard.
            echo '</table >';
        } else {
            $this->renderEmptyText();
        }
    }
*/
    public function renderTableHeader()
    {
        ?>
      <colgroup>
        <col style="width:80px;">
        <col style="width:70px;">
        <col style="width:50px;">
        <col>
        <col style="width:20px;">
        <col style="width:90px;">
      </colgroup>
        <?php
        parent::renderTableHeader();
    }
}
