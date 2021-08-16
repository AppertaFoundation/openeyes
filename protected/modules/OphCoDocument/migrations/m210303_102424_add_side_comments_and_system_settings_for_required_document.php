<?php

class m210303_102424_add_side_comments_and_system_settings_for_required_document extends OEMigration
{
    public function safeUp()
    {
        $this->addOEColumn('et_ophcodocument_document', 'left_comment', 'text', true);
        $this->addOEColumn('et_ophcodocument_document', 'right_comment', 'text', true);

        $sided_documents = $this->dbConnection->createCommand(
            'SELECT id, comment FROM et_ophcodocument_document WHERE single_document_id IS NULL AND comment IS NOT NULL AND comment != "";'
        )->query();

        foreach ($sided_documents as $document) {
            $this->update('et_ophcodocument_document', ['left_comment' => $document['comment'], 'right_comment' => $document['comment'], 'comment' => null], 'id=:id', [':id' => $document['id']]);
        }

        $this->renameColumn('et_ophcodocument_document', 'comment', 'single_comment');
        $this->renameColumn('et_ophcodocument_document_version', 'comment', 'single_comment');

        $setting_field_type_id = $this->dbConnection->createCommand()
            ->select('id')
            ->from('setting_field_type')
            ->where('name = :name', array(':name' => 'Radio buttons'))
            ->queryScalar();

        $this->insert('setting_metadata', array(
            'field_type_id' => $setting_field_type_id,
            'key' => 'document_file_upload_mandatory',
            'name' => 'Document - File Upload Mandatory',
            'data' => serialize(array('on'=>'On', 'off'=>'Off')),
            'default_value' => 'on',
        ));
        $this->insert('setting_installation', array(
            'key' => 'document_file_upload_mandatory',
            'value' => 'on'
        ));
    }

    public function safeDown()
    {
        $sided_documents = $this->dbConnection->createCommand(
            'SELECT id, left_comment FROM et_ophcodocument_document WHERE single_document_id IS NULL AND left_comment IS NOT NULL;'
        )->query();

        foreach ($sided_documents as $document) {
            $this->update('et_ophcodocument_document', ['single_comment' => $document->left_comment], 'id=:id', [':id' => $document->id]);
        }

        $this->dropOEColumn('et_ophcodocument_document', 'left_comment');
        $this->dropOEColumn('et_ophcodocument_document', 'right_comment');
        $this->renameColumn('et_ophcodocument_document', 'single_comment', 'comment');

        $this->delete('setting_metadata', '`key`="document_file_upload_mandatory"');
        $this->delete('setting_installation', '`key`="document_file_upload_mandatory"');
    }
}
