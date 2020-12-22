<?php

class m190919_014602_add_more_import_statuses extends CDbMigration
{
    public function up()
    {
        $builder = $this->getDbConnection()->schema->commandBuilder;
        $builder->createMultipleInsertCommand(
            'import_status',
            array(
                array('id' => 12, 'status_value' => 'Import Trial Success'),
                array('id' => 13, 'status_value' => 'Import Trial Patient Success')
            )
        )->execute();
    }

    public function down()
    {
        $this->delete('import_status', 'id=12 AND status_value="Import Trial Success"');
        $this->delete('import_status', 'id=13 AND status_value="Import Trial Patient Success"');
    }
}
