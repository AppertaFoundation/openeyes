<?php

class m180601_131147_create_elements_comments_table extends \OEMigration
{
    public function up()
    {

        $this->insert('event_type', ['name' => 'Did Not Attend', 'event_group_id' => 1, 'class_name' => 'OphCiDidNotAttend',]);
        $event_type_id = \Yii::app()->db->createCommand()->select('id')->from('event_type')->where('name=:name', array(':name' => 'Did Not Attend'))->queryScalar();
        $this->insert('element_type', [
            'name' => 'Comments',
            'class_name' => 'OEModule\OphCiDidNotAttend\models\Comments',
            'event_type_id' => $event_type_id, 'required' => 1]);
        $this->createOETable('et_ophcididnotattend_comments', ['id' => 'pk', 'comment' => 'text', 'event_id' => 'int(10) unsigned NOT NULL'], true);
        $this->addForeignKey('fk_documentdidnotattend_event_id', 'et_ophcididnotattend_comments', 'event_id', 'event', 'id');
    }

    public function down()
    {
        $event_type_id = \Yii::app()->db->createCommand()->select('id')->from('event_type')->where('name=:name', array(':name' => 'Did Not Attend'))->queryScalar();
        $this->delete('elemnt_type', ['name' => 'Comments', 'class_name' => 'OEModule\OphCiDidNotAttend\models\Comments', 'event_type_id' => $event_type_id]);
        $this->delete('event_type', ['name' => 'Did Not Attend', 'event_group_id' => 1, 'class_name' => 'OphCiDidNotAttend']);
        $this->dropForeignKey('fk_documentdidnotattend_event_id', 'et_ophcididnotattend_comments');
        $this->dropOETable('et_ophcididnotattend_comments');
    }

    /*
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}