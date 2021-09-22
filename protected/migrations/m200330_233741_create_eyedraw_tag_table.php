<?php

class m200330_233741_create_eyedraw_tag_table extends OEMigration
{
    public function up()
    {
        $this->createOETable('eyedraw_tag', array(
            'id'=>'pk',
            'text'=>'text NOT NULL',
            'snomed_code'=>'int NOT NULL',
        ), true);
    }

    public function down()
    {
        $this->dropOETable('eyedraw_tag', true);
    }
}
