<?php

class m210408_235109_add_institution_column_to_xpath_remap_table extends OEMigration
{
    public function safeUp()
    {
        $this->addOEColumn('pasapi_xpath_remap', 'institution_id', 'int(10) unsigned AFTER xpath', true);
        $this->addForeignKey('pasapi_xpath_remap_institution_fk', 'pasapi_xpath_remap', 'institution_id', 'institution', 'id');
    }

    public function safeDown()
    {
        $this->dropForeignKey('pasapi_xpath_remap_institution_fk', 'pasapi_xpath_remap');
        $this->dropOEColumn('pasapi_xpath_remap', 'institution_id', true);
    }
}
