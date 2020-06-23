<?php

class m185401_134847_create_generic_event_and_comments_table extends \OEMigration
{
    public function safeUp()
    {
        $this->insert('event_type', ['name' => 'Device Information', 'event_group_id' => 1, 'class_name' => 'OphGeneric', 'can_be_created_manually' => 0]);
        $event_type_id = \Yii::app()->db->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name' => 'OphGeneric'))->queryScalar();
        $this->insert('element_type', [
            'name' => 'Comments',
            'class_name' => 'OEModule\OphGeneric\models\Comments',
            'event_type_id' => $event_type_id,
            'display_order' => 10,
            'required' => 1]);

        $this->insert('element_type', [
                'name' => 'Attachment',
                'class_name' => 'OEModule\OphGeneric\models\Attachment',
                'event_type_id' => $event_type_id,
                'display_order' => 1,
                'required' => 1
            ]
        );

        $this->createOETable('et_ophgeneric_attachment', [
            'id' => 'pk',
            'event_id' => 'INT(10) UNSIGNED NOT NULL DEFAULT 0',
        ], true);
        $this->createOETable(
            'et_ophgeneric_comments',
            ['id' => 'pk', 'comment' => 'text', 'event_id' => 'int(10) unsigned NOT NULL'],
            true
        );
        $this->addForeignKey('fk_documentophgeneric_event_id', 'et_ophgeneric_comments', 'event_id', 'event', 'id');
        $this->addForeignKey('et_ophgeneric_attach_ev_fk', 'et_ophgeneric_attachment', 'event_id', 'event', 'id');
    }

    public function safeDown()
    {
            $event_type_id = \Yii::app()->db->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name' => 'OphGeneric'))->queryScalar();
            $this->delete('element_type', 'class_name = ? AND event_type_id = ?', ['OEModule\OphGeneric\models\Comments', $event_type_id]);
            $this->delete('element_type', 'class_name = ? AND event_type_id = ?', ['OEModule\OphGeneric\models\Attachment', $event_type_id]);
            $this->delete('event_type', 'class_name = ?', ["OphGeneric"]);
            $this->dropForeignKey('fk_documentophgeneric_event_id', 'et_ophgeneric_comments');
            $this->dropForeignKey('et_ophgeneric_attach_ev_fk', 'et_ophgeneric_attachment');
            $this->dropOETable('et_ophgeneric_comments', true);
            $this->dropOETable('et_ophgeneric_attachment', true);

            $event_type_id = \Yii::app()->db->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name' => 'OphInBiometry'))->queryScalar();
            $this->delete('element_type', 'class_name = ? AND event_type_id = ?', [ 'OEModule\OphGeneric\models\Attachment', $event_type_id]);
    }
}
