<?php

class m131217_122048_exam_episode_summary_items extends OEMigration
{
    public function up()
    {
        $event_type_id = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name = ?', array('OphCiExamination'))->queryScalar();

        $this->insert('episode_summary_item', array('event_type_id' => $event_type_id, 'name' => 'CCT'));
        $this->insert('episode_summary_item', array('event_type_id' => $event_type_id, 'name' => 'IOP'));
        $this->insert('episode_summary_item', array('event_type_id' => $event_type_id, 'name' => 'Medical Retinal History'));
        $this->insert('episode_summary_item', array('event_type_id' => $event_type_id, 'name' => 'IOP History'));
    }

    public function down()
    {
        $event_type_id = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name = ?', array('OphCiExamination'))->queryScalar();

        $this->delete('episode_summary_item', 'event_type_id = ? and name = ?', array($event_type_id, 'CCT'));
        $this->delete('episode_summary_item', 'event_type_id = ? and name = ?', array($event_type_id, 'IOP'));
        $this->delete('episode_summary_item', 'event_type_id = ? and name = ?', array($event_type_id, 'Medical Retinal History'));
        $this->delete('episode_summary_item', 'event_type_id = ? and name = ?', array($event_type_id, 'IOP History'));
    }
}
