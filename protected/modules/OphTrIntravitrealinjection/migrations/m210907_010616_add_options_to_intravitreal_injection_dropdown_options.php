<?php

class m210907_010616_add_options_to_intravitreal_injection_dropdown_options extends OEMigration
{
    protected const postinjection_drops = [
        'G. Chloramphenical 0.5% stat and lubricating drops PRN for 3 days',
        'Lubricating drops PRN for 3 days',
    ];
    protected const antiseptic_drugs = [
        'G. Iodine 5%',
        'G. Chloramphenicol 0.5%',
        'G. Chlorhexidine 0.05%'
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
            $this->insert('ophtrintravitinjection_postinjection_drops',
                ['name' => $postinjection_drop, 'display_order' => ++$display_order, 'active' => 1]);
        }

        // Pre-injection antiseptic
        $display_order = $this->getDbConnection()->createCommand('select max(display_order) from ophtrintravitinjection_antiseptic_drug')->queryScalar();
        foreach (self::antiseptic_drugs as $antiseptic_drug) {
            $this->insert('ophtrintravitinjection_antiseptic_drug',
                ['name' => $antiseptic_drug, 'display_order' => ++$display_order, 'is_default' => 0, 'active' => 1]);
        }

        // Anaesthetic Agents
        $agent_display_order = $this->getDbConnection()->createCommand('select max(display_order) from anaesthetic_agent')->queryScalar();
        $injection_display_order = $this->getDbConnection()->createCommand('select max(display_order) from ophtrintravitinjection_anaestheticagent')->queryScalar();
        foreach (self::anaesthetic_agents as $anaesthetic_agent) {
            $anaesthetic_agent_id = $this->dbConnection->createCommand()->select('id')->from('anaesthetic_agent')->where('name = :name',
                [':name' => $anaesthetic_agent])->queryScalar();
            if (!$anaesthetic_agent_id || $anaesthetic_agent_id === '') {
                $this->insert('anaesthetic_agent',
                    ['name' => $anaesthetic_agent, 'display_order' => ++$agent_display_order, 'active' => 1]);
                $anaesthetic_agent_id = $this->dbConnection->getLastInsertID();
            }
            $this->insert('ophtrintravitinjection_anaestheticagent', [
                'anaesthetic_agent_id' => $anaesthetic_agent_id,
                'display_order' => ++$injection_display_order,
                'is_default' => 0,
            ]);
        }
    }

    public function safeDown()
    {
        // Post-injection drops
        foreach (self::postinjection_drops as $postinjection_drop) {
            $this->delete('ophtrintravitinjection_postinjection_drops', 'name = :name',
                [':name' => $postinjection_drop]);
        }

        // Pre-injection antiseptic
        foreach (self::antiseptic_drugs as $antiseptic_drug) {
            $this->delete('ophtrintravitinjection_antiseptic_drug', 'name = :name', [':name' => $antiseptic_drug]);
        }

        // Anaesthetic Agents
        foreach (self::anaesthetic_agents as $anaesthetic_agent) {
            $id = $this->dbConnection->createCommand()->select('id')->from('anaesthetic_agent')->where('name = :name',
                [':name' => $anaesthetic_agent])->queryScalar();
            $this->delete('ophtrintravitinjection_anaestheticagent', 'anaesthetic_agent_id = :id', [':id' => $id]);
        }
    }
}
