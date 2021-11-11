<?php

class m210810_115137_migrate_risks_from_benfitrisk extends OEMigration
{
    public function safeUp()
    {
        if (!$this->verifyTableExists('et_ophtrconsent_benefitrisk_risk')) {
            return true;
        }

        $this->alterOEColumn('et_ophtrconsent_benfitrisk','risks','mediumtext',true);

        $this->execute("
            UPDATE 
                `event` e
                LEFT JOIN event_type et ON et.id = e.event_type_id
                LEFT JOIN et_ophtrconsent_benfitrisk br1 ON br1.event_id = e.id
            SET br1.risks = CONCAT(
                '<ul>',
                REPLACE(REGEXP_REPLACE(COALESCE(br1.risks,''),'(<ul[^>]*>)',''),'</ul>',''),
                (
                    SELECT 
                        CONCAT(
                            '<li>',
                            COALESCE(GROUP_CONCAT(IF(brr.risk_id=(SELECT id FROM risk ar WHERE ar.name = 'Other') , br.other_risks, r.name) SEPARATOR '</li><li>'),''),
                            '</li>'
                        ) AS `risks_str`
                    FROM `event` e
                        LEFT JOIN event_type et ON et.id = e.event_type_id
                        LEFT JOIN et_ophtrconsent_benfitrisk br ON br.event_id = e.id
                        LEFT JOIN et_ophtrconsent_benefitrisk_risk brr ON brr.element_id = br.id
                        LEFT JOIN risk r ON r.id = brr.risk_id
                    WHERE br.id = br1.id
                    GROUP BY br.id
                ),
                '</ul>'
            )
            WHERE et.class_name = 'OphTrConsent' AND (SELECT COUNT(id) FROM et_ophtrconsent_benefitrisk_risk brr WHERE brr.element_id = br1.id) > 0
            ;
        ");

        // Rename table
        $this->renameOETable('et_ophtrconsent_benefitrisk_risk','archive_et_ophtrconsent_benefitrisk_risk',true);
    }

    public function safeDown()
    {
        return false;
    }
}
