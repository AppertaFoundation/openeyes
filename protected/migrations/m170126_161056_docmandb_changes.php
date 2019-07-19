<?php

class m170126_161056_docmandb_changes extends CDbMigration
{
    public function up()
    {
        //Make requested changes to column names
        $this->renameColumn('document_log', 'event_date', 'letter_created_date' );
        $this->renameColumn('document_log', 'event_updated', 'letter_finalised_date' );
        $this->renameColumn('document_log', 'output_date', 'letter_sent_date' );
        // Add new column
        $this->addColumn('document_log', 'last_significant_event_date', 'DATETIME');
    }


    public function down()
    {
        $this->renameColumn('document_log', 'letter_created_date', 'event_date' );
        $this->renameColumn('document_log', 'letter_finalised_date', 'event_updated' );
        $this->renameColumn('document_log', 'letter_sent_date', 'output_date' );
        // Add new column
        $this->dropColumn('document_log', 'last_significant_event_date');
    }

}