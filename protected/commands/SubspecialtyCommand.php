<?php

class SubspecialtyCommand extends CConsoleCommand
{
    /**
     * @param name The name of the subspecialty to add
     * @param ref_spec The 2 letter code used to refrence the subspecialty in, e.g, event lists
     * @param shot_name The short / abbreviated form of the name [Optional - defaults to same as name]
     */
    public function actionAdd($name, $ref_spec, $short_name = null, $specialty = null)
    {
        // default short_name to match full name if short_name was not supplied
        if (empty($short_name)) {
            $short_name = $name;
        }

        if (empty($specialty)) {
            $specialty = 'Ophthalmology';
        }

        // This could be done as a transaction and a long line of yii commands, but keeping it simple...
        Yii::app()->db->createCommand("
-- Create a new index for the subspecialy to ensure uniqueness
CREATE UNIQUE INDEX IF NOT EXISTS subspecialty_name_IDX USING BTREE ON subspecialty (name,specialty_id);

-- add the subspecialty
INSERT INTO subspecialty (`name`, short_name, ref_spec, specialty_id)
VALUES 
	(:name, :short_name, :ref_spec, (SELECT id FROM specialty WHERE `name` = 'Ophthalmology'))
ON DUPLICATE KEY UPDATE 
	short_name = :short_name,
	ref_spec = :ref_spec;

SELECT @tosub := id FROM subspecialty WHERE `name` = :name AND specialty_id = (SELECT id FROM specialty WHERE `name` = :specialty); 

-- Create a new index for the service to ensure uniqueness
CREATE UNIQUE INDEX IF NOT EXISTS service_name_IDX USING BTREE ON service (name);

-- Add the service
INSERT IGNORE INTO service (`name`)
VALUES (:name);

SELECT @toserv := id FROM service WHERE `name` = :name;

-- Create a new index for the service_subspecialty_assignment to ensure uniqueness
CREATE UNIQUE INDEX IF NOT EXISTS service_subspecialty_IDX USING BTREE ON service_subspecialty_assignment (service_id, subspecialty_id);
INSERT IGNORE INTO service_subspecialty_assignment (service_id, subspecialty_id)
VALUES (@toserv, @tosub);

")->execute([':name' => $name, ':short_name' => $short_name, ':ref_spec' => $ref_spec, ':specialty' => $specialty]);
    }
}
