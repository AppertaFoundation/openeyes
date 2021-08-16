<?php

class m210311_230222_add_institution_id_column_to_disorder_admin_tables extends OEMTMigration
{
    protected function getLevelStructuredTables(): array
    {
        return array(
            'user' => array(),
            'firm' => array(),
            'site' => array(),
            'subspecialty' => array(),
            'specialty' => array(),
            'institution' => array(
                'common_ophthalmic_disorder_group',
                'common_ophthalmic_disorder',
                'secondaryto_common_oph_disorder',
                'common_systemic_disorder_group',
                'common_systemic_disorder',
            ),
        );
    }
}
