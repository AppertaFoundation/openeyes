<?php

class m180604_091904_add_column_short_term_to_medications extends CDbMigration
{
    public function up()
    {
        $this->addColumn('medication', 'short_term', 'string');
        $this->addColumn('medication_version', 'short_term', 'string');
        $this->execute("UPDATE medication SET short_term = preferred_term");
    }

    public function down()
    {
        $this->dropColumn('medication', 'short_term');
        $this->dropColumn('medication_version', 'short_term');
    }
}
