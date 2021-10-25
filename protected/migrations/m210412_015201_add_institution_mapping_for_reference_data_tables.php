<?php

class m210412_015201_add_institution_mapping_for_reference_data_tables extends OEMigration
{
    public function safeUp()
    {
        $this->execute("ALTER TABLE firm ADD COLUMN IF NOT EXISTS institution_id int(10) unsigned");
        $this->execute("ALTER TABLE firm_version ADD COLUMN IF NOT EXISTS institution_id int(10) unsigned");

        $this->execute("ALTER TABLE firm ADD CONSTRAINT firm_institution_fk FOREIGN KEY (institution_id) REFERENCES institution (id)");

        $institution_id = $this->dbConnection->createCommand("SELECT id FROM institution WHERE remote_id = :code")->queryScalar(array(':code' => Yii::app()->params['institution_code']));

        $this->execute("UPDATE firm SET institution_id = :id", array(':id' => $institution_id));

        // Dealing with duplicates before setting constraint
        $duplicate_ids = $this->dbConnection->createCommand("SELECT MAX(id) as id FROM firm GROUP BY name, institution_id, service_subspecialty_assignment_id HAVING COUNT(name)>1")->queryAll();

        foreach ($duplicate_ids as $id) {
            $entry = $this->dbConnection->createCommand()
                ->select('name, service_subspecialty_assignment_id')
                ->from('firm')
                ->where('id=:id', array(':id'=>$id['id']))
                ->queryRow();
            $this->execute("UPDATE firm SET name = CONCAT(name,IF(active=0, CONCAT('_Inactive_', id), CONCAT('_Duplicate_', id))) WHERE id<>:id AND name=:name AND institution_id=:institution_id AND service_subspecialty_assignment_id=:ssaid",
                array(':id' => $id['id'],
                    ':name' => $entry["name"],
                    ':institution_id' => $institution_id,
                    ':ssaid' => $entry["service_subspecialty_assignment_id"]));
        }


        $this->execute("ALTER TABLE firm ADD CONSTRAINT firm_name_institution_service UNIQUE (`name`, institution_id, service_subspecialty_assignment_id)");
    }

    public function safeDown()
    {
        $this->execute("ALTER TABLE firm DROP CONSTRAINT IF EXISTS firm_name_institution_service");

        $this->execute("UPDATE firm set name = REPLACE(name, '_Inactive', '') WHERE name LIKE '%_Inactive'");
        $this->execute("UPDATE firm set name = REPLACE(name, '_Duplicate', '') WHERE name LIKE '%_Duplicate'");

        $this->execute("ALTER TABLE firm DROP FOREIGN KEY IF EXISTS firm_institution_fk");

        $this->execute("ALTER TABLE firm DROP COLUMN IN EXISTS institution_id");
        $this->execute("ALTER TABLE firm_version DROP COLUMN IF EXISTS institution_id");
    }
}
