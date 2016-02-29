<?php

class m140605_134057_glaucoma_summary_items extends OEMigration
{
    public function safeUp()
    {
        $event_type_id = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name = ?', array('OphCiExamination'))->queryScalar();

        $this->insert('episode_summary_item', array('event_type_id' => $event_type_id, 'name' => 'Refraction'));
        $this->insert('episode_summary_item', array('event_type_id' => $event_type_id, 'name' => 'IOP1'));
        $this->insert('episode_summary_item', array('event_type_id' => $event_type_id, 'name' => 'Glaucoma Management Plan'));
        $this->insert('episode_summary_item', array('event_type_id' => $event_type_id, 'name' => 'Visual Acuity History'));
    }

    public function safeDown()
    {
        $event_type_id = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name = ?', array('OphCiExamination'))->queryScalar();

        $this->delete('episode_summary_item', 'event_type_id = ? and name = ?', array($event_type_id, 'Refraction'));
        $this->delete('episode_summary_item', 'event_type_id = ? and name = ?', array($event_type_id, 'IOP1'));
        $this->delete('episode_summary_item', 'event_type_id = ? and name = ?', array($event_type_id, 'Glaucoma Management Plan'));
        $this->delete('episode_summary_item', 'event_type_id = ? and name = ?', array($event_type_id, 'Visual Acuity History'));
    }
}
