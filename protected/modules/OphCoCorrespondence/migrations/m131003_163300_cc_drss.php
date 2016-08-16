<?php

class m131003_163300_cc_drss extends CDbMigration
{
    public function up()
    {
        $this->addColumn('et_ophcocorrespondence_letter_macro', 'cc_drss', 'tinyint(1) default 0');
        $this->addColumn('et_ophcocorrespondence_subspecialty_letter_macro', 'cc_drss', 'tinyint(1) default 0');
        $this->addColumn('et_ophcocorrespondence_firm_letter_macro', 'cc_drss', 'tinyint(1) default 0');
    }

    public function down()
    {
        $this->dropColumn('et_ophcocorrespondence_letter_macro', 'cc_drss');
        $this->dropColumn('et_ophcocorrespondence_subspecialty_letter_macro', 'cc_drss');
        $this->dropColumn('et_ophcocorrespondence_firm_letter_macro', 'cc_drss');
    }
}
