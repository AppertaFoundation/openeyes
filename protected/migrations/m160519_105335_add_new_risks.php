<?php

class m160519_105335_add_new_risks extends CDbMigration
{
    public function up()
    {
        $this->createIndex('risk_name_unique', 'risk', 'name', true);

        $newRisks = array(
            array('name' => 'Pregnant'),
            array('name' => 'Alpha blockers'),
            array('name' => 'Anticoagulants'),
            array('name' => 'Unfit for surgery - DO NOT BOOK'),
        );

        foreach ($newRisks as $riskRow) {
            $this->insert('risk', $riskRow);
        }
    }

    public function down()
    {
        $this->delete('risk', 'name = "Pregnant"');
        $this->delete('risk', 'name = "Alpha blockers"');
        $this->delete('risk', 'name = "Anticoagulants"');
        $this->delete('risk', 'name = "Unfit for surgery - DO NOT BOOK"');

        $this->dropIndex('risk_name_unique', 'risk');
    }

    /*
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
