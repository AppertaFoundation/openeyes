<?php

class m210329_032827_add_print_authorise_columns extends OEMigration
{
    public function up()
    {
        $this->addOEColumn('et_ophdrprescription_details', 'printed_by_user', 'int(10) unsigned NULL', true);
        $this->addOEColumn('et_ophdrprescription_details', 'printed_date', 'datetime NULL', true);
        $this->addOEColumn('et_ophdrprescription_details', 'authorised_by_user', 'int(10) unsigned NULL', true);
        $this->addOEColumn('et_ophdrprescription_details', 'authorised_date', 'datetime NULL', true);

        $this->addForeignKey(
            'et_ophdrprescription_details_printed_by_user_fk',
            'et_ophdrprescription_details',
            'printed_by_user',
            'user',
            'id'
        );

        $this->addForeignKey(
            'et_ophdrprescription_details_authorised_by_user_fk',
            'et_ophdrprescription_details',
            'authorised_by_user',
            'user',
            'id'
        );
    }

    public function down()
    {
        $this->dropOEColumn('et_ophdrprescription_details', 'authorised_date', true);
        $this->dropOEColumn('et_ophdrprescription_details', 'authorised_by_user', true);
        $this->dropOEColumn('et_ophdrprescription_details', 'printed_date', true);
        $this->dropOEColumn('et_ophdrprescription_details', 'printed_by_user', true);
    }
}
