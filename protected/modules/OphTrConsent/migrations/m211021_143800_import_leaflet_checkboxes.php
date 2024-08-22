<?php

class m211021_143800_import_leaflet_checkboxes extends OEMigration
{
    private const OTHER_ET_TBL = 'et_ophtrconsent_other';
    private const LEAFLETS_ET_TBL = 'et_ophtrconsent_leaflets';

    public function safeUp()
    {
        if (
            ($this->dbConnection->schema->getTable(self::OTHER_ET_TBL, true)) &&
            ($this->dbConnection->schema->getTable(self::OTHER_ET_TBL, true))
        ) {
            $this->addOEColumn(self::LEAFLETS_ET_TBL, 'information', 'tinyint(1) unsigned NULL', true);
            $this->addOEColumn(self::LEAFLETS_ET_TBL, 'anaesthetic_leaflet', 'tinyint(1) unsigned NULL', true);

            $this->execute("
                UPDATE " . self::LEAFLETS_ET_TBL . " as lf
                SET
                lf.information = (
                    SELECT information FROM " . self::OTHER_ET_TBL . " WHERE event_id = lf.event_id
                ),
                lf.anaesthetic_leaflet = (
                    SELECT anaesthetic_leaflet FROM " . self::OTHER_ET_TBL . " WHERE event_id = lf.event_id
                )
            ");
        }
    }

    public function safeDown()
    {
        $this->dropOEColumn(self::LEAFLETS_ET_TBL, 'anaesthetic_leaflet', true);
        $this->dropOEColumn(self::LEAFLETS_ET_TBL, 'information', true);
        return false;
    }
}
