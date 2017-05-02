<?php

class m170502_184511_tags_feature extends OEMigration
{
	public function safeUp()
	{
	    $this->createOETable('tag', array(
	        'id' => 'pk',
            'name' => 'string NOT NULL'
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


        $this->execute("INSERT IGNORE INTO `tag` (`name`)
                        SELECT `name` FROM `drug_type`;");

        $this->execute("INSERT INTO `drug_tag` (`drug_id`, `tag_id`)
                        SELECT `drug`.`id`, `tag`.`id`
                         FROM `drug`
                         LEFT JOIN `drug_type` ON `drug_type`.`id` = `drug`.`type_id`
                         LEFT JOIN `tag` ON `tag`.name = `drug_type`.`name`;");

        $this->execute("INSERT INTO `drug_tag` (`drug_id`, `tag_id`)
                        SELECT `drug`.`id`, 1 FROM `drug` WHERE `preservative_free` = 1;");

        $this->createOETable('medication_tag', array(
            'id' => 'pk',
            'medication_id' => 'int(10) unsigned not null',
            'tag_id' => 'int(11) not null',
            'constraint medication_tags_ti_fk foreign key (tag_id) references tag (id)',
            'constraint medication_tags_mi_fk foreign key (medication_id) references medication (id)'
        ), true);


	}

	public function safeDown()
	{
        $this->dropOETable('drug_tag', true);
        $this->dropOETable('medication_tag', true);
        $this->dropOETable('tag', true);
	}

}