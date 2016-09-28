<?php

class m140211_145626_fix_table_name extends CDbMigration
{
    public function up()
    {
        $this->dropForeignKey('et_ophcotherapya_exceptional_intervention_lmui_fk', 'et_ophcotherapya_exceptional_intervention');
        $this->dropForeignKey('et_ophcotherapya_exceptional_intervention_cui_fk', 'et_ophcotherapya_exceptional_intervention');

        $this->dropIndex('et_ophcotherapya_exceptional_intervention_lmui_fk', 'et_ophcotherapya_exceptional_intervention');
        $this->dropIndex('et_ophcotherapya_exceptional_intervention_cui_fk', 'et_ophcotherapya_exceptional_intervention');

        $this->renameTable('et_ophcotherapya_exceptional_intervention', 'ophcotherapya_exceptional_intervention');

        $this->addForeignKey('ophcotherapya_exceptional_intervention_cui_fk', 'ophcotherapya_exceptional_intervention', 'created_user_id', 'user', 'id');
        $this->addForeignKey('ophcotherapya_exceptional_intervention_lmui_fk', 'ophcotherapya_exceptional_intervention', 'last_modified_user_id', 'user', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('ophcotherapya_exceptional_intervention_lmui_fk', 'ophcotherapya_exceptional_intervention');
        $this->dropForeignKey('ophcotherapya_exceptional_intervention_cui_fk', 'ophcotherapya_exceptional_intervention');

        $this->dropIndex('ophcotherapya_exceptional_intervention_lmui_fk', 'ophcotherapya_exceptional_intervention');
        $this->dropIndex('ophcotherapya_exceptional_intervention_cui_fk', 'ophcotherapya_exceptional_intervention');

        $this->renameTable('ophcotherapya_exceptional_intervention', 'et_ophcotherapya_exceptional_intervention');

        $this->addForeignKey('et_ophcotherapya_exceptional_intervention_cui_fk', 'et_ophcotherapya_exceptional_intervention', 'created_user_id', 'user', 'id');
        $this->addForeignKey('et_ophcotherapya_exceptional_intervention_lmui_fk', 'et_ophcotherapya_exceptional_intervention', 'last_modified_user_id', 'user', 'id');
    }
}
