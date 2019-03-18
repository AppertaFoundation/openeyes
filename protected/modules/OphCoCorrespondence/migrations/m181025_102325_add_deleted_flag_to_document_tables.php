<?php

class m181025_102325_add_deleted_flag_to_document_tables extends CDbMigration
{
    public function up()
    {
        $this->addColumn('document_set', 'deleted', "tinyint(1) unsigned NOT NULL DEFAULT '0'");
        $this->addColumn('document_set_version', 'deleted', "tinyint(1) unsigned NOT NULL DEFAULT '0'");

        $this->addColumn('document_instance', 'deleted', "tinyint(1) unsigned NOT NULL DEFAULT '0'");
        $this->addColumn('document_instance_version', 'deleted', "tinyint(1) unsigned NOT NULL DEFAULT '0'");

        $this->addColumn('document_target', 'deleted', "tinyint(1) unsigned NOT NULL DEFAULT '0'");
        $this->addColumn('document_target_version', 'deleted', "tinyint(1) unsigned NOT NULL DEFAULT '0'");

        $this->addColumn('document_instance_data', 'deleted', "tinyint(1) unsigned NOT NULL DEFAULT '0'");
        $this->addColumn('document_instance_data_version', 'deleted', "tinyint(1) unsigned NOT NULL DEFAULT '0'");

        $this->addColumn('document_output', 'deleted', "tinyint(1) unsigned NOT NULL DEFAULT '0'");
        $this->addColumn('document_output_version', 'deleted', "tinyint(1) unsigned NOT NULL DEFAULT '0'");

        // loop over all the events to check if they are deleted
        // get all correspondence where the status is not COMPLETED - completed document cannot be deleted
        $criteria = new CDbCriteria();
        $criteria->join = "JOIN document_target dt ON t.document_target_id = dt.id ";
        $criteria->join .= "JOIN document_instance di ON dt.document_instance_id = di.id ";
        $criteria->join .= "JOIN event ON di.correspondence_event_id = event.id";
        $criteria->addCondition('event.deleted = 1 AND output_status != "COMPLETE"');

        $dataProvider = new CActiveDataProvider('DocumentOutput');
        $dataProvider->setCriteria($criteria);
        $iterator = new CDataProviderIterator($dataProvider);

        foreach ($iterator as $document_output) {
            $event = $document_output->getEvent();
            $letter = ElementLetter::model()->findByAttributes(['event_id' => $event->id]);

            if ($letter && $event->deleted) {
                $letter->markDocumentRelationTreeDeleted();
            }
        }
    }

    public function down()
    {
        $this->dropColumn('document_set', 'deleted');
        $this->dropColumn('document_set_version', 'deleted');
        $this->dropColumn('document_instance', 'deleted');
        $this->dropColumn('document_instance_version', 'deleted');
        $this->dropColumn('document_target', 'deleted');
        $this->dropColumn('document_target_version', 'deleted');
        $this->dropColumn('document_instance_data', 'deleted');
        $this->dropColumn('document_instance_data_version', 'deleted');
        $this->dropColumn('document_output', 'deleted');
        $this->dropColumn('document_output_version', 'deleted');
    }
}