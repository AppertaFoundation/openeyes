<?php

class m210923_045837_consolidate_intravitreal_injection_options extends OEMigration
{
    protected const postinjection_drops = [
        'G. Chloramphenical 0.5% stat and lubricating drops PRN for 3 days',
        'Lubricating drops PRN for 3 days',
    ];
    protected const antiseptic_drugs = [
        'G. Iodine 5%',
        'G. Chloramphenicol 0.5%',
        'G. Chlorhexidine 0.05%',
    ];
    protected const anaesthetic_agents = [
        'Tetracaine 1%',
        'G. Proxymetacaine 0.5%',
        'Lidocaine 2%',
        'Tetracaine 1% and G. Proxymetacaine 0.5%',
        'Lidocaine 2%, Tetracaine 1% and G. Proxymetacaine 0.5%',
    ];

    public function safeUp()
    {
        // Post-injection drops
        $display_order = $this->getDbConnection()->createCommand('select max(display_order) from ophtrintravitinjection_postinjection_drops')->queryScalar();
        foreach (self::postinjection_drops as $postinjection_drop) {
            $postinjection_drop_id = $this->dbConnection->createCommand()
                ->select('id')
                ->from('ophtrintravitinjection_postinjection_drops')
                ->where('name = :name', [':name' => $postinjection_drop])
                ->queryScalar();
            if (!$postinjection_drop_id || $postinjection_drop_id === '') {
                $this->insert('ophtrintravitinjection_postinjection_drops',
                    [
                        'name' => $postinjection_drop,
                        'display_order' => ++$display_order,
                        'active' => 1
                    ]);
            }
        }

        // Pre-injection antiseptic
        $display_order = $this->getDbConnection()->createCommand('select max(display_order) from ophtrintravitinjection_antiseptic_drug')->queryScalar();
        foreach (self::antiseptic_drugs as $antiseptic_drug) {
            $antiseptic_drug_id = $this->dbConnection->createCommand()
                ->select('id')
                ->from('ophtrintravitinjection_antiseptic_drug')
                ->where('name = :name', [':name' => $antiseptic_drug])
                ->queryScalar();
            if (!$antiseptic_drug_id || $antiseptic_drug_id === '') {
                $this->insert('ophtrintravitinjection_antiseptic_drug',
                    [
                        'name' => $antiseptic_drug,
                        'display_order' => ++$display_order,
                        'is_default' => 0,
                        'active' => 1,
                    ]);
            }
        }

        // Anaesthetic Agents
        $agent_display_order = $this->getDbConnection()->createCommand('select max(display_order) from anaesthetic_agent')->queryScalar();
        $injection_display_order = $this->getDbConnection()->createCommand('select max(display_order) from ophtrintravitinjection_anaestheticagent')->queryScalar();
        foreach (self::anaesthetic_agents as $anaesthetic_agent) {
            $anaesthetic_agent_id = $this->dbConnection->createCommand()
                ->select('id')
                ->from('anaesthetic_agent')
                ->where('name = :name', [':name' => $anaesthetic_agent])
                ->queryScalar();
            if (!$anaesthetic_agent_id || $anaesthetic_agent_id === '') {
                $this->insert('anaesthetic_agent',
                    [
                        'name' => $anaesthetic_agent,
                        'display_order' => ++$agent_display_order,
                        'active' => 1
                    ]);
                $anaesthetic_agent_id = $this->dbConnection->getLastInsertID();
            }
            $intravitrealinjection_anaesthetic_agent_id = $this->dbConnection->createCommand()
                ->select('id')
                ->from('ophtrintravitinjection_anaestheticagent')
                ->where('anaesthetic_agent_id = :agent_id', [':agent_id' => $anaesthetic_agent_id])
                ->queryScalar();
            if (!$intravitrealinjection_anaesthetic_agent_id || $intravitrealinjection_anaesthetic_agent_id === '') {
                $this->insert('ophtrintravitinjection_anaestheticagent', [
                    'anaesthetic_agent_id' => $anaesthetic_agent_id,
                    'display_order' => ++$injection_display_order,
                    'is_default' => 0,
                ]);
            }
        }
    }

    public function safeDown()
    {
        echo "m210923_045837_consolidate_intravitreal_injection_options does not support migration down.\n";
        return true;
    }
}
