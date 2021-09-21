<?php

class m210719_053116_add_mt_tables_for_pathway_wait_times extends OEMTMigration
{
    protected function getLevelStructuredTables(): array
    {
        return array(
            'user' => array(),
            'firm' => array('worklist_wait_time'),
            'site' => array('worklist_wait_time'),
            'subspecialty' => array('worklist_wait_time'),
            'specialty' => array('worklist_wait_time'),
            'institution' => array(),
        );
    }
}
