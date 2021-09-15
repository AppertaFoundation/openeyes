<?php

class m161102_094326_preferred_language_text extends CDbMigration
{
    public function up()
    {
        $this->addColumn('et_ophcocvi_clericinfo', 'preferred_language_text', 'text');
        $this->addColumn('et_ophcocvi_clericinfo_version', 'preferred_language_text', 'text');
    }

    public function down()
    {
        $this->dropColumn('et_ophcocvi_clericinfo', 'preferred_language_text');
        $this->dropColumn('et_ophcocvi_clericinfo_version', 'preferred_language_text');
    }

}
