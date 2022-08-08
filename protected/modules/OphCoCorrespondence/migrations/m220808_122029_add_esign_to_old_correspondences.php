<?php

class m220808_122029_add_esign_to_old_correspondences extends OEMigration
{
    public function up()
    {
        $this->execute('INSERT IGNORE INTO et_ophcocorrespondence_esign (event_id)
                        SELECT DISTINCT event_id
                        FROM et_ophcocorrespondence_letter
                        WHERE draft=1
                        AND event_id NOT IN (SELECT event_id
                                            FROM et_ophcocorrespondence_esign);');
    }

    public function down()
    {
        echo "m220808_122029_add_esign_to_old_correspondences does not support migration down.\n";
        return false;
    }
}
