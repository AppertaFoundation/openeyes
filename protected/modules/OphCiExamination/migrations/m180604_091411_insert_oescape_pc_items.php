<?php

class m180604_091411_insert_oescape_pc_items extends CDbMigration
{

	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
        $VA_id = $this->getDbConnection()->createCommand('select id from oescape_summary_item where name ="Visual Acuity History"')->queryRow();
        $Med_id = $this->getDbConnection()->createCommand('select id from oescape_summary_item where name ="Medication"')->queryRow();
        $IOP_id = $this->getDbConnection()->createCommand('select id from oescape_summary_item where name ="IOP History"')->queryRow();

        $PC_id =  $this->getDbConnection()->createCommand('select id from subspecialty where name ="General Ophthalmology"')->queryRow();

        $this->insert('oescape_summary', array('display_order'=>0, 'item_id'=>$Med_id['id'], 'subspecialty_id' =>$PC_id['id']));
        $this->insert('oescape_summary', array('display_order'=>2, 'item_id'=>$IOP_id['id'], 'subspecialty_id' =>$PC_id['id']));
        $this->insert('oescape_summary', array('display_order'=>1, 'item_id'=>$VA_id['id'], 'subspecialty_id'=>$PC_id['id']));
    }

	public function safeDown()
	{
        $VA_id = $this->getDbConnection()->createCommand('select id from oescape_summary_item where name ="Visual Acuity History"')->queryRow();
        $Med_id = $this->getDbConnection()->createCommand('select id from oescape_summary_item where name ="Medication"')->queryRow();
        $IOP_id = $this->getDbConnection()->createCommand('select id from oescape_summary_item where name ="IOP History"')->queryRow();

        $PC_id =  $this->getDbConnection()->createCommand('select id from subspecialty where name ="General Ophthalmology"')->queryRow();

        $this->delete('oescape_summary','`item_id` ='.$Med_id['id'].' and `subspecialty_id`='.$PC_id['id']);
        $this->delete('oescape_summary','`item_id` ='.$VA_id['id'].' and `subspecialty_id`='.$PC_id['id']);
        $this->delete('oescape_summary','`item_id` ='.$IOP_id['id'].' and `subspecialty_id`='.$PC_id['id']);
	}

}