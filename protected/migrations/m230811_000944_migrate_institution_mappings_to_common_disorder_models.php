<?php

class m230811_000944_migrate_institution_mappings_to_common_disorder_models extends OEMigration
{
    public function safeUp()
    {
        $this->addOEColumn('common_systemic_disorder', 'institution_id', 'int(10) unsigned', true);
        $this->addOEColumn('common_ophthalmic_disorder', 'institution_id', 'int(10) unsigned', true);
        $this->addOEColumn('common_systemic_disorder_group', 'institution_id', 'int(10) unsigned', true);
        $this->addOEColumn('common_ophthalmic_disorder_group', 'institution_id', 'int(10) unsigned', true);
        $this->addOEColumn('secondaryto_common_oph_disorder', 'institution_id', 'int(10) unsigned', true);

        $this->addOEColumn('common_systemic_disorder', 'deleted', 'tinyint(1) unsigned NOT NULL DEFAULT 0', true);
        $this->addOEColumn('common_systemic_disorder_group', 'deleted', 'tinyint(1) unsigned NOT NULL DEFAULT 0', true);
        $this->addOEColumn('common_ophthalmic_disorder', 'deleted', 'tinyint(1) unsigned NOT NULL DEFAULT 0', true);
        $this->addOEColumn('secondaryto_common_oph_disorder', 'deleted', 'tinyint(1) unsigned NOT NULL DEFAULT 0', true);

        // common_ophthalmic_disorder_group already has a deleted column.

        $this->addForeignKey(
            'common_systemic_disorder_i_fk',
            'common_systemic_disorder',
            'institution_id',
            'institution',
            'id'
        );

        $this->addForeignKey(
            'common_systemic_disorder_group_i_fk',
            'common_systemic_disorder_group',
            'institution_id',
            'institution',
            'id'
        );

        $this->addForeignKey(
            'common_ophthalmic_disorder_i_fk',
            'common_ophthalmic_disorder',
            'institution_id',
            'institution',
            'id'
        );

        $this->addForeignKey(
            'common_ophthalmic_disorder_group_i_fk',
            'common_ophthalmic_disorder_group',
            'institution_id',
            'institution',
            'id'
        );

        $this->addForeignKey(
            'secondaryto_common_oph_disorder_i_fk',
            'secondaryto_common_oph_disorder',
            'institution_id',
            'institution',
            'id'
        );

        $this->remapCommonSystemicDisorders();
        $this->remapCommonSystemicDisorderGroups();
        $this->remapCommonOphthalmicDisorders();
        $this->remapCommonOphthalmicDisorderGroups();
        $this->remapSecondaryCommonDiagnoses();
    }

    public function safeDown()
    {
        echo "m230811_000944_migrate_institution_mappings_to_common_disorder_models does not support down migration.\n";
        return false;
    }

    private function remapCommonSystemicDisorders()
    {
        $all_mappings = $this->dbConnection->createCommand()
            ->select('csd.id, COUNT(csdi.id) total_mappings')
            ->from('common_systemic_disorder csd')
            ->leftJoin('common_systemic_disorder_institution csdi', 'csdi.common_systemic_disorder_id = csd.id')
            ->group('csd.id')
            ->queryAll();

        foreach ($all_mappings as $csd) {
            if ((int)$csd['total_mappings'] > 0) {
                $mappings = $this->dbConnection->createCommand()
                    ->select('csd.disorder_id, csd.group_id, csdi.institution_id')
                    ->from('common_systemic_disorder csd')
                    ->join('common_systemic_disorder_institution csdi', 'csdi.common_systemic_disorder_id = csd.id')
                    ->where('csd.id = :id')
                    ->bindValues([':id' => $csd['id']])
                    ->queryAll();
                $this->update(
                    'common_systemic_disorder',
                    ['institution_id' => $mappings[0]['institution_id']],
                    'id = :id',
                    [':id' => $csd['id']]
                );
                array_shift($mappings); // Pop the first mapping from the list to avoid duplicates.

                if (count($mappings) > 0) {
                    $this->insertMultiple(
                        'common_systemic_disorder',
                        $mappings
                    );
                }
            }
        }
        $this->dropOETable('common_systemic_disorder_institution', true);
    }

    private function remapCommonOphthalmicDisorders()
    {
        $all_mappings = $this->dbConnection->createCommand()
            ->select('cod.id, COUNT(codi.id) total_mappings')
            ->from('common_ophthalmic_disorder cod')
            ->leftJoin('common_ophthalmic_disorder_institution codi', 'codi.common_ophthalmic_disorder_id = cod.id')
            ->group('cod.id')
            ->queryAll();

        foreach ($all_mappings as $cod) {
            if ((int)$cod['total_mappings'] > 0) {
                $mappings = $this->dbConnection->createCommand()
                    ->select('cod.disorder_id, cod.subspecialty_id, cod.group_id, cod.alternate_disorder_id, cod.alternate_disorder_label, cod.finding_id, codi.institution_id')
                    ->from('common_ophthalmic_disorder cod')
                    ->join('common_ophthalmic_disorder_institution codi', 'codi.common_ophthalmic_disorder_id = cod.id')
                    ->where('cod.id = :id')
                    ->bindValues([':id' => $cod['id']])
                    ->queryAll();
                $this->update(
                    'common_ophthalmic_disorder',
                    ['institution_id' => $mappings[0]['institution_id']],
                    'id = :id',
                    [':id' => $cod['id']]
                );
                array_shift($mappings); // Pop the first mapping from the list to avoid duplicates.

                if (count($mappings) > 0) {
                    $this->insertMultiple(
                        'common_ophthalmic_disorder',
                        $mappings
                    );
                }
            }
        }
        $this->dropOETable('common_ophthalmic_disorder_institution', true);
    }

    private function remapCommonSystemicDisorderGroups()
    {
        $all_mappings = $this->dbConnection->createCommand()
            ->select('csdg.id, COUNT(csdgi.id) total_mappings')
            ->from('common_systemic_disorder_group csdg')
            ->leftJoin('common_systemic_disorder_group_institution csdgi', 'csdgi.common_systemic_disorder_group_id = csdg.id')
            ->group('csdg.id')
            ->queryAll();

        foreach ($all_mappings as $csdg) {
            if ((int)$csdg['total_mappings'] > 0) {
                $mappings = $this->dbConnection->createCommand()
                    ->select('csdg.name, csdgi.institution_id')
                    ->from('common_systemic_disorder_group csdg')
                    ->join('common_systemic_disorder_group_institution csdgi', 'csdgi.common_systemic_disorder_group_id = csdg.id')
                    ->where('csdg.id = :id')
                    ->bindValues([':id' => $csdg['id']])
                    ->queryAll();
                $this->update(
                    'common_systemic_disorder_group',
                    ['institution_id' => $mappings[0]['institution_id']],
                    'id = :id',
                    [':id' => $csdg['id']]
                );
                array_shift($mappings); // Pop the first mapping from the list to avoid duplicates.
                if (count($mappings) > 0) {
                    foreach ($mappings as $mapping) {
                        $this->insert(
                            'common_systemic_disorder_group',
                            $mapping
                        );

                        $group_id = $this->dbConnection->getLastInsertID();

                        $sql = <<<EOSQL
SELECT csd.id
FROM common_systemic_disorder csd
JOIN common_systemic_disorder_group group_old ON group_old.id = csd.group_id
WHERE group_old.id = :id AND csd.institution_id = :institution_id;
EOSQL;

                        $cds = $this->dbConnection->createCommand($sql)
                            ->bindValues([':id' => $csdg['id'], ':institution_id' => $mapping['institution_id']])
                            ->queryColumn();

                        $this->update(
                            'common_systemic_disorder',
                            ['group_id' => $group_id],
                            ['id IN (' . implode(', ', $cds) . ')']
                        );
                    }
                }
            }
        }
        $this->dropOETable('common_systemic_disorder_group_institution', true);
    }

    private function remapCommonOphthalmicDisorderGroups()
    {
        $all_mappings = $this->dbConnection->createCommand()
            ->select('codg.id, COUNT(codgi.id) total_mappings')
            ->from('common_ophthalmic_disorder_group codg')
            ->leftJoin('common_ophthalmic_disorder_group_institution codgi', 'codgi.common_ophthalmic_disorder_group_id = codg.id')
            ->group('codg.id')
            ->queryAll();

        foreach ($all_mappings as $codg) {
            if ((int)$codg['total_mappings'] > 0) {
                $mappings = $this->dbConnection->createCommand()
                    ->select('codg.name, codg.subspecialty_id, codgi.institution_id')
                    ->from('common_ophthalmic_disorder_group codg')
                    ->join('common_ophthalmic_disorder_group_institution codgi', 'codgi.common_ophthalmic_disorder_group_id = codg.id')
                    ->where('codg.id = :id')
                    ->bindValues([':id' => $codg['id']])
                    ->queryAll();
                $this->update(
                    'common_ophthalmic_disorder_group',
                    ['institution_id' => $mappings[0]['institution_id']],
                    'id = :id',
                    [':id' => $codg['id']]
                );

                array_shift($mappings); // Pop the first mapping from the list to avoid duplicates.
                if (count($mappings) > 0) {
                    foreach ($mappings as $mapping) {
                        $this->insert(
                            'common_ophthalmic_disorder_group',
                            $mapping
                        );

                        $group_id = $this->dbConnection->getLastInsertID();

                        $sql = <<<EOSQL
SELECT cod.id
FROM common_ophthalmic_disorder cod
JOIN common_ophthalmic_disorder_group group_old ON group_old.id = cod.group_id
WHERE group_old.id = :id AND cod.institution_id = :institution_id;
EOSQL;

                        $cds = $this->dbConnection->createCommand($sql)
                            ->bindValues([':id' => $codg['id'], ':institution_id' => $mapping['institution_id']])
                            ->queryColumn();

                        $this->update(
                            'common_ophthalmic_disorder',
                            ['group_id' => $group_id],
                            ['id IN (' . implode(', ', $cds) . ')']
                        );
                    }
                }
            }
        }
        $this->dropOETable('common_ophthalmic_disorder_group_institution', true);
    }

    private function remapSecondaryCommonDiagnoses()
    {
        $all_mappings = $this->dbConnection->createCommand()
            ->select('sd.id, COUNT(sdi.id) total_mappings')
            ->from('secondaryto_common_oph_disorder sd')
            ->leftJoin('secondaryto_common_oph_disorder_institution sdi', 'sdi.secondaryto_common_oph_disorder_id = sd.id')
            ->group('sd.id')
            ->queryAll();

        foreach ($all_mappings as $sd) {
            if ((int)$sd['total_mappings'] > 0) {
                $mappings = $this->dbConnection->createCommand()
                    ->select('sd.disorder_id, sd.parent_id, sd.finding_id, sd.letter_macro_text, sd.subspecialty_id, sdi.institution_id')
                    ->from('secondaryto_common_oph_disorder sd')
                    ->join('secondaryto_common_oph_disorder_institution sdi', 'sdi.secondaryto_common_oph_disorder_id = sd.id')
                    ->where('sd.id = :id')
                    ->bindValues([':id' => $sd['id']])
                    ->queryAll();
                $this->update(
                    'secondaryto_common_oph_disorder',
                    ['institution_id' => $mappings[0]['institution_id']],
                    'id = :id',
                    [':id' => $sd['id']]
                );
                array_shift($mappings); // Pop the first mapping from the list to avoid duplicates.

                if (count($mappings) > 0) {
                    $this->insertMultiple(
                        'secondaryto_common_oph_disorder',
                        $mappings
                    );
                }
            }
        }
        $this->dropOETable('secondaryto_common_oph_disorder_institution', true);
    }
}
