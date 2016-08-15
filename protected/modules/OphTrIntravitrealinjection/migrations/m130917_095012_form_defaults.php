<?php

class m130917_095012_form_defaults extends CDbMigration
{
    public function up()
    {
        $this->addColumn('ophtrintravitinjection_anaesthetictype', 'is_default', 'boolean default false');
        $this->addColumn('ophtrintravitinjection_anaestheticagent', 'is_default', 'boolean default false');
        $this->addColumn('ophtrintravitinjection_antiseptic_drug', 'is_default', 'boolean default false');
        $this->addColumn('ophtrintravitinjection_skin_drug', 'is_default', 'boolean default false');
    }

    public function down()
    {
        $this->dropColumn('ophtrintravitinjection_skin_drug', 'is_default');
        $this->dropColumn('ophtrintravitinjection_antiseptic_drug', 'is_default');
        $this->dropColumn('ophtrintravitinjection_anaestheticagent', 'is_default');
        $this->dropColumn('ophtrintravitinjection_anaesthetictype', 'is_default');
    }

    /*
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
