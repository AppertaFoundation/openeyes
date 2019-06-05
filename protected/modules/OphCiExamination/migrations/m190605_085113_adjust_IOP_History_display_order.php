<?php

class m190605_085113_adjust_IOP_History_display_order extends CDbMigration
{
	public function up()
	{
	    $this->update('element_type' , ['display_order' => 305],'class_name =:class_name',
            [':class_name' => 'OEModule\OphCiExamination\models\HistoryIOP']);
	}

	public function down()
	{
        $this->update('element_type' , ['display_order' => 310],'class_name =:class_name',
            [':class_name' => 'OEModule\OphCiExamination\models\HistoryIOP']);
	}
}