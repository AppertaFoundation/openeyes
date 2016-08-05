<?php

class m140205_102136_remove_anaesthetic_default extends CDbMigration
{
    public function up()
    {
        $this->alterColumn('et_ophtroperationnote_anaesthetic', 'anaesthetic_type_id', 'int unsigned not null');
    }

    public function down()
    {
        $this->alterColumn('et_ophtroperationnote_anaesthetic', 'anaesthetic_type_id', 'int unsigned not null default 1');
    }
}
