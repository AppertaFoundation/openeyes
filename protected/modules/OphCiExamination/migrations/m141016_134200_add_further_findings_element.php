<?php

class m141016_134200_add_further_findings_element extends OEMigration
{
    public function up()
    {
        $event_type_id = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name = :class_name', array(':class_name' => 'OphCiExamination'))->queryScalar();

        $element_types = array(
            'OEModule\OphCiExamination\models\Element_OphCiExamination_FurtherFindings' => array('name' => 'Further Findings', 'parent_element_type_id' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_Diagnoses'),
        );
        $this->insertOEElementType($element_types, $event_type_id);

        $this->createOETable(
            'ophciexamination_further_findings',
            array('id' => 'pk', 'name' => 'varchar(255)', 'display_order' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'active' => 'tinyint(1) unsigned not null DEFAULT 1',
            ),
            true
        );

        $this->createIndex(
            'ophciexamination_further_findings_unique_name',
            'ophciexamination_further_findings',
            'name',
            true
        );

        $this->createOETable('et_ophciexamination_further_findings', array(
            'id' => 'pk',
            'event_id' => 'int(10) unsigned NOT NULL',
            'KEY `et_ophciexam_overallmanagementplan_ev_fk` (`event_id`)',
            'CONSTRAINT `et_ophciexamination_further_findings_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
        ), true);

        $this->createOETable(
            'ophciexamination_further_findings_assignment',
            array('id' => 'pk', 'element_id' => 'int(11) NOT NULL',
                'further_finding_id' => 'int(11) NOT NULL',
                'CONSTRAINT `ophciexamination_further_findings_assign_e_id_fk` FOREIGN KEY (`element_id`) REFERENCES `et_ophciexamination_further_findings` (`id`)',
                'CONSTRAINT `ophciexamination_further_findings_assign_f_id_fk` FOREIGN KEY (`further_finding_id`) REFERENCES `ophciexamination_further_findings` (`id`)',
            ),
            true
        );

        $this->createOETable(
            'ophciexamination_further_findings_subspec_assignment',
            array('id' => 'pk', 'further_finding_id' => 'int(11) NOT NULL', 'subspecialty_id' => 'int(10) unsigned NOT NULL',
                'CONSTRAINT `ophciexamination_further_findings_subspec_f_id_fk` FOREIGN KEY (`further_finding_id`) REFERENCES `ophciexamination_further_findings` (`id`)',
                'CONSTRAINT `ophciexamination_further_findings_subspec_s_id_fk` FOREIGN KEY (`subspecialty_id`) REFERENCES `subspecialty` (`id`)',
            ),
            true
        );

        $migrations_path = dirname(__FILE__);
        $this->initialiseData($migrations_path);
    }

    public function down()
    {
        $tables = array(
            'ophciexamination_further_findings_subspec_assignment',
            'ophciexamination_further_findings_assignment',
            'et_ophciexamination_further_findings',
            'ophciexamination_further_findings',
        );
        foreach ($tables as $table) {
            $this->dropTable($table);
            $this->dropTable($table.'_version');
        }

        $this->delete(
            'element_type',
            'class_name = :class',
            array(':class' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_FurtherFindings')
        );
    }
}
