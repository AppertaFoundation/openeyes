<?php

class m131104_101736_event_type_OphInDnaextraction extends CDbMigration
{
    public function up()
    {
        if (!$parent_event = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name = :class_name', array(':class_name' => 'OphInDnasample'))->queryRow()) {
            throw new Exception("Parent event type 'OphInDnasample' not found, please install the parent module first.");
        }
        $parent_id = $parent_event['id'];

        if (!$this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name' => 'OphInDnaextraction'))->queryRow()) {
            $group = $this->dbConnection->createCommand()->select('id')->from('event_group')->where('name=:name', array(':name' => 'Investigation events'))->queryRow();
            $this->insert('event_type', array('class_name' => 'OphInDnaextraction', 'name' => 'DNA extraction', 'event_group_id' => $group['id'], 'parent_id' => $parent_id));
        }
        $event_type = $this->dbConnection->createCommand()
            ->select('id')
            ->from('event_type')
            ->where('class_name=:class_name', array(':class_name' => 'OphInDnaextraction'))
            ->queryRow();
        if (!$this->dbConnection->createCommand()->select('id')
            ->from('element_type')
            ->where('name=:name and event_type_id=:eventTypeId', array(':name' => 'DNA extraction', ':eventTypeId' => $event_type['id']))
            ->queryRow()
        ) {
            $this->insert(
                'element_type',
                array('name' => 'DNA extraction', 'class_name' => 'Element_OphInDnaextraction_DnaExtraction', 'event_type_id' => $event_type['id'], 'display_order' => 1)
            );
        }

        $element_type = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('event_type_id=:eventTypeId and name=:name', array(':eventTypeId' => $event_type['id'], ':name' => 'DNA extraction'))->queryRow();

        $this->createTable('et_ophindnaextraction_dnaextraction', array(
                'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
                'event_id' => 'int(10) unsigned NOT NULL',
                'sample_event_id' => 'int(10) unsigned NOT NULL',
                'box' => 'varchar(5) COLLATE utf8_bin',
                'letter' => 'varchar(2) COLLATE utf8_bin',
                'number' => 'varchar(5) COLLATE utf8_bin',
                'orientry' => 'varchar(8) COLLATE utf8_bin',
                'extracted_date' => 'date DEFAULT NULL',
                'extracted_by' => 'varchar(255) COLLATE utf8_bin',
                'comments' => 'text COLLATE utf8_bin',
                'dna_concentration' => 'float NULL',
                'volume' => 'int(10) unsigned NULL',
                'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'PRIMARY KEY (`id`)',
                'KEY `et_ophindnaextraction_dnaextraction_lmui_fk` (`last_modified_user_id`)',
                'KEY `et_ophindnaextraction_dnaextraction_cui_fk` (`created_user_id`)',
                'KEY `et_ophindnaextraction_dnaextraction_ev_fk` (`event_id`)',
                'KEY `et_ophindnaextraction_dnaextraction_sev_fk` (`sample_event_id`)',
                'CONSTRAINT `et_ophindnaextraction_dnaextraction_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `et_ophindnaextraction_dnaextraction_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `et_ophindnaextraction_dnaextraction_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
                'CONSTRAINT `et_ophindnaextraction_dnaextraction_sev_fk` FOREIGN KEY (`sample_event_id`) REFERENCES `event` (`id`)',
            ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
    }

    public function down()
    {
        $this->dropTable('et_ophindnaextraction_dnaextraction');

        $event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name' => 'OphInDnaextraction'))->queryRow();

        foreach ($this->dbConnection->createCommand()->select('id')->from('event')->where('event_type_id=:event_type_id', array(':event_type_id' => $event_type['id']))->queryAll() as $row) {
            $this->delete('audit', 'event_id='.$row['id']);
            $this->delete('event', 'id='.$row['id']);
        }

        $this->delete('element_type', 'event_type_id='.$event_type['id']);
        $this->delete('event_type', 'id='.$event_type['id']);
    }
}
