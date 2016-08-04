<?php

class m141222_153733_refactor_macro_tables extends OEMigration
{
    public function up()
    {
        $this->alterColumn('ophcocorrespondence_letter_macro', 'site_id', 'int(10) unsigned null');
        $this->alterColumn('ophcocorrespondence_letter_macro_version', 'site_id', 'int(10) unsigned null');

        $this->addColumn('ophcocorrespondence_letter_macro', 'subspecialty_id', 'int(10) unsigned null');
        $this->addColumn('ophcocorrespondence_letter_macro_version', 'subspecialty_id', 'int(10) unsigned null');

        $this->addColumn('ophcocorrespondence_letter_macro', 'firm_id', 'int(10) unsigned null');
        $this->addColumn('ophcocorrespondence_letter_macro_version', 'firm_id', 'int(10) unsigned null');

        foreach ($this->dbConnection->createCommand()->select('*')->from('ophcocorrespondence_subspecialty_letter_macro')->order('id asc')->queryAll() as $slm) {
            $id = $slm['id'];
            unset($slm['id']);

            $this->insert('ophcocorrespondence_letter_macro', $slm);

            $new_id = $this->dbConnection->createCommand()->select('max(id)')->from('ophcocorrespondence_letter_macro')->queryScalar();

            foreach ($this->dbConnection->createCommand()->select('*')->from('ophcocorrespondence_subspecialty_letter_macro_version')->where('id = :id', array(':id' => $id))->order('version_id asc')->queryAll() as $slm_v) {
                $slm_v['id'] = $new_id;
                $this->insert('ophcocorrespondence_letter_macro_version', $slm_v);
            }
        }

        foreach ($this->dbConnection->createCommand()->select('*')->from('ophcocorrespondence_firm_letter_macro')->order('id asc')->queryAll() as $flm) {
            $id = $flm['id'];
            unset($flm['id']);

            $this->insert('ophcocorrespondence_letter_macro', $flm);

            $new_id = $this->dbConnection->createCommand()->select('max(id)')->from('ophcocorrespondence_letter_macro')->queryScalar();

            foreach ($this->dbConnection->createCommand()->select('*')->from('ophcocorrespondence_firm_letter_macro_version')->where('id = :id', array(':id' => $id))->order('version_id asc')->queryAll() as $flm_v) {
                $flm_v['id'] = $new_id;
                $this->insert('ophcocorrespondence_letter_macro_version', $flm_v);
            }
        }

        $this->dropTable('ophcocorrespondence_subspecialty_letter_macro_version');
        $this->dropTable('ophcocorrespondence_subspecialty_letter_macro');

        $this->dropTable('ophcocorrespondence_firm_letter_macro_version');
        $this->dropTable('ophcocorrespondence_firm_letter_macro');
    }

    public function down()
    {
        if ($this->dbConnection->createCommand()->select('*')->from('ophcocorrespondence_letter_macro')->where('(site_id is not null and firm_id is not null) or (site_id is not null and subspecialty_id is not null) or (firm_id is not null and subspecialty_id is not null)')->queryRow()) {
            throw new Exception('Invalid rows in ophcocorrespondence_letter_macro table.');
        }

        $this->createTable('ophcocorrespondence_subspecialty_letter_macro', array(
                'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
                'subspecialty_id' => 'int(10) unsigned NOT NULL',
                'name' => 'varchar(64) not null',
                'use_nickname' => 'tinyint(1) unsigned NOT NULL',
                'body' => 'text COLLATE utf8_unicode_ci',
                'cc_patient' => 'tinyint(1) unsigned NOT NULL',
                'display_order' => 'tinyint(1) unsigned not null',
                'episode_status_id' => 'int(10) unsigned DEFAULT NULL',
                'cc_doctor' => 'tinyint(1) unsigned NOT NULL',
                'cc_drss' => 'tinyint(1) unsigned NOT NULL',
                'recipient_id' => 'int(10) unsigned DEFAULT NULL',
                'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'PRIMARY KEY (`id`)',
                'KEY `ophcocorrespondence_slm2_created_user_id_fk` (`created_user_id`)',
                'KEY `ophcocorrespondence_slm2_last_modified_user_id_fk` (`last_modified_user_id`)',
                'KEY `ophcocorrespondence_slm2_subspecialty_id_fk` (`subspecialty_id`)',
                'KEY `ophcocorrespondence_subspecialty_letter_macro_rcp_fk` (`recipient_id`)',
                'CONSTRAINT `ophcocorrespondence_slm2_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `ophcocorrespondence_slm2_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `ophcocorrespondence_slm2_subspecialty_id_fk` FOREIGN KEY (`subspecialty_id`) REFERENCES `subspecialty` (`id`)',
                'CONSTRAINT `ophcocorrespondence_subspecialty_letter_macro_rcp_fk` FOREIGN KEY (`recipient_id`) REFERENCES `ophcocorrespondence_letter_recipient` (`id`)',
            ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->versionExistingTable('ophcocorrespondence_subspecialty_letter_macro');

        $this->createTable('ophcocorrespondence_firm_letter_macro', array(
                'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
                'firm_id' => 'int(10) unsigned NOT NULL',
                'name' => 'varchar(64) not null',
                'use_nickname' => 'tinyint(1) unsigned NOT NULL',
                'body' => 'text COLLATE utf8_unicode_ci',
                'cc_patient' => 'tinyint(1) unsigned NOT NULL',
                'display_order' => 'tinyint(1) unsigned not null',
                'episode_status_id' => 'int(10) unsigned DEFAULT NULL',
                'cc_doctor' => 'tinyint(1) unsigned NOT NULL',
                'cc_drss' => 'tinyint(1) unsigned NOT NULL',
                'recipient_id' => 'int(10) unsigned DEFAULT NULL',
                'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'PRIMARY KEY (`id`)',
                'KEY `ophcocorrespondence_flm_created_user_id_fk` (`created_user_id`)',
                'KEY `ophcocorrespondence_flm_last_modified_user_id_fk` (`last_modified_user_id`)',
                'KEY `ophcocorrespondence_flm_firm_id_fk` (`firm_id`)',
                'KEY `ophcocorrespondence_firm_letter_macro_rcp_fk` (`recipient_id`)',
                'CONSTRAINT `ophcocorrespondence_flm_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `ophcocorrespondence_flm_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `ophcocorrespondence_flm_firm_id_fk` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`)',
                'CONSTRAINT `ophcocorrespondence_firm_letter_macro_rcp_fk` FOREIGN KEY (`recipient_id`) REFERENCES `ophcocorrespondence_letter_recipient` (`id`)',
            ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->versionExistingTable('ophcocorrespondence_firm_letter_macro');

        foreach ($this->dbConnection->createCommand()->select('*')->from('ophcocorrespondence_letter_macro')->where('subspecialty_id is not null or firm_id is not null')->order('id asc')->queryAll() as $lm) {
            unset($lm['site_id']);
            $id = $lm['id'];
            unset($lm['id']);

            if ($lm['subspecialty_id']) {
                unset($lm['firm_id']);

                $this->insert('ophcocorrespondence_subspecialty_letter_macro', $lm);

                $new_id = $this->dbConnection->createCommand()->select('max(id)')->from('ophcocorrespondence_subspecialty_letter_macro')->queryScalar();

                $to_delete = array();

                foreach ($this->dbConnection->createCommand()->select('*')->from('ophcocorrespondence_letter_macro_version')->where('id = :id', array(':id' => $id))->order('version_id asc')->queryAll() as $lm_v) {
                    $to_delete[] = $lm_v['id'];

                    $lm_v['id'] = $new_id;
                    unset($lm_v['firm_id']);
                    unset($lm_v['site_id']);

                    $this->insert('ophcocorrespondence_subspecialty_letter_macro_version', $lm_v);
                }

                if (!empty($to_delete)) {
                    $this->dbConnection->createCommand('delete from ophcocorrespondence_letter_macro_version where id in ('.implode(',', $to_delete).')')->query();
                }
            }

            if (@$lm['firm_id']) {
                unset($lm['subspecialty_id']);

                $this->insert('ophcocorrespondence_firm_letter_macro', $lm);

                $new_id = $this->dbConnection->createCommand()->select('max(id)')->from('ophcocorrespondence_firm_letter_macro')->queryScalar();

                $to_delete = array();

                foreach ($this->dbConnection->createCommand()->select('*')->from('ophcocorrespondence_letter_macro_version')->where('id = :id', array(':id' => $id))->order('version_id asc')->queryAll() as $lm_v) {
                    $to_delete[] = $lm_v['id'];

                    $lm_v['id'] = $new_id;
                    unset($lm['subspecialty_id']);
                    unset($lm_v['site_id']);

                    $this->insert('ophcocorrespondence_firm_letter_macro_version', $lm_v);
                }

                if (!empty($to_delete)) {
                    $this->dbConnection->createCommand('delete from ophcocorrespondence_letter_macro_version where id in ('.implode(',', $to_delete).')')->query();
                }
            }
        }

        $this->dbConnection->createCommand('delete from ophcocorrespondence_letter_macro where subspecialty_id is not null or firm_id is not null')->query();

        $this->dropColumn('ophcocorrespondence_letter_macro', 'firm_id');
        $this->dropColumn('ophcocorrespondence_letter_macro_version', 'firm_id');

        $this->dropColumn('ophcocorrespondence_letter_macro', 'subspecialty_id');
        $this->dropColumn('ophcocorrespondence_letter_macro_version', 'subspecialty_id');

        $this->alterColumn('ophcocorrespondence_letter_macro', 'site_id', 'int(10) unsigned null');
        $this->alterColumn('ophcocorrespondence_letter_macro_version', 'site_id', 'int(10) unsigned null');
    }
}
