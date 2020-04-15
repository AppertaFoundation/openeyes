<?php

class m170502_184511_tags_feature extends OEMigration
{
    public function safeUp()
    {
        $this->createOETable('tag', array(
            'id' => 'pk',
            'name' => 'string NOT NULL',
            'active' => 'TINYINT NOT NULL DEFAULT 1'
        ), true);

        $this->createIndex('idx_tag_name', 'tag', 'name', true);

        $this->execute("INSERT IGNORE INTO `tag` (`id`, `name`) VALUES (1, 'Preservative free');");

        $this->createOETable('drug_tag', array(
            'id' => 'pk',
            'drug_id' => 'int(10) unsigned not null',
            'tag_id' => 'int(11) not null',
            'constraint drug_tags_ti_fk foreign key (tag_id) references tag (id)',
            'constraint drug_tags_di_fk foreign key (drug_id) references drug (id)'
        ), true);

        $this->addColumn('drug_type', 'tag_id', 'int(11) null');
        $this->addColumn('drug_type_version', 'tag_id', 'int(11) null');

        $this->execute("ALTER TABLE `drug_type` ADD CONSTRAINT drug_type_tags_fk foreign key (tag_id) references tag (id)");

        $this->execute("INSERT IGNORE INTO `tag` (`name`)
                        SELECT `name` FROM `drug_type`;");

        $this->execute("INSERT INTO `drug_tag` (`drug_id`, `tag_id`)
                        SELECT `drug`.`id`, `tag`.`id`
                         FROM `drug`
                         LEFT JOIN `drug_type` ON `drug_type`.`id` = `drug`.`type_id`
                         LEFT JOIN `tag` ON `tag`.name = `drug_type`.`name`;");

        $this->execute("INSERT INTO `drug_tag` (`drug_id`, `tag_id`)
                        SELECT `drug`.`id`, 1 FROM `drug` WHERE `preservative_free` = 1;");

        $this->execute("UPDATE drug_type, `tag`, drug_tag, drug SET drug_type.tag_id = tag.id 
                        WHERE tag.id = drug_tag.tag_id
                        AND drug.type_id = drug_type.id
                        AND tag.name = drug_type.name");

        $this->createOETable('medication_drug_tag', array(
            'id' => 'pk',
            'medication_drug_id' => 'int(11) not null',
            'tag_id' => 'int(11) not null',
            'constraint medication_tags_ti_fk foreign key (tag_id) references tag (id)',
            'constraint medication_tags_mi_fk foreign key (medication_drug_id) references medication_drug (id)'
        ), true);

        $this->execute("CREATE VIEW `drug_drug_type` AS SELECT drug.id AS drug_id, drug_type.id AS drug_type_id
                            FROM drug_tag
                            LEFT JOIN drug ON drug.id = drug_tag.drug_id
                            LEFT JOIN drug_type ON drug_type.tag_id = drug_tag.tag_id
                            WHERE drug_type.id IS NOT NULL");

        $this->dropColumn('drug', 'preservative_free');
        $this->dropColumn('drug_version', 'preservative_free');
    }

    public function safeDown()
    {
        $this->dropOETable('drug_tag', true);
        $this->dropOETable('medication_drug_tag', true);
        $this->execute('ALTER TABLE openeyes.drug_type DROP FOREIGN KEY drug_type_tags_fk;');
        $this->execute('DROP INDEX drug_type_tags_fk ON openeyes.drug_type;');
        $this->execute('ALTER TABLE openeyes.drug_type DROP tag_id;');
        $this->dropColumn('drug_type_version', 'tag_id');
        $this->dropOETable('tag', true);
        $this->execute("DROP VIEW IF EXISTS `drug_drug_type`");
        $this->addColumn('drug', 'preservative_free', 'TINYINT unsigned NOT NULL DEFAULT 0');
        $this->addColumn('drug_version', 'preservative_free', 'TINYINT unsigned NOT NULL DEFAULT 0');
    }

}
