<?php

class m210701_120323_remove_unnecessary_high_volume_tables extends OEMigration
{
    private $proc_hve_factor_assign = 'proc_high_volume_exclusion_factor_assign';
    private $proc_hve_factor_section = 'proc_high_volume_exclusion_factor_section';
    private $proc_hve_factor_criteria = 'proc_high_volume_exclusion_factor_criteria';

    private function getFK($fkey, $table)
    {
        $sql = "SELECT CONSTRAINT_NAME, TABLE_NAME
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                WHERE REFERENCED_TABLE_SCHEMA = (SELECT DATABASE())
                AND TABLE_NAME = '".$table."'
                AND CONSTRAINT_NAME = '".$fkey."';";

        return $this->dbConnection->createCommand($sql)->queryRow();
    }

    private function dropForeignKeyIfExist($fkey, $table)
    {
        if ($foreign_key = $this->getFK($fkey, $table)) {
            return $this->dropForeignKey($foreign_key['CONSTRAINT_NAME'], $foreign_key['TABLE_NAME']);
        }

        return 0;
    }

    public function up()
    {
        $this->dropForeignKeyIfExist(
            $this->proc_hve_factor_criteria.'_section_fk',
            $this->proc_hve_factor_criteria
        );

        $this->dropForeignKeyIfExist(
            $this->proc_hve_factor_assign.'_criteria_fk',
            $this->proc_hve_factor_assign
        );

        $this->dbConnection->createCommand('DROP TABLE IF EXISTS `'.$this->proc_hve_factor_section.'`;')->execute();
        $this->dbConnection->createCommand('DROP TABLE IF EXISTS `'.$this->proc_hve_factor_assign.'`;')->execute();
        $this->dbConnection->createCommand('DROP TABLE IF EXISTS `'.$this->proc_hve_factor_criteria.'`;')->execute();
    }

    public function down()
    {
        $this->createOETable(
            $this->proc_hve_factor_section,
            array(
                'id' => 'pk',
                'procedure_id' => 'int(10) unsigned not null',
                'name' => 'varchar(200) not null',
                'display_order' => 'int(10) unsigned not null default 1',
            ),
            false
        );

        $this->createOETable(
            $this->proc_hve_factor_criteria,
            array(
                'id' => 'pk',
                'section_id' => 'int(11) not null',
                'name' => 'varchar(200) not null',
                'display_order' => 'int(10) unsigned not null default 1',
            ),
            false
        );

        $this->createOETable(
            $this->proc_hve_factor_assign,
            array(
                'id' => 'pk',
                'procedure_id' => 'int(10) unsigned not null',
                'criteria_id'  => 'int(11) not null',
                'display_order' => 'int(10) unsigned not null default 1',
            ),
            false
        );

        $this->addForeignKey(
            $this->proc_hve_factor_assign.'_criteria_fk',
            $this->proc_hve_factor_assign,
            'criteria_id',
            $this->proc_hve_factor_criteria,
            'id'
        );

        $this->addForeignKey(
            $this->proc_hve_factor_criteria.'_section_fk',
            $this->proc_hve_factor_criteria,
            'section_id',
            $this->proc_hve_factor_section,
            'id'
        );
    }
}
