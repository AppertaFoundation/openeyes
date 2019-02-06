/*------------------------------------------ FORMS ------------------------------------------------------*/
INSERT INTO medication_form (`term`,`code`,`unit_term`,`default_dose_unit_term`,`source_type`)
  SELECT DISTINCT
    `desc`,
    cd,
    `desc`,
    `desc`,
    'DM+D' AS source_type
  FROM {prefix}lookup_form
;
/*------------------------------------------ ROUTES -----------------------------------------------------*/
INSERT INTO medication_route (term,`code`, source_type)
  SELECT
    lr.desc,
    lr.cd,
    'DM+D'
  FROM
    {prefix}lookup_route lr
;