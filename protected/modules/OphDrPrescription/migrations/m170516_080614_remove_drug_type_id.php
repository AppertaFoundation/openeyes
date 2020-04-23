<?php

class m170516_080614_remove_drug_type_id extends CDbMigration
{
    public function up()
    {
        $this->execute("ALTER TABLE `drug` DROP FOREIGN KEY `drug_type_id_fk`;");
        $this->dropColumn('drug', 'type_id');
        $this->dropColumn('drug_version', 'type_id');
    }

    public function down()
    {
        $this->addColumn('drug', 'type_id', 'int(10) unsigned not null default 1');
        $this->execute("ALTER TABLE `drug` ADD CONSTRAINT `drug_type_id_fk` FOREIGN KEY (`type_id`) REFERENCES `drug_type` (`id`)");
        $this->addColumn('drug_version', 'type_id', 'int(10) unsigned not null default 1');
    }

}
