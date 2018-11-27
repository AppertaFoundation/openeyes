<?php

class m181127_091916_examination_delete_allergy_set_assignment extends \OEMigration
{
    public function up()
    {
        $this->addColumn('ophciexamination_allergy_set_entry_version', 'set_id', 'int(11)');
        $this->addColumn('ophciexamination_allergy_set_entry', 'set_id', 'int(11)');
        $this->addForeignKey('allergy_set_entry_allergy_set',
            'ophciexamination_allergy_set_entry',
            'set_id',
            'ophciexamination_allergy_set',
            'id');

        $dataProvider = new CActiveDataProvider('OEModule\OphCiExamination\models\OphCiExaminationAllergySetAssignment');
        $iterator = new CDataProviderIterator($dataProvider);

        foreach ($iterator as $assignment) {
            $allergy_set_entry = \OEModule\OphCiExamination\models\OphCiExaminationAllergySetEntry::model()->findByPk($assignment->ophciexamination_allergy_entry_id);
            $allergy_set_entry->set_id = $assignment->allergy_set_id;
            $allergy_set_entry->save();
        }

        $this->dropForeignKey('ophciexamination_allergy_set_assignment_allergy_e', 'ophciexamination_allergy_set_assignment');
        $this->dropForeignKey('ophciexamination_allergy_set_assignment_set', 'ophciexamination_allergy_set_assignment');
        $this->dropTable('ophciexamination_allergy_set_assignment');
        $this->dropTable('ophciexamination_allergy_set_assignment_version');
    }

    public function down()
    {
        $this->createOETable('ophciexamination_allergy_set_assignment',
            array(
                'id' => 'pk',
                'ophciexamination_allergy_entry_id' => 'int(11)',
                'allergy_set_id' => 'int(11)',
            ), true
        );

        $this->dropForeignKey('allergy_set_entry_allergy_set', 'ophciexamination_allergy_set_entry');

        $this->addForeignKey('ophciexamination_allergy_set_assignment_allergy_e', 'ophciexamination_allergy_set_assignment', 'ophciexamination_allergy_entry_id', 'ophciexamination_allergy_set_entry', 'id');
        $this->addForeignKey('ophciexamination_allergy_set_assignment_set', 'ophciexamination_allergy_set_assignment', 'allergy_set_id', 'ophciexamination_allergy_set', 'id');

        $dataProvider = new CActiveDataProvider('OEModule\OphCiExamination\models\OphCiExaminationAllergySetEntry');
        $iterator = new CDataProviderIterator($dataProvider);

        foreach ($iterator as $allergy_entry) {
            $allergy_set_assignment = new \OEModule\OphCiExamination\models\OphCiExaminationAllergySetAssignment();
            $allergy_set_assignment->ophciexamination_allergy_entry_id = $allergy_entry->id;
            $allergy_set_assignment->allergy_set_id = $allergy_entry->allergy_set_id;
            $allergy_set_assignment->save();
        }

        $this->dropColumn('ophciexamination_allergy_set_entry_version', '_set_id');
        $this->dropColumn('ophciexamination_allergy_set_entry', 'set_id');
    }
}