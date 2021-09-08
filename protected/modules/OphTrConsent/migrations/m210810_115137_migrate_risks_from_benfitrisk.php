<?php

class m210810_115137_migrate_risks_from_benfitrisk extends OEMigration
{
    public function safeUp()
    {
        if (!$this->verifyTableExists('et_ophtrconsent_benefitrisk_risk')) {
            return true;
        }

        $this->alterOEColumn('et_ophtrconsent_benfitrisk','risks','mediumtext',true);

        $risk_data = $this->dbConnection->createCommand("
            SELECT
                br.id AS element_id,
                CONCAT('<ul><li>',GROUP_CONCAT(b.name SEPARATOR '</li><li>'),'</li></ul>') AS `risks_str`
            FROM
                event_type et
                LEFT JOIN `event` e ON e.event_type_id = et.id
                LEFT JOIN `et_ophtrconsent_benfitrisk` br ON br.`event_id` = e.id
                LEFT JOIN `et_ophtrconsent_benefitrisk_risk` brr ON brr.`element_id` = br.id
                LEFT JOIN `benefit` b ON b.id = brr.risk_id
            WHERE et.`class_name` = 'OphTrConsent' AND brr.id IS NOT NULL
            GROUP BY br.id;
        ")->queryAll();

        foreach($risk_data as $risk){
            $this->update('et_ophtrconsent_benfitrisk',
                array('risks' => new CDbExpression('CONCAT(risks,"'.addslashes($risk['risks_str']).'")')),
                'id = '.$risk['element_id']
            );
        }
        // Assign table
        $this->dropOETable('et_ophtrconsent_benefitrisk_risk', false);
    }

    public function safeDown()
    {
        return false;
    }
}
