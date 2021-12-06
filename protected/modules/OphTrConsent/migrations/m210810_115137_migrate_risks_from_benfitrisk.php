<?php

class m210810_115137_migrate_risks_from_benfitrisk extends OEMigration
{
    public function safeUp()
    {
        if (!$this->verifyTableExists('et_ophtrconsent_benefitrisk_risk')) {
            return true;
        }

        $this->alterOEColumn('et_ophtrconsent_benfitrisk', 'risks', 'mediumtext', true);

        $risk_data = $this->dbConnection->createCommand("
            SELECT
				br.id AS element_id,
				REPLACE(REGEXP_REPLACE(COALESCE(risks,''),'(<ul[^>]*>)',''),'</ul>','') AS risks,
				CONCAT(
					'<li>',
						COALESCE(GROUP_CONCAT(IF(brr.risk_id=(SELECT id FROM risk ar WHERE ar.name = 'Other') , br.other_risks, r.name) SEPARATOR '</li><li>'),''),
					'</li>'
				) AS `risks_str`
            FROM et_ophtrconsent_benfitrisk br
                LEFT JOIN et_ophtrconsent_benefitrisk_risk brr ON brr.element_id = br.id
                LEFT JOIN risk r ON r.id = brr.risk_id
            WHERE brr.id IS NOT NULL
            GROUP BY br.id;
        ")->queryAll();

        foreach ($risk_data as $risk) {
            $old_risks = $risk['risks'];

            if (!str_starts_with($old_risks, "<li>")) {
                $parts = explode(',', $old_risks);
                $old_risks = '<li>' . implode('</li><li>', $parts) . '</li>';
            }
            $new_risks = addslashes('<ul>' . $old_risks . str_replace("<li></li>", "", $risk['risks_str']) . '</ul>');
            $new_risks = str_replace("<li></li>", "", $new_risks);

            $this->update(
                'et_ophtrconsent_benfitrisk',
                array('risks' => $new_risks),
                'id = ' . $risk['element_id']
            );
        }

        // Rename table
        $this->renameOETable('et_ophtrconsent_benefitrisk_risk', 'archive_et_ophtrconsent_benefitrisk_risk', true);
    }

    public function safeDown()
    {
        return false;
    }
}
