<?php

class m190509_164708_assessment_element extends OEMigration
{
    public function safeUp()
    {
        $this->createOETable('ophgeneric_assessment_entry', [
            'id' => 'pk',
            'element_id' => 'int(11) NOT NULL',
            'crt' => 'INT(10) DEFAULT NULL',
            'avg_thickness' => 'INT(10) DEFAULT NULL',
            'total_vol' => 'INT(10) DEFAULT NULL',
            'no_fluid' => 'INT(10) DEFAULT NULL',
            'irf' => 'INT(10) DEFAULT NULL',
            'srf' => 'INT(10) DEFAULT NULL',
            'cysts' => 'INT(10) DEFAULT NULL',
            'retinal_thickening' => 'INT(10) DEFAULT NULL',
            'ped' => 'INT(10) DEFAULT NULL',
            'cmo' => 'INT(10) DEFAULT NULL',
            'dmo' => 'INT(10) DEFAULT NULL',
            'heamorrhage' => 'INT(10) DEFAULT NULL',
            'exudates' => 'INT(10) DEFAULT NULL',
            'avg_rnfl' => 'INT(10) DEFAULT NULL',
            'cct' => 'INT(10) DEFAULT NULL',
            'cd_ratio' => 'INT(10) DEFAULT NULL',
            'eye_id' => 'INT(10) unsigned'
        ], true);

        $this->createOETable('et_ophgeneric_assessment', [
            'id' => 'pk',
            'event_id' => 'INT(10) UNSIGNED NOT NULL DEFAULT 0',
            'eye_id' => 'INT(10) unsigned'
        ], true);

        $event_type_id = \Yii::app()->db->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name' => 'OphGeneric'))->queryScalar();
        $this->insert('element_type', [
            'name' => 'Assessment',
            'class_name' => 'OEModule\OphGeneric\models\Assessment',
            'event_type_id' => $event_type_id,
            'display_order' => 20,
            'required' => 1]);

        $this->addForeignKey('et_ophgeneric_assessment_ev_fk', 'et_ophgeneric_assessment', 'event_id', 'event', 'id');
        $this->addForeignKey('generic_assessment_entry_eye_fk', 'ophgeneric_assessment_entry', 'eye_id', 'eye', 'id');
        $this->addForeignKey('generic_assessment_eye_fk', 'et_ophgeneric_assessment', 'eye_id', 'eye', 'id');
        $this->addForeignKey(
            'generic_assessment_entry_element_fk',
            'ophgeneric_assessment_entry',
            'element_id',
            'et_ophgeneric_assessment',
            'id'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('et_ophgeneric_assessment_ev_fk', 'et_ophgeneric_assessment');
        $this->dropForeignKey('generic_assessment_entry_eye_fk', 'ophgeneric_assessment_entry');
        $this->dropForeignKey('generic_assessment_eye_fk', 'et_ophgeneric_assessment');
        $this->dropForeignKey('et_ophgeneric_assessment_ev_fk', 'et_ophgeneric_assessment');
        $this->dropOETable('et_ophgeneric_assessment', true);
        $this->dropOETable('ophgeneric_assessment_entry', true);
    }
}
