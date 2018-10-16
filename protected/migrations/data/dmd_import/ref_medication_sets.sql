SELECT id INTO @amp_id FROM ref_set WHERE `name` = 'DM+D AMP';
SELECT id INTO @vmp_id FROM ref_set WHERE `name` = 'DM+D VMP';
SELECT id INTO @vtm_id FROM ref_set WHERE `name` = 'DM+D VTM';

SELECT
  rm.id,
  CASE rm.source_subtype
    WHEN 'AMP' THEN @amp_id
    WHEN 'VMP' THEN @vmp_id
    WHEN 'VTM' THEN @vtm_id
  END AS setId,
  mf.id AS formId,
  mr.id AS RouteId
INTO OUTFILE '/tmp/ref_medication_set.csv'
FROM openeyes.ref_medication rm

  LEFT JOIN drugs2.f_amp_amps amp2 ON amp2.apid = rm.amp_code
  LEFT JOIN drugs2.f_vmp_drug_form dft ON dft.vpid = amp2.vpid
  LEFT JOIN drugs2.f_lookup_form fhit ON fhit.cd = dft.formcd
  LEFT JOIN openeyes.ref_medication_form mf ON mf.term = fhit.desc AND mf.source_type = 'DM+D'

  LEFT JOIN drugs2.f_vmp_drug_route drt ON drt.vpid = amp2.vpid
  LEFT JOIN drugs2.f_lookup_route lr ON lr.cd = drt.routecd
  LEFT JOIN openeyes.ref_medication_route mr ON mr.term COLLATE utf8_general_ci = lr.desc AND mr.source_type = 'DM+D'

WHERE
  rm.source_type='DM+D';
 /* AND
  rm.source_subtype != 'VTM' */

INSERT INTO ref_set_rules (ref_set_id, site_id, subspecialty_id, usage_code) VALUES
  (@amp_id, NULL, NULL, 'Formulary'),
  (@vmp_id, NULL, NULL, 'Formulary'),
  (@vtm_id, NULL, NULL, 'Formulary');