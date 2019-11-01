<?php

class m180601_114000_insert_oescape_summaries extends CDbMigration
{
    public function safeUp()
	{
        $event_type_id = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name = ?', array('OphCiExamination'))->queryScalar();

        $this->insert('oescape_summary_item', array('event_type_id' => $event_type_id, 'name' => 'Visual Acuity History'));
        $this->insert('oescape_summary_item', array('event_type_id' => $event_type_id, 'name' => 'Medication'));
        $this->insert('oescape_summary_item', array('event_type_id' => $event_type_id, 'name' => 'Medical Retinal History'));
        $this->insert('oescape_summary_item', array('event_type_id' => $event_type_id, 'name' => 'IOP History'));

        $VA_id = $this->getDbConnection()->createCommand('select id from oescape_summary_item where name ="Visual Acuity History"')->queryRow();
        $Med_id = $this->getDbConnection()->createCommand('select id from oescape_summary_item where name ="Medication"')->queryRow();
        $MR_id = $this->getDbConnection()->createCommand('select id from oescape_summary_item where name ="Medical Retinal History"')->queryRow();
        $IOP_id = $this->getDbConnection()->createCommand('select id from oescape_summary_item where name ="IOP History"')->queryRow();

        $glaucoma_id = $this->getDbConnection()->createCommand('select id from subspecialty where name ="Glaucoma"')->queryRow();
        $cataract_id = $this->getDbConnection()->createCommand('select id from subspecialty where name ="Cataract"')->queryRow();
        $MR_sub_id =  $this->getDbConnection()->createCommand('select id from subspecialty where name ="Medical Retina"')->queryRow();

        $this->insert('oescape_summary', array('display_order'=>0, 'item_id'=>$Med_id['id'], 'subspecialty_id' =>$glaucoma_id['id']));
        $this->insert('oescape_summary', array('display_order'=>1, 'item_id'=>$IOP_id['id'], 'subspecialty_id' =>$glaucoma_id['id']));
        $this->insert('oescape_summary', array('display_order'=>2, 'item_id'=>$VA_id['id'], 'subspecialty_id'=>$glaucoma_id['id']));
        $this->insert('oescape_summary', array('display_order'=>0, 'item_id'=>$MR_id['id'], 'subspecialty_id' =>$MR_sub_id['id']));
        $this->insert('oescape_summary', array('display_order'=>0, 'item_id'=>$VA_id['id'], 'subspecialty_id'=>$cataract_id['id']));

    }

	public function safeDown()
	{

        $VA_id = $this->getDbConnection()->createCommand('select id from oescape_summary_item where name ="Visual Acuity History"')->queryRow();
        $Med_id = $this->getDbConnection()->createCommand('select id from oescape_summary_item where name ="Medication"')->queryRow();
        $MR_id = $this->getDbConnection()->createCommand('select id from oescape_summary_item where name ="Medical Retinal History"')->queryRow();
        $IOP_id = $this->getDbConnection()->createCommand('select id from oescape_summary_item where name ="IOP History"')->queryRow();

        $glaucoma_id = $this->getDbConnection()->createCommand('select id from subspecialty where name ="Glaucoma"')->queryRow();
        $cataract_id = $this->getDbConnection()->createCommand('select id from subspecialty where name ="Cataract"')->queryRow();
        $MR_sub_id =  $this->getDbConnection()->createCommand('select id from subspecialty where name ="Medical Retina"')->queryRow();

        $this->delete('oescape_summary','`item_id` ='.$Med_id['id'].' and `subspecialty_id`='.$glaucoma_id['id']);
        $this->delete('oescape_summary','`item_id` ='.$VA_id['id'].' and `subspecialty_id`='.$glaucoma_id['id']);
        $this->delete('oescape_summary','`item_id` ='.$IOP_id['id'].' and `subspecialty_id`='.$glaucoma_id['id']);
        $this->delete('oescape_summary', '`item_id` ='.$MR_id['id'].' and `subspecialty_id`='.$MR_sub_id['id']);
        $this->delete('oescape_summary', '`item_id` ='.$VA_id['id'].' and `subspecialty_id`='.$cataract_id['id']);

        $event_type_id = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name = ?', array('OphCiExamination'))->queryScalar();

        $this->delete('oescape_summary_item', 'event_type_id = ? and name = ?', array($event_type_id, 'Visual Acuity History'));
        $this->delete('oescape_summary_item', 'event_type_id = ? and name = ?', array($event_type_id, 'Medication'));
        $this->delete('oescape_summary_item', 'event_type_id = ? and name = ?', array($event_type_id, 'Medical Retinal History'));
        $this->delete('oescape_summary_item', 'event_type_id = ? and name = ?', array($event_type_id, 'IOP History'));


    }

}