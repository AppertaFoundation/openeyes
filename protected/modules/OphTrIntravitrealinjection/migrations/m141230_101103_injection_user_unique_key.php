<?php

class m141230_101103_injection_user_unique_key extends CDbMigration
{
    public function up()
    {
        $this->createIndex('ophtrintravitinjection_injectionuser_user_id_unique_fk', 'ophtrintravitinjection_injectionuser', 'user_id', true);
    }

    public function down()
    {
        $this->dropIndex('ophtrintravitinjection_injectionuser_user_id_unique_fk', 'ophtrintravitinjection_injectionuser');
    }
}
