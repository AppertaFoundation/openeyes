<?php

class m200223_190045_fix_columns_with_no_defaults_that_arent_set extends OEMigration
{
    public function up()
    {
        $this->alterOEColumn('et_ophtroperationbooking_operation', 'cancellation_comment', 'VARCHAR(200) NULL', true);
        $this->alterOEColumn('ophtroperationbooking_operation_booking', 'cancellation_comment', 'VARCHAR(200) NULL', true);
    }

    public function down()
    {
        $this->alterOEColumn('et_ophtroperationbooking_operation', 'cancellation_comment', 'VARCHAR(200) NOT NULL', true);
        $this->alterOEColumn('ophtroperationbooking_operation_booking', 'cancellation_comment', 'VARCHAR(200) NOT NULL', true);
    }
}
