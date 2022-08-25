<?php

class m220822_083000_convert_contact_new_format extends OEMigration
{
    public function safeUp()
    {
        $this->dbConnection->createCommand("
        INSERT INTO address (contact_id, address_type_id , address1, address2, city, postcode, county, country_id)
        SELECT c.id AS 'contact_id', a.address_type_id, s.name AS address1, IF(a.address2 IS NULL OR a.address2='', a.address1, CONCAT(a.address1 , ', ', a.address2)) AS address2 , a.city, a.postcode , a.county , a.country_id
        FROM contact c 
            INNER JOIN contact_location cl ON cl.contact_id = c.id
            INNER JOIN site s ON s.id = cl.site_id 
            INNER JOIN address a ON a.contact_id = s.contact_id 
        WHERE c.id NOT IN (SELECT contact_id from address)
            AND a.date_end is NULL
        GROUP BY c.id
        HAVING MAX(a.last_modified_date)
        ")->execute();

        $this->dbConnection->createCommand("
        UPDATE patient_contact_assignment pca
        JOIN contact_location cl ON cl.id = pca.location_id
        SET
            pca.contact_id = cl.contact_id,
            pca.location_id = NULL
        WHERE pca.contact_id IS NULL AND pca.location_id IS NOT NULL;")->execute();
    }

    public function down()
    {
        echo "m220822_083000_convert_contact_new_format does not support migration down.\n";
        return false;
    }
}
