<?php

class m210621_055928_rename_ripcord_to_Intraluminal_stent extends OEMigration
{
    public function safeUp()
    {
        $this->renameOEColumn('et_ophtroperationnote_revision_aqueous', 'ripcord_suture_id', 'intraluminal_stent_id', true);
    }

    public function down()
    {
        $this->renameOEColumn('et_ophtroperationnote_revision_aqueous', 'intraluminal_stent_id', 'ripcord_suture_id', true);
    }
}
