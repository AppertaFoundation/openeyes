<?php

class m190517_131302_add_optometrist_to_enum extends CDbMigration
{
    public function up()
    {
        $this->alterColumn('document_target', 'contact_type',
            "enum('PATIENT','GP','DRSS','LEGACY','OTHER', 'INTERNALREFERRAL', 'OPTOMETRIST') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'OTHER'");
    }

    public function down()
    {
        $this->alterColumn('document_target', 'contact_type',
            "enum('PATIENT','GP','DRSS','LEGACY','OTHER', 'INTERNALREFERRAL') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'OTHER'");
    }
}