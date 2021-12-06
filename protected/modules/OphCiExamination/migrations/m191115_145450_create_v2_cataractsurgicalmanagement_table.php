<?php

class m191115_145450_create_v2_cataractsurgicalmanagement_table extends OEMigration
{
    public function safeUp()
    {
        $this->execute('RENAME TABLE et_ophciexamination_cataractsurgicalmanagement TO et_ophciexamination_cataractsurgicalmanagement_archive');
        $this->execute('RENAME TABLE et_ophciexamination_cataractsurgicalmanagement_version TO et_ophciexamination_cataractsurgicalmanagement_archive_version');
        $this->createOETable(
            'et_ophciexamination_cataractsurgicalmanagement',
            [
                'id' => 'pk',
                'event_id' => 'INT(10) UNSIGNED NOT NULL DEFAULT 0',
                'eye_id' => 'INT(10) UNSIGNED NOT NULL DEFAULT 3',
                'left_target_postop_refraction' => 'DECIMAL(5,2) NULL DEFAULT NULL',
                'right_target_postop_refraction' => 'DECIMAL(5,2) NULL DEFAULT NULL',
                'left_correction_discussed' => 'TINYINT(1) NULL DEFAULT NULL',
                'right_correction_discussed' => 'TINYINT(1) NULL DEFAULT NULL',
                'left_refraction_category' => 'TINYINT(1) NULL DEFAULT NULL',
                'right_refraction_category' => 'TINYINT(1) NULL DEFAULT NULL',
                'left_eye_id' => 'INT(10) UNSIGNED NOT NULL',
                'right_eye_id' => 'INT(10) UNSIGNED NOT NULL',
                'left_reason_for_surgery_id' => 'INT(10) UNSIGNED NULL DEFAULT NULL',
                'right_reason_for_surgery_id' => 'INT(10) UNSIGNED NULL DEFAULT NULL',
                'left_notes' => 'text',
                'right_notes' => 'text',
            ],
            true
        );

        $this->addForeignKey(
            'fk_ophciexamination_csm_eye',
            'et_ophciexamination_cataractsurgicalmanagement',
            'eye_id',
            'eye',
            'id'
        );

        $this->addForeignKey(
            'fk_ophciexamination_csm_lefteye',
            'et_ophciexamination_cataractsurgicalmanagement',
            'left_eye_id',
            'ophciexamination_cataractsurgicalmanagement_eye',
            'id'
        );

        $this->addForeignKey(
            'fk_ophciexamination_csm_righteye',
            'et_ophciexamination_cataractsurgicalmanagement',
            'right_eye_id',
            'ophciexamination_cataractsurgicalmanagement_eye',
            'id'
        );

        $this->update(
            'element_type',
            [
                'element_group_id' => null,
                'class_name' => 'OEModule\\OphCiExamination\\models\\Element_OphCiExamination_CataractSurgicalManagement_Archive'
            ],
            'name = "Cataract Surgical Management"'
        );

        $this->createElementType('OphCiExamination', 'Cataract Surgical Management', [
            'class_name' => 'OEModule\\OphCiExamination\\models\\Element_OphCiExamination_CataractSurgicalManagement',
            'display_order' => 440,
            'default' => 1,
            'required' => 0,
            'group_name' => 'Clinical Management'
        ]);

        return true;
    }

    public function safeDown()
    {
        $this->dropForeignKey(
            'fk_ophciexamination_csm_lefteye',
            'et_ophciexamination_cataractsurgicalmanagement'
        );
        $this->dropForeignKey(
            'fk_ophciexamination_csm_righteye',
            'et_ophciexamination_cataractsurgicalmanagement'
        );
        $this->dropOETable('et_ophciexamination_cataractsurgicalmanagement', true);
        $this->execute('RENAME TABLE et_ophciexamination_cataractsurgicalmanagement_archive TO et_ophciexamination_cataractsurgicalmanagement');
        $this->execute('RENAME TABLE et_ophciexamination_cataractsurgicalmanagement_archive_version TO et_ophciexamination_cataractsurgicalmanagement_version');
        return true;
    }
}
