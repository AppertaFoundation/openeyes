<?php

class m230712_125900_update_conatct_number_length_to_match_contact_model extends OEMigration
{
    public function safeUp()
    {
        $this->alterOEColumn('et_ophtroperationbooking_contact_details', 'patient_booking_contact_number', 'VARCHAR(20)', true);
        $this->alterOEColumn('et_ophtroperationbooking_contact_details', 'collector_contact_number', 'VARCHAR(20)', true);
    }

    public function safeDown()
    {
        $this->alterOEColumn('et_ophtroperationbooking_contact_details', 'patient_booking_contact_number', 'VARCHAR(15)', true);
        $this->alterOEColumn('et_ophtroperationbooking_contact_details', 'collector_contact_number', 'VARCHAR(15)', true);
    }
}
