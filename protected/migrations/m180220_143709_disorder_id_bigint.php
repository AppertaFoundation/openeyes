<?php

class m180220_143709_disorder_id_bigint extends CDbMigration
{
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{

	    $this->dropFKs();

        //alter columns
        $this->alterColumn('disorder','id','BIGINT UNSIGNED NOT NULL');

        $this->alterColumn('common_ophthalmic_disorder','disorder_id','BIGINT UNSIGNED NULL');
        $this->alterColumn('common_ophthalmic_disorder','alternate_disorder_id','BIGINT UNSIGNED NULL');
        $this->alterColumn('common_systemic_disorder','disorder_id','BIGINT UNSIGNED NOT NULL');
        $this->alterColumn('disorder_tree','disorder_id','BIGINT UNSIGNED NOT NULL');
        $this->alterColumn('episode','disorder_id','BIGINT UNSIGNED NULL');

        $this->alterColumn('secondary_diagnosis','disorder_id','BIGINT UNSIGNED NOT NULL');
        $this->alterColumn('secondaryto_common_oph_disorder','disorder_id','BIGINT UNSIGNED NULL');

        if(\Yii::app()->getModule('OphCiExamination')){
            $this->alterColumn('et_ophciexamination_injectionmanagementcomplex','left_diagnosis1_id','BIGINT UNSIGNED NULL');
            $this->alterColumn('et_ophciexamination_injectionmanagementcomplex','left_diagnosis2_id','BIGINT UNSIGNED NULL');
            $this->alterColumn('et_ophciexamination_injectionmanagementcomplex','right_diagnosis1_id','BIGINT UNSIGNED NULL');
            $this->alterColumn('et_ophciexamination_injectionmanagementcomplex','right_diagnosis2_id','BIGINT UNSIGNED NULL');
            $this->alterColumn('ophciexamination_diagnosis','disorder_id','BIGINT UNSIGNED NOT NULL');
            $this->alterColumn('ophciexamination_injectmanagecomplex_question','disorder_id','BIGINT UNSIGNED NOT NULL');
            $this->alterColumn('ophciexamination_systemic_diagnoses_diagnosis','disorder_id','BIGINT UNSIGNED NOT NULL');
        }

        if(\Yii::app()->getModule('OphCoTherapyapplication')){
            $this->alterColumn('et_ophcotherapya_therapydiag','left_diagnosis1_id','BIGINT UNSIGNED NULL');
            $this->alterColumn('et_ophcotherapya_therapydiag','left_diagnosis2_id','BIGINT UNSIGNED NULL');
            $this->alterColumn('et_ophcotherapya_therapydiag','right_diagnosis1_id','BIGINT UNSIGNED NULL');
            $this->alterColumn('et_ophcotherapya_therapydiag','right_diagnosis2_id','BIGINT UNSIGNED NULL');
            $this->alterColumn('ophcotherapya_therapydisorder','disorder_id','BIGINT UNSIGNED NOT NULL');
        }

        if(\Yii::app()->getModule('OphTrOperationbooking')) {
            $this->alterColumn('et_ophtroperationbooking_diagnosis', 'disorder_id', 'BIGINT UNSIGNED NOT NULL');
        }

        if(\Yii::app()->getModule('Genetics')){

            if (\Yii::app()->db->schema->getTable('pedigree',true) !== null) {
                $this->alterColumn('pedigree','disorder_id','BIGINT UNSIGNED NULL');
            }

            if (\Yii::app()->db->schema->getTable('genetics_patient_diagnosis',true) !== null) {
                $this->alterColumn('genetics_patient_diagnosis','disorder_id','BIGINT UNSIGNED NULL');
            }
        }

        if(\Yii::app()->getModule('OphCoCvi')) {
            $this->alterColumn('ophcocvi_clinicinfo_disorder', 'disorder_id', 'BIGINT UNSIGNED NULL');
        }

        $this->addFKs();
	}

	public function safeDown()
	{
        $this->dropFKs();

        //alter columns

        $this->alterColumn('disorder','id','INT(10) UNSIGNED NOT NULL');

        $this->alterColumn('common_ophthalmic_disorder','disorder_id','INT(10) UNSIGNED NULL');
        $this->alterColumn('common_ophthalmic_disorder','alternate_disorder_id','INT(10) UNSIGNED NULL');
        $this->alterColumn('common_systemic_disorder','disorder_id','INT(10) UNSIGNED NOT NULL');
        $this->alterColumn('disorder_tree','disorder_id','INT(10) UNSIGNED NOT NULL');
        $this->alterColumn('episode','disorder_id','INT(10) UNSIGNED NULL');
        $this->alterColumn('secondary_diagnosis','disorder_id','INT(10) UNSIGNED NOT NULL');
        $this->alterColumn('secondaryto_common_oph_disorder','disorder_id','INT(10) UNSIGNED NULL');

        if(\Yii::app()->getModule('OphCiExamination')) {
            $this->alterColumn('et_ophciexamination_injectionmanagementcomplex', 'left_diagnosis1_id', 'INT(10) UNSIGNED NULL');
            $this->alterColumn('et_ophciexamination_injectionmanagementcomplex', 'left_diagnosis2_id', 'INT(10) UNSIGNED NULL');
            $this->alterColumn('et_ophciexamination_injectionmanagementcomplex', 'right_diagnosis1_id', 'INT(10) UNSIGNED NULL');
            $this->alterColumn('et_ophciexamination_injectionmanagementcomplex', 'right_diagnosis2_id', 'INT(10) UNSIGNED NULL');
            $this->alterColumn('ophciexamination_diagnosis','disorder_id','INT(10) UNSIGNED NOT NULL');
            $this->alterColumn('ophciexamination_injectmanagecomplex_question','disorder_id','INT(10) UNSIGNED NOT NULL');
            $this->alterColumn('ophciexamination_systemic_diagnoses_diagnosis','disorder_id','INT(10) UNSIGNED NOT NULL');
        }

        if(\Yii::app()->getModule('OphCoTherapyapplication')) {
            $this->alterColumn('et_ophcotherapya_therapydiag', 'left_diagnosis1_id', 'INT(10) UNSIGNED NULL');
            $this->alterColumn('et_ophcotherapya_therapydiag', 'left_diagnosis2_id', 'INT(10) UNSIGNED NULL');
            $this->alterColumn('et_ophcotherapya_therapydiag', 'right_diagnosis1_id', 'INT(10) UNSIGNED NULL');
            $this->alterColumn('et_ophcotherapya_therapydiag', 'right_diagnosis2_id', 'INT(10) UNSIGNED NULL');
            $this->alterColumn('ophcotherapya_therapydisorder','disorder_id','INT(10) UNSIGNED NOT NULL');
        }

        if(\Yii::app()->getModule('OphTrOperationbooking')) {
            $this->alterColumn('et_ophtroperationbooking_diagnosis', 'disorder_id', 'INT(10) UNSIGNED NOT NULL');
        }

        if(\Yii::app()->getModule('Genetics')) {

            if (\Yii::app()->db->schema->getTable('pedigree',true) !== null) {
                $this->alterColumn('pedigree', 'disorder_id', 'INT(10) UNSIGNED NULL');
            }
            if (\Yii::app()->db->schema->getTable('genetics_patient_diagnosis',true) !== null) {
                $this->alterColumn('genetics_patient_diagnosis', 'disorder_id', 'INT(10) UNSIGNED NULL');
            }
        }

        if(\Yii::app()->getModule('OphCoCvi')) {
            $this->alterColumn('ophcocvi_clinicinfo_disorder', 'disorder_id', 'INT(10) UNSIGNED NULL');
        }

        $this->addFKs();
	}

    /**
     * Drop FKs that referencing to disorder table
     */
	private function dropFKs()
    {
        // drop all FKs point to disorder table

        $this->dropForeignKey('common_ophthalmic_disorder_ibfk_1','common_ophthalmic_disorder');
        $this->dropForeignKey('common_ophthalmic_disorder_ibfk_3','common_ophthalmic_disorder');
        $this->dropForeignKey('common_systemic_disorder_ibfk_1','common_systemic_disorder');
        $this->dropForeignKey('episode_disorder_id_fk','episode');
        $this->dropForeignKey('secondary_diagnosis_disorder_id_fk','secondary_diagnosis');
        $this->dropForeignKey('secondaryto_common_oph_disorder_did_fk','secondaryto_common_oph_disorder');

        $sql = "SELECT TABLE_NAME,COLUMN_NAME,CONSTRAINT_NAME, REFERENCED_TABLE_NAME,REFERENCED_COLUMN_NAME
                            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                            WHERE REFERENCED_TABLE_SCHEMA = (SELECT DATABASE()) AND
                            REFERENCED_TABLE_NAME = 'disorder' AND
                            TABLE_NAME = 'disorder_tree'";

        $foreign_key = $this->dbConnection->createCommand($sql)->queryRow();

        if($foreign_key){
            $this->dropForeignKey($foreign_key['CONSTRAINT_NAME'],$foreign_key['TABLE_NAME']);
        }

        if(\Yii::app()->getModule('OphCiExamination')) {
            $this->dropForeignKey('et_ophciexamination_injectionmanagementcomplex_ldiag1_fk', 'et_ophciexamination_injectionmanagementcomplex');
            $this->dropForeignKey('et_ophciexamination_injectionmanagementcomplex_ldiag2_fk', 'et_ophciexamination_injectionmanagementcomplex');
            $this->dropForeignKey('et_ophciexamination_injectionmanagementcomplex_rdiag1_fk', 'et_ophciexamination_injectionmanagementcomplex');
            $this->dropForeignKey('et_ophciexamination_injectionmanagementcomplex_rdiag2_fk', 'et_ophciexamination_injectionmanagementcomplex');
            $this->dropForeignKey('ophciexamination_diagnosis_disorder_id_fk','ophciexamination_diagnosis');
            $this->dropForeignKey('ophciexamination_injectmanagecomplex_question_disorder_fk','ophciexamination_injectmanagecomplex_question');
            $this->dropForeignKey('ophciexamination_sysdiag_dia_dis_fk','ophciexamination_systemic_diagnoses_diagnosis');
        }

        if(\Yii::app()->getModule('OphCoTherapyapplication')) {
            $this->dropForeignKey('et_ophcotherapya_therapydiag_ldiagnosis1_id_fk', 'et_ophcotherapya_therapydiag');
            $this->dropForeignKey('et_ophcotherapya_therapydiag_ldiagnosis2_id_fk', 'et_ophcotherapya_therapydiag');
            $this->dropForeignKey('et_ophcotherapya_therapydiag_rdiagnosis1_id_fk', 'et_ophcotherapya_therapydiag');
            $this->dropForeignKey('et_ophcotherapya_therapydiag_rdiagnosis2_id_fk', 'et_ophcotherapya_therapydiag');
            $this->dropForeignKey('ophcotherapya_therapydisorder_di_fk', 'ophcotherapya_therapydisorder');
        }

        if(\Yii::app()->getModule('Genetics')) {

            if($foreign_key = $this->getFK('pedigree')){
                $this->dropForeignKey($foreign_key['CONSTRAINT_NAME'],$foreign_key['TABLE_NAME']);
            }

            if($foreign_key = $this->getFK('genetics_patient_diagnosis')){
                $this->dropForeignKey($foreign_key['CONSTRAINT_NAME'],$foreign_key['TABLE_NAME']);
            }
        }

        if(\Yii::app()->getModule('OphCoCvi')) {
            $this->dropForeignKey('ophcocvi_clinicinfo_disorder_disorder_fk', 'ophcocvi_clinicinfo_disorder');
        }

        if(\Yii::app()->getModule('OphTrOperationbooking')) {
            if($foreign_key = $this->getFK('et_ophtroperationbooking_diagnosis')){
                $this->dropForeignKey($foreign_key['CONSTRAINT_NAME'],$foreign_key['TABLE_NAME']);
            }
        }
    }

    /**
     * Add FKs to disorder reference table
     */
    private function addFKs()
    {
        $this->addForeignKey('common_ophthalmic_disorder_ibfk_1','common_ophthalmic_disorder','disorder_id','disorder','id');
        $this->addForeignKey('common_ophthalmic_disorder_ibfk_3','common_ophthalmic_disorder','alternate_disorder_id','disorder','id');
        $this->addForeignKey('common_systemic_disorder_ibfk_1','common_systemic_disorder','disorder_id','disorder','id');
        $this->addForeignKey('disorder_tree_ibfk_1','disorder_tree','disorder_id','disorder','id');
        $this->addForeignKey('episode_disorder_id_fk','episode','disorder_id','disorder','id');
        $this->addForeignKey('secondary_diagnosis_disorder_id_fk','secondary_diagnosis','disorder_id','disorder','id');
        $this->addForeignKey('secondaryto_common_oph_disorder_did_fk','secondaryto_common_oph_disorder','disorder_id','disorder','id');

        if(\Yii::app()->getModule('OphCiExamination')) {
            $this->addForeignKey('et_ophciexamination_injectionmanagementcomplex_ldiag1_fk', 'et_ophciexamination_injectionmanagementcomplex', 'left_diagnosis1_id', 'disorder', 'id');
            $this->addForeignKey('et_ophciexamination_injectionmanagementcomplex_ldiag2_fk', 'et_ophciexamination_injectionmanagementcomplex', 'left_diagnosis2_id', 'disorder', 'id');
            $this->addForeignKey('et_ophciexamination_injectionmanagementcomplex_rdiag1_fk', 'et_ophciexamination_injectionmanagementcomplex', 'right_diagnosis1_id', 'disorder', 'id');
            $this->addForeignKey('et_ophciexamination_injectionmanagementcomplex_rdiag2_fk', 'et_ophciexamination_injectionmanagementcomplex', 'right_diagnosis2_id', 'disorder', 'id');
            $this->addForeignKey('ophciexamination_diagnosis_disorder_id_fk', 'ophciexamination_diagnosis', 'disorder_id', 'disorder', 'id');
            $this->addForeignKey('ophciexamination_injectmanagecomplex_question_disorder_fk', 'ophciexamination_injectmanagecomplex_question', 'disorder_id', 'disorder', 'id');
            $this->addForeignKey('ophciexamination_sysdiag_dia_dis_fk', 'ophciexamination_systemic_diagnoses_diagnosis', 'disorder_id', 'disorder', 'id');
        }

        if(\Yii::app()->getModule('OphCoTherapyapplication')) {
            $this->addForeignKey('et_ophcotherapya_therapydiag_ldiagnosis1_id_fk', 'et_ophcotherapya_therapydiag', 'left_diagnosis1_id', 'disorder', 'id');
            $this->addForeignKey('et_ophcotherapya_therapydiag_ldiagnosis2_id_fk', 'et_ophcotherapya_therapydiag', 'left_diagnosis2_id', 'disorder', 'id');
            $this->addForeignKey('et_ophcotherapya_therapydiag_rdiagnosis1_id_fk', 'et_ophcotherapya_therapydiag', 'right_diagnosis1_id', 'disorder', 'id');
            $this->addForeignKey('et_ophcotherapya_therapydiag_rdiagnosis2_id_fk', 'et_ophcotherapya_therapydiag', 'right_diagnosis2_id', 'disorder', 'id');
            $this->addForeignKey('ophcotherapya_therapydisorder_di_fk','ophcotherapya_therapydisorder','disorder_id','disorder','id');
        }

        if(\Yii::app()->getModule('OphTrOperationbooking')) {
            $this->addForeignKey('et_ophtroperationbooking_diagnosis_disorder','et_ophtroperationbooking_diagnosis','disorder_id','disorder','id');
        }

        if(\Yii::app()->getModule('Genetics')) {
            if (\Yii::app()->db->schema->getTable('pedigree',true) !== null) {
                $this->addForeignKey('pedigree_disorder_id_fk','pedigree','disorder_id','disorder','id');
            }
            if (\Yii::app()->db->schema->getTable('genetics_patient_diagnosis',true) !== null) {
                $this->addForeignKey('genetics_patient_diagnosis_disorder', 'genetics_patient_diagnosis', 'disorder_id', 'disorder', 'id');
            }
        }

        if(\Yii::app()->getModule('OphCoCvi')) {
            $this->addForeignKey('ophcocvi_clinicinfo_disorder_disorder_fk', 'ophcocvi_clinicinfo_disorder', 'disorder_id', 'disorder', 'id');
        }
    }

    private function getFK($table)
    {
        $sql = "SELECT TABLE_NAME,COLUMN_NAME,CONSTRAINT_NAME, REFERENCED_TABLE_NAME,REFERENCED_COLUMN_NAME
                            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                            WHERE REFERENCED_TABLE_SCHEMA = (SELECT DATABASE()) AND
                            REFERENCED_TABLE_NAME = 'disorder' AND
                            TABLE_NAME = '$table'";

        return $this->dbConnection->createCommand($sql)->queryRow();
    }

}