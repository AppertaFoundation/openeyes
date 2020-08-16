<?php

class m200812_201400_reposition_version_table_fields extends CDbMigration
{
    public function up()
    {
        $this->execute("ALTER TABLE `event_medication_use_version` MODIFY `version_date` datetime AFTER `comments` ");
        $this->execute("ALTER TABLE `event_medication_use_version` MODIFY `version_id` int NOT NULL AUTO_INCREMENT AFTER `version_date` ");
    }

    public function down()
    {
        echo "Doesn't support down";
    }
}
