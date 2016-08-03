<?php

class m140108_084630_simplify_workflow_rules extends CDbMigration
{
    public function up()
    {
        foreach ($this->dbConnection->createCommand()->select('*')->from('ophciexamination_workflow_rule')->queryAll() as $rule) {
            if (!in_array($rule['clause'], array(null, 'subspecialty_id', 'status_id', 'firm_id'))) {
                throw new Exception("Unhandled workflow rule clause: {$rule['clause']}");
            }
        }

        if (count($this->dbConnection->createCommand()->select('*')->from('ophciexamination_workflow_rule')->where('parent_id is null')->queryAll()) > 1) {
            throw new Exception('More than one root node found');
        }

        $this->addColumn('ophciexamination_workflow_rule', 'subspecialty_id', 'int(10) unsigned null');
        $this->addColumn('ophciexamination_workflow_rule', 'firm_id', 'int(10) unsigned null');
        $this->addColumn('ophciexamination_workflow_rule', 'episode_status_id', 'int(10) unsigned null');

        foreach ($this->dbConnection->createCommand()->select('*')->from('ophciexamination_workflow_rule')->where('parent_id is not null')->order('id asc')->queryAll() as $rule) {
            $this->update('ophciexamination_workflow_rule', array(
                'subspecialty_id' => $this->getValueForClause($rule, 'subspecialty_id'),
                'firm_id' => $this->getValueForClause($rule, 'firm_id'),
                'episode_status_id' => $this->getValueForClause($rule, 'status_id'),
            ), "id = {$rule['id']}");
        }

        $this->addForeignKey('ophciexamination_workflow_rule_subspecialty_id_fk', 'ophciexamination_workflow_rule', 'subspecialty_id', 'subspecialty', 'id');
        $this->addForeignKey('ophciexamination_workflow_rule_firm_id_fk', 'ophciexamination_workflow_rule', 'firm_id', 'firm', 'id');
        $this->addForeignKey('ophciexamination_workflow_rule_episode_status_id_fk', 'ophciexamination_workflow_rule', 'episode_status_id', 'episode_status', 'id');

        $this->dropForeignKey('ophciexamination_workflow_rule_parent_id_fk', 'ophciexamination_workflow_rule');
        $this->dropIndex('ophciexamination_workflow_rule_parent_id_fk', 'ophciexamination_workflow_rule');
        $this->dropColumn('ophciexamination_workflow_rule', 'parent_id');

        $this->dropColumn('ophciexamination_workflow_rule', 'clause');
        $this->dropColumn('ophciexamination_workflow_rule', 'value');
    }

    public function getValueForClause($rule, $clause)
    {
        $parent = $this->dbConnection->createCommand()->select('*')->from('ophciexamination_workflow_rule')->where('id = ?', array($rule['parent_id']))->queryRow();

        $value = null;

        while ($parent) {
            if ($parent['clause'] == $clause) {
                $value = $rule['value'];
                break;
            }

            $rule = $parent;
            $parent = $this->dbConnection->createCommand()->select('*')->from('ophciexamination_workflow_rule')->where('id = ?', array($rule['parent_id']))->queryRow();
        }

        return $value;
    }

    public function down()
    {
        if (count($this->dbConnection->createCommand()->select('*')->from('ophciexamination_workflow_rule')->where('subspecialty_id is null and episode_status_id is null and firm_id is null')->queryAll()) > 1) {
            throw new Exception('More than one root node found');
        }

        $this->addColumn('ophciexamination_workflow_rule', 'parent_id', 'int(10) unsigned null');
        $this->addColumn('ophciexamination_workflow_rule', 'clause', 'varchar(255) DEFAULT NULL');
        $this->addColumn('ophciexamination_workflow_rule', 'value', 'varchar(255) DEFAULT NULL');

        $this->dropForeignKey('ophciexamination_workflow_rule_episode_status_id_fk', 'ophciexamination_workflow_rule');
        $this->dropForeignKey('ophciexamination_workflow_rule_firm_id_fk', 'ophciexamination_workflow_rule');
        $this->dropForeignKey('ophciexamination_workflow_rule_subspecialty_id_fk', 'ophciexamination_workflow_rule');

        $this->dropIndex('ophciexamination_workflow_rule_episode_status_id_fk', 'ophciexamination_workflow_rule');
        $this->dropIndex('ophciexamination_workflow_rule_firm_id_fk', 'ophciexamination_workflow_rule');
        $this->dropIndex('ophciexamination_workflow_rule_subspecialty_id_fk', 'ophciexamination_workflow_rule');

        $root_node = $this->dbConnection->createCommand()->select('*')->from('ophciexamination_workflow_rule')->where('subspecialty_id is null and episode_status_id is null and firm_id is null')->queryRow();

        $this->update('ophciexamination_workflow_rule', array('clause' => 'subspecialty_id'), "id = {$root_node['id']}");

        foreach ($this->dbConnection->createCommand()->select('*')->from('ophciexamination_workflow_rule')->where('subspecialty_id is not null or episode_status_id is not null or firm_id is not null')->order('id asc')->queryAll() as $rule) {
            $clause = null;

            if ($rule['episode_status_id'] === null && $rule['firm_id'] === null) {
                if ($this->dbConnection->createCommand()->select('*')->from('ophciexamination_workflow_rule')->where('episode_status_id is not null and subspecialty_id = :subspecialty_id', array(':subspecialty_id' => $rule['subspecialty_id']))->queryRow()) {
                    $clause = 'status_id';
                }

                $parent_id = $root_node['id'];
                $value = $rule['subspecialty_id'];
            } elseif ($rule['episode_status_id'] !== null && $rule['firm_id'] === null) {
                if ($this->dbConnection->createCommand()->select('*')->from('ophciexamination_workflow_rule')->where('firm_id is not null and subspecialty_id = :subspecialty_id and episode_status_id = :episode_status_id', array(':subspecialty_id' => $rule['subspecialty_id'], ':episode_status_id' => $rule['episode_status_id']))->queryRow()) {
                    $clause = 'firm_id';
                }

                $parent = $this->dbConnection->createCommand()->select('*')->from('ophciexamination_workflow_rule')->where('subspecialty_id = :subspecialty_id and episode_status_id is null and firm_id is null', array(':subspecialty_id' => $rule['subspecialty_id']))->queryRow();
                $parent_id = $parent['id'];
                $value = $rule['episode_status_id'];
            } elseif ($rule['episode_status_id'] !== null && $rule['firm_id'] !== null) {
                $parent = $this->dbConnection->createCommand()->select('*')->from('ophciexamination_workflow_rule')->where('subspecialty_id = :subspecialty_id and episode_status_id = :episode_status_id and firm_id is null', array(':subspecialty_id' => $rule['subspecialty_id'], ':episode_status_id' => $rule['episode_status_id']))->queryRow();
                $parent_id = $parent['id'];
                $value = $rule['firm_id'];
            }

            $this->update('ophciexamination_workflow_rule', array('parent_id' => $parent_id, 'clause' => $clause, 'value' => $value), "id = {$rule['id']}");
        }

        $this->dropColumn('ophciexamination_workflow_rule', 'subspecialty_id');
        $this->dropColumn('ophciexamination_workflow_rule', 'firm_id');
        $this->dropColumn('ophciexamination_workflow_rule', 'episode_status_id');

        $this->addForeignKey('ophciexamination_workflow_rule_parent_id_fk', 'ophciexamination_workflow_rule', 'parent_id', 'ophciexamination_workflow_rule', 'id');
    }
}
