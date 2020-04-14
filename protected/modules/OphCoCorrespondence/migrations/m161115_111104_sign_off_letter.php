<?php

class m161115_111104_sign_off_letter extends OEMigration
{
    public function up()
    {
        $this->addColumn('et_ophcocorrespondence_letter', 'is_signed_off', 'tinyint(1)');
        $this->addColumn('et_ophcocorrespondence_letter_version', 'is_signed_off', 'tinyint(1)');
    }

    public function down()
    {
        $this->dropColumn('et_ophcocorrespondence_letter', 'is_signed_off');
        $this->dropColumn('et_ophcocorrespondence_letter_version', 'is_signed_off');
    }
}
