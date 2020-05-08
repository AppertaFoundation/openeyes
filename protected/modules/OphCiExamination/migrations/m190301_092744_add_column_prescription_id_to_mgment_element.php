<?php

class m190301_092744_add_column_prescription_id_to_mgment_element extends OEMigration
{
    public function up()
    {
        $this->addColumn('et_ophciexamination_medicationmanagement', 'prescription_id', 'INT(10) UNSIGNED NULL');
        $this->addColumn('et_ophciexamination_medicationmanagement_version', 'prescription_id', 'INT(10) UNSIGNED NULL');
        $this->addForeignKey(
            'fk_management_presc',
            'et_ophciexamination_medicationmanagement',
            'prescription_id',
            'et_ophdrprescription_details',
            'id'
        );
    }

    public function down()
    {
        $this->dropColumn('et_ophciexamination_medicationmanagement', 'prescription_id');
        $this->dropColumn('et_ophciexamination_medicationmanagement_version', 'prescription_id');
    }
}
