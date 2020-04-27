<?php

class m170908_110912_medication_risk_tag_links extends OEMigration
{
    public function link_tag_to_risk($tag_id, $risk_id)
    {
        $link = $this->getDbConnection()->createCommand()
            ->select('count(*)')
            ->from('ophciexamination_risk_tag')
            ->where('risk_id = :risk_id AND tag_id = :tag_id')
            ->queryScalar(array(':risk_id' => $risk_id, ':tag_id' => $tag_id));
        if (!$link) {
            $this->insert('ophciexamination_risk_tag', array(
                'risk_id' => $risk_id,
                'tag_id' => $tag_id
            ));
        }
    }

    protected $tag_risks = array(
        'Anticoagulant' => 'Anticoagulants',
        'Alphablocker' => 'Alpha blockers'
    );

    public function up()
    {
        foreach ($this->tag_risks as $tag_name => $risk_name) {
            $tag_id = $this->getDbConnection()->createCommand()
                ->select('id')
                ->from('tag')
                ->where(' LOWER(name) LIKE ?')
                ->queryScalar(array($tag_name));
            if (!$tag_id) {
                $this->insert('tag', array(
                    'name' => $tag_name,
                ));
                $tag_id = $this->dbConnection->getLastInsertID();
            }
            $risk_id = $this->getDbConnection()->createCommand()
                ->select('id')
                ->from('ophciexamination_risk')
                ->where('LOWER(name) = ?')
                ->queryScalar(array($risk_name));
            $this->link_tag_to_risk($tag_id, $risk_id);
        }
    }

    public function down()
    {
        echo "m170908_110912_medication_risk_tag_links will go down but doesn't remove data changes.\n";
    }
}
