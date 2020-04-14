<?php

class m170118_140823_fix_extracted_by extends CDbMigration
{
    public function up()
    {
        $this->renameColumn('et_ophindnaextraction_dnaextraction', 'extracted_by', 'extracted_by_text');
        $this->renameColumn('et_ophindnaextraction_dnaextraction_version', 'extracted_by', 'extracted_by_text');
        $this->addColumn('et_ophindnaextraction_dnaextraction', 'extracted_by_id', 'int(10) unsigned');
        $this->addColumn('et_ophindnaextraction_dnaextraction_version', 'extracted_by_id', 'int(10) unsigned');
        $this->addForeignKey('et_ophindnaextraction_dnaextraction_ext_usr', 'et_ophindnaextraction_dnaextraction', 'extracted_by_id', 'user', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('et_ophindnaextraction_dnaextraction_ext_usr', 'et_ophindnaextraction_dnaextraction');
        $this->dropColumn('et_ophindnaextraction_dnaextraction_version', 'extracted_by_id');
        $this->dropColumn('et_ophindnaextraction_dnaextraction', 'extracted_by_id');
        $this->renameColumn('et_ophindnaextraction_dnaextraction', 'extracted_by_text', 'extracted_by');
        $this->renameColumn('et_ophindnaextraction_dnaextraction_version', 'extracted_by_text', 'extracted_by');
    }
}
