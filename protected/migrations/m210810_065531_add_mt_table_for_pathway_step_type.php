<?php

class m210810_065531_add_mt_table_for_pathway_step_type extends OEMTMigration
{
    protected function getLevelStructuredTables(): array
    {
        return array(
            'user' => array(),
            'firm' => array(),
            'site' => array(),
            'subspecialty' => array(),
            'specialty' => array(),
            'institution' => array('pathway_step_type'),
        );
    }
}
