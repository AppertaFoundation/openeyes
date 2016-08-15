<?php

class m160310_121034_link_medication_and_prescription extends OEMigration
{
    public function up()
    {
        $this->addColumn('medication', 'prescription_item_id', 'int(10) UNSIGNED');
        $this->addColumn('medication_version', 'prescription_item_id', 'int(10) UNSIGNED');
    }

    public function down()
    {
        $this->dropColumn('medication_version', 'prescription_item_id');
        $this->dropColumn('medication', 'prescription_item_id');
    }
}
