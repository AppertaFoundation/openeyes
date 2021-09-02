<?php

class m200617_105332_add_other_field_to_post_op_et_complications extends OEMigration
{
    public function safeUp()
    {
        $this->addColumn('ophciexamination_postop_et_complications', 'other', 'text');
        $this->addColumn('ophciexamination_postop_et_complications_version', 'other', 'text');
    }

    public function safeDown()
    {
        $this->dropColumn('ophciexamination_postop_et_complications', 'other');
        $this->dropColumn('ophciexamination_postop_et_complications_version', 'other');
    }
}
