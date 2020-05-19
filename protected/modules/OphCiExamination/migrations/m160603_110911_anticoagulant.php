<?php

class m160603_110911_anticoagulant extends OEMigration
{
    public function up()
    {
        $event_type = $this->dbConnection->createCommand('SELECT id FROM event_type WHERE name = :name')
            ->bindValues(array(':name' => 'Examination'))
            ->queryScalar();
        // Insert element types (in order of display)
        $element_types = array(
            'OEModule\OphCiExamination\models\Element_OphCiExamination_HistoryRisk' => array('name' => 'Risk', 'parent_element_type_id' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_History', 'display_order' => 30, 'default' => 0),
        );
        $this->insertOEElementType($element_types, $event_type);
        $this->createOETable('et_ophciexamination_examinationrisk', array(
            'id' => 'pk',
            'event_id' => 'int(10) unsigned NOT NULL',
            'anticoagulant' => 'tinyint(1) unsigned not null',
            'alphablocker' => 'tinyint(1) unsigned not null',
            'KEY `et_ophciexamination_examinationrisk_ev_fk` (`event_id`)',
            'CONSTRAINT `et_ophciexamination_examinationrisk_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
        ), true);
    }

    public function down()
    {
        $this->dropOETable('et_ophciexamination_examinationrisk', true);
        $this->delete(
            'element_type',
            'class_name = :class',
            array(':class' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_HistoryRisk')
        );
    }
}
