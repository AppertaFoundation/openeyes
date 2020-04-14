<?php

class m181211_142626_add_cost_code_to_firm extends CDbMigration
{
    public function up()
    {
        $this->addColumn('firm', 'cost_code', 'VARCHAR(5)');
        $this->addColumn('firm_version', 'cost_code', 'VARCHAR(5)');
    }

    public function down()
    {
        $this->dropColumn('firm', 'cost_code');
        $this->dropColumn('firm_version', 'cost_code');
    }
}
