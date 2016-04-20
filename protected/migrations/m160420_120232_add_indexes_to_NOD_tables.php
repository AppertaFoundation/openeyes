<?php

class m160420_120232_add_indexes_to_NOD_tables extends CDbMigration
{
	public function up()
	{
            //EpisodeDrug
            $this->createIndex('medication_prescription_item_id', 'medication', 'prescription_item_id');
            $this->createIndex('medication_last_modified_date', 'medication', 'last_modified_date');
	}

	public function down()
	{
            //EpisodeDrug
            $this->dropIndex('medication_prescription_item_id', 'medication');
            $this->dropIndex('medication_last_modified_date', 'medication');
	}
}