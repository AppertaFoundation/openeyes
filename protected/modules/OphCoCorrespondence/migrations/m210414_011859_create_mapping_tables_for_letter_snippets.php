<?php

class m210414_011859_create_mapping_tables_for_letter_snippets extends OEMigration
{
    public function safeUp()
    {
        // Institution mapping table
        $this->createOETable(
            'ophcocorrespondence_letter_string_institution',
            [
                'id' => 'pk',
                'letter_string_id' => 'int(10) unsigned NOT NULL',
                'institution_id' => 'int(10) unsigned NOT NULL',
                'CONSTRAINT `ophcocorrespondence_letter_string_i_ls_fk` FOREIGN KEY (`letter_string_id`) REFERENCES `ophcocorrespondence_letter_string` (`id`)',
                'CONSTRAINT `ophcocorrespondence_letter_string_i_fk` FOREIGN KEY (`institution_id`) REFERENCES `institution` (`id`)',
            ], true
        );
        // Site mapping table
        $this->createOETable(
            'ophcocorrespondence_letter_string_site',
            [
                'id' => 'pk',
                'letter_string_id' => 'int(10) unsigned NOT NULL',
                'site_id' => 'int(10) unsigned NOT NULL',
                'CONSTRAINT `ophcocorrespondence_letter_string_s_ls_fk` FOREIGN KEY (`letter_string_id`) REFERENCES `ophcocorrespondence_letter_string` (`id`)',
                'CONSTRAINT `ophcocorrespondence_letter_string_s_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`)',
            ], true
        );

        // Get all existing letter strings.
        $snippets = $this->dbConnection->createCommand()
            ->select('id, site_id')
            ->from('ophcocorrespondence_letter_string')
            ->where('site_id IS NOT NULL')
            ->queryAll();

        $institutions = [];
        foreach ($snippets as $snippet) {
            // Get the institution_id for the site currently assigned to the snippet (if it has one),
            // then assign that to the new column.
            $institution_id = $this->dbConnection->createCommand()
                ->select('institution_id')
                ->from('site')
                ->where('id = :id', array(':id' => $snippet['site_id']))
                ->queryScalar();
            $institutions[] = ['letter_string_id' => $snippet['id'], 'institution_id' => $institution_id];
        }

        if(!empty($institutions)){
            $this->insertMultiple('ophcocorrespondence_letter_string_institution', $institutions);
        }
        if(!empty($snippets))
        $this->insertMultiple('ophcocorrespondence_letter_string_site', array_map(function ($snippet) {
            return [
                'letter_string_id' => $snippet['id'],
                'site_id' => $snippet['site_id']
            ];
        }, $snippets));

        $this->dropForeignKey('ophcocorrespondence_ls2_site_id_fk', 'ophcocorrespondence_letter_string');
        $this->dropOEColumn('ophcocorrespondence_letter_string', 'site_id', true);
    }

    public function safeDown()
    {
        echo "m210414_011859_create_mapping_tables_for_letter_snippets does not support migration down.";
        return false;
    }
}
