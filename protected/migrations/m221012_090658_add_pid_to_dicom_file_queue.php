<?php

class m221012_090658_add_pid_to_dicom_file_queue extends OEMigration
{
	public function safeUp()
	{
        $this->addOEColumn("dicom_file_queue", "pid_type_id", "int(11)");
	}

	public function safeDown()
	{
        $this->dropOEColumn("dicom_file_queue", "pid_type_id");
    }
}