INSERT INTO medication_set_rule (medication_set_id, site_id, subspecialty_id, usage_code) SELECT
  id, NULL, NULL, 'Formulary'
FROM medication_set WHERE `name` IN ('DM+D AMP', 'DM+D VMP', 'DM+D VTM');