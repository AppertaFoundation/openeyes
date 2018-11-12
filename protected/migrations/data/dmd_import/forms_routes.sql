/*------------------------------------------ FORMS ------------------------------------------------------*/
INSERT INTO openeyes.ref_medication_form (`term`,`code`,`unit_term`,`default_dose_unit_term`,`source_type`)
  SELECT DISTINCT
    `desc`,
    cd,
    `desc`,
    `desc`,
    'DM+D' AS source_type
  FROM drugs2.f_lookup_form
;
/*------------------------------------------ ROUTES -----------------------------------------------------*/
INSERT INTO openeyes.ref_medication_route (term,`code`, source_type)
  SELECT
    lr.desc,
    lr.cd,
    'DM+D'
  FROM
    drugs2.f_lookup_route lr
;