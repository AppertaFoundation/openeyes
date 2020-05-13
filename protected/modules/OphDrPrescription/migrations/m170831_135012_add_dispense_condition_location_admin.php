<?php

class m170831_135012_add_dispense_condition_location_admin extends OEMigration
{
    public function up()
    {
        $this->addColumn('drug_set_item', 'dispense_condition_id', 'int(11)');
        $this->addColumn('drug_set_item_version', 'dispense_condition_id', 'int(11)');
        $this->addColumn('drug_set_item', 'dispense_location_id', 'int(11)');
        $this->addColumn('drug_set_item_version', 'dispense_location_id', 'int(11)');

        $this->addForeignKey(
            'fk_drug_set_item_dispense_condition_id',
            'drug_set_item',
            'dispense_condition_id',
            'ophdrprescription_dispense_condition',
            'id'
        );

        $this->addForeignKey(
            'fk_drug_set_item_dispense_location_id',
            'drug_set_item',
            'dispense_location_id',
            'ophdrprescription_dispense_location',
            'id'
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk_drug_set_item_dispense_condition_id', 'drug_set_item');
        $this->dropForeignKey('fk_drug_set_item_dispense_location_id', 'drug_set_item');

        $this->dropColumn('drug_set_item', 'dispense_condition_id');
        $this->dropColumn('drug_set_item_version', 'dispense_condition_id');
        $this->dropColumn('drug_set_item', 'dispense_location_id');
        $this->dropColumn('drug_set_item_version', 'dispense_location_id');

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
