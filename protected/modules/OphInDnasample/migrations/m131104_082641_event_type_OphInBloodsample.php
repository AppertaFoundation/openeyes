<?php

class m131104_082641_event_type_OphInBloodsample extends OEMigration
{
    public function up()
    {
        if (!$this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name' => 'OphInBloodsample'))->queryRow()) {
            $group = $this->dbConnection->createCommand()->select('id')->from('event_group')->where('name=:name', array(':name' => 'Investigation events'))->queryRow();
            $this->insert('event_type', array('class_name' => 'OphInBloodsample', 'name' => 'DNA sample', 'event_group_id' => $group['id']));
        }

        $event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name' => 'OphInBloodsample'))->queryRow();

        if (!$this->dbConnection->createCommand()->select('id')->from('element_type')->where('name=:name and event_type_id=:eventTypeId', array(':name' => 'Sample', ':eventTypeId' => $event_type['id']))->queryRow()) {
            $this->insert('element_type', array('name' => 'Sample', 'class_name' => 'Element_OphInBloodsample_Sample', 'event_type_id' => $event_type['id'], 'display_order' => 1));
        }

        $element_type = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('event_type_id=:eventTypeId and name=:name', array(':eventTypeId' => $event_type['id'], ':name' => 'Sample'))->queryRow();

        $this->createTable('ophinbloodsample_sample_type', array(
                'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
                'name' => 'varchar(128) NOT NULL',
                'display_order' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'PRIMARY KEY (`id`)',
                'KEY `ophinbloodsample_sample_type_lmui_fk` (`last_modified_user_id`)',
                'KEY `ophinbloodsample_sample_type_cui_fk` (`created_user_id`)',
                'CONSTRAINT `ophinbloodsample_sample_type_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `ophinbloodsample_sample_type_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
            ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->insert('ophinbloodsample_sample_type', array('id' => 1, 'name' => 'Blood', 'display_order' => 1));
        $this->insert('ophinbloodsample_sample_type', array('id' => 2, 'name' => 'Buccal', 'display_order' => 2));
        $this->insert('ophinbloodsample_sample_type', array('id' => 3, 'name' => 'DNA', 'display_order' => 3));
        $this->insert('ophinbloodsample_sample_type', array('id' => 4, 'name' => 'Other', 'display_order' => 4));
        $this->insert('ophinbloodsample_sample_type', array('id' => 5, 'name' => 'RNA', 'display_order' => 5));
        $this->insert('ophinbloodsample_sample_type', array('id' => 6, 'name' => 'Sputum', 'display_order' => 6));
        $this->insert('ophinbloodsample_sample_type', array('id' => 7, 'name' => 'Serum', 'display_order' => 7));

        $this->createTable('et_ophinbloodsample_sample', array(
                'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
                'event_id' => 'int(10) unsigned NOT NULL',
                'old_dna_no' => 'int(10) unsigned',
                'blood_date' => 'date DEFAULT NULL',
                'comments' => 'text',
                'type_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'volume' => 'float NOT NULL',
                'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'PRIMARY KEY (`id`)',
                'KEY `et_ophinbloodsample_sample_lmui_fk` (`last_modified_user_id`)',
                'KEY `et_ophinbloodsample_sample_cui_fk` (`created_user_id`)',
                'KEY `et_ophinbloodsample_sample_ev_fk` (`event_id`)',
                'KEY `ophinbloodsample_sample_type_fk` (`type_id`)',
                'CONSTRAINT `et_ophinbloodsample_sample_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `et_ophinbloodsample_sample_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `et_ophinbloodsample_sample_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
                'CONSTRAINT `ophinbloodsample_sample_type_fk` FOREIGN KEY (`type_id`) REFERENCES `ophinbloodsample_sample_type` (`id`)',
            ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->versionExistingTable('et_ophinbloodsample_sample');
        $this->versionExistingTable('ophinbloodsample_sample_type');
    }

    public function down()
    {
        $this->dropTable('et_ophinbloodsample_sample');
        $this->dropTable('ophinbloodsample_sample_type');

        $event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name' => 'OphInBloodsample'))->queryRow();

        foreach ($this->dbConnection->createCommand()->select('id')->from('event')->where('event_type_id=:event_type_id', array(':event_type_id' => $event_type['id']))->queryAll() as $row) {
            $this->delete('audit', 'event_id='.$row['id']);
            $this->delete('event', 'id='.$row['id']);
        }

        $this->delete('element_type', 'event_type_id='.$event_type['id']);
        $this->delete('event_type', 'id='.$event_type['id']);

        $this->dropTable('et_ophinbloodsample_sample_version');
        $this->dropTable('ophinbloodsample_sample_type_version');

        echo "If you are removing this module you may also need to remove references to it in your configuration files\n";
    }
}
