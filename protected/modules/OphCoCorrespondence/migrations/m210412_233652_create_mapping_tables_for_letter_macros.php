<?php

class m210412_233652_create_mapping_tables_for_letter_macros extends OEMigration
{
    public function safeUp()
    {
        // Institution mapping table
        $this->createOETable(
            'ophcocorrespondence_letter_macro_institution',
            [
                'id' => 'pk',
                'letter_macro_id' => 'int(10) unsigned NOT NULL',
                'institution_id' => 'int(10) unsigned NOT NULL',
                'CONSTRAINT `ophcocorrespondence_letter_macro_i_lm_fk` FOREIGN KEY (`letter_macro_id`) REFERENCES `ophcocorrespondence_letter_macro` (`id`)',
                'CONSTRAINT `ophcocorrespondence_letter_macro_i_fk` FOREIGN KEY (`institution_id`) REFERENCES `institution` (`id`)',
            ], true
        );
        // Site mapping table
        $this->createOETable(
            'ophcocorrespondence_letter_macro_site',
            [
                'id' => 'pk',
                'letter_macro_id' => 'int(10) unsigned NOT NULL',
                'site_id' => 'int(10) unsigned NOT NULL',
                'CONSTRAINT `ophcocorrespondence_letter_macro_s_lm_fk` FOREIGN KEY (`letter_macro_id`) REFERENCES `ophcocorrespondence_letter_macro` (`id`)',
                'CONSTRAINT `ophcocorrespondence_letter_macro_s_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`)',
            ], true
        );
        // Firm mapping table
        $this->createOETable(
            'ophcocorrespondence_letter_macro_firm',
            [
                'id' => 'pk',
                'letter_macro_id' => 'int(10) unsigned NOT NULL',
                'firm_id' => 'int(10) unsigned NOT NULL',
                'CONSTRAINT `ophcocorrespondence_letter_macro_f_lm_fk` FOREIGN KEY (`letter_macro_id`) REFERENCES `ophcocorrespondence_letter_macro` (`id`)',
                'CONSTRAINT `ophcocorrespondence_letter_macro_f_fk` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`)',
            ], true
        );
        // Subspecialty mapping table
        $this->createOETable(
            'ophcocorrespondence_letter_macro_subspecialty',
            [
                'id' => 'pk',
                'letter_macro_id' => 'int(10) unsigned NOT NULL',
                'subspecialty_id' => 'int(10) unsigned NOT NULL',
                'CONSTRAINT `ophcocorrespondence_letter_macro_ss_lm_fk` FOREIGN KEY (`letter_macro_id`) REFERENCES `ophcocorrespondence_letter_macro` (`id`)',
                'CONSTRAINT `ophcocorrespondence_letter_macro_ss_fk` FOREIGN KEY (`subspecialty_id`) REFERENCES `subspecialty` (`id`)',
            ], true
        );

        // Create mappings for existing macros
        $site_macros = $this->dbConnection->createCommand()
            ->select('id, site_id')
            ->from('ophcocorrespondence_letter_macro')
            ->where('site_id IS NOT NULL')
            ->queryAll();
        $firm_macros = $this->dbConnection->createCommand()
            ->select('id, firm_id')
            ->from('ophcocorrespondence_letter_macro')
            ->where('firm_id IS NOT NULL')
            ->queryAll();
        $subspecialty_macros = $this->dbConnection->createCommand()
            ->select('id, subspecialty_id')
            ->from('ophcocorrespondence_letter_macro')
            ->where('subspecialty_id IS NOT NULL AND subspecialty_id != 0')
            ->queryAll();

        $site_institutions = [];
        foreach ($site_macros as $macro) {
            $institution_id = $this->dbConnection->createCommand()
                ->select('institution_id')
                ->from('site')
                ->where('id = :id', array(':id' => $macro['site_id']))
                ->queryScalar();
            $site_institutions[] = ['letter_macro_id' => $macro['id'], 'institution_id' => $institution_id];
        }

        $firm_institutions = [];
        foreach ($firm_macros as $macro) {
            $institution_id = $this->dbConnection->createCommand()
                ->select('institution_id')
                ->from('firm')
                ->where('id = :id', array(':id' => $macro['firm_id']))
                ->queryScalar();
            $firm_institutions[] = ['letter_macro_id' => $macro['id'], 'institution_id' => $institution_id];
        }

        $institution_mappings = array_unique(array_merge($site_institutions, $firm_institutions), SORT_REGULAR);
        if(!empty($institution_mappings)){
            $this->insertMultiple(
                'ophcocorrespondence_letter_macro_institution',
                $institution_mappings,
            );
        }
        if(!empty($site_macros)){
            $this->insertMultiple('ophcocorrespondence_letter_macro_site', array_map(function ($macros) {
                return [
                    'letter_macro_id' => $macros['id'],
                    'site_id' => $macros['site_id']
                ];
            }, $site_macros));
        }
        if(!empty($firm_macros)){
            $this->insertMultiple('ophcocorrespondence_letter_macro_firm', array_map(function ($macros) {
                return [
                    'letter_macro_id' => $macros['id'],
                    'firm_id' => $macros['firm_id']
                ];
            }, $firm_macros));
        }
        if(!empty($subspecialty_macros)){
            $this->insertMultiple('ophcocorrespondence_letter_macro_subspecialty', array_map(function ($macros) {
                return [
                    'letter_macro_id' => $macros['id'],
                    'subspecialty_id' => $macros['subspecialty_id']
                ];
            }, $subspecialty_macros));
        }

        $this->dropForeignKey('ophcocorrespondence_lm_site_id_fk', 'ophcocorrespondence_letter_macro');
        $this->dropOEColumn('ophcocorrespondence_letter_macro', 'site_id', true);
        $this->dropOEColumn('ophcocorrespondence_letter_macro', 'firm_id', true);
        $this->dropOEColumn('ophcocorrespondence_letter_macro', 'subspecialty_id', true);
    }

    public function safeDown()
    {
        echo "m210412_233652_create_mapping_tables_for_letter_macros does not support migration down.";
        return false;
    }
}
