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
