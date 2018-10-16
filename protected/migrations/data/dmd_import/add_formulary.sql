INSERT INTO ref_set_rules (ref_set_id, site_id, subspecialty_id, usage_code) SELECT
  id, NULL, NULL, 'Formulary'
FROM ref_set WHERE `name` IN ('DM+D AMP', 'DM+D VMP', 'DM+D VTM');