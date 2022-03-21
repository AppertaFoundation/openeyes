<?php

class m220215_165200_add_patient_identifiers_view extends OEMigration
{
    public function safeUp()
    {
        $this->execute("CREATE OR REPLACE VIEW v_patient_identifiers AS
SELECT
  n.patient_id
  , i.short_name AS institution_short_name
  , s.short_name AS site_short_name
  , t.short_title AS type_short_title
  , n.value AS identifier_value
  , i.name AS institution_long_name
  , i.remote_id AS institution_code
  , s.name AS site_long_name
  , s.remote_id AS site_code
  , t.long_title AS type_long_title
  , n.patient_identifier_type_id
  , t.usage_type
  , t.institution_id
  , t.site_id
  , n.deleted AS identifier_deleted
FROM patient_identifier AS n
JOIN patient_identifier_type AS t
  ON t.id = n.patient_identifier_type_id
JOIN institution AS i
  ON i.id = t.institution_id
LEFT OUTER JOIN site AS s
  ON s.id = t.site_id
ORDER BY patient_id;
        ");
    }

    public function safeDown()
    {
        echo "Down not supported";
    }
}
