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
  (
    SELECT
      mf.id
    FROM openeyes.ref_medication m
      LEFT JOIN drugs2.f_amp_amps amp2 ON amp2.apid COLLATE utf8_general_ci = m.amp_code COLLATE utf8_general_ci
      LEFT JOIN drugs2.f_vmp_drug_form dft ON dft.vpid COLLATE utf8_general_ci = amp2.vpid COLLATE utf8_general_ci
      LEFT JOIN drugs2.f_lookup_form fhit ON fhit.cd COLLATE utf8_general_ci = dft.formcd COLLATE utf8_general_ci
      LEFT JOIN openeyes.ref_medication_form mf ON mf.term COLLATE utf8_general_ci = fhit.desc COLLATE utf8_general_ci
                                                   AND mf.source_type = 'DM+D'
    WHERE m.id = rm.id
    LIMIT 1
  ) AS FormId,
  mr.id AS RouteId
INTO OUTFILE '/tmp/ref_medication_set.csv'
FROM openeyes.ref_medication rm
  LEFT JOIN openeyes.ref_medication_route mr ON mr.id IN (
    SELECT
      mr2.id
    FROM openeyes.ref_medication m2
      LEFT JOIN drugs2.f_amp_amps amp3 ON amp3.apid COLLATE utf8_general_ci = m2.amp_code COLLATE utf8_general_ci
      LEFT JOIN drugs2.f_vmp_drug_route drt ON drt.vpid COLLATE utf8_general_ci = amp3.vpid COLLATE utf8_general_ci
      LEFT JOIN drugs2.f_lookup_route lr ON lr.cd COLLATE utf8_general_ci = drt.routecd COLLATE utf8_general_ci
      LEFT JOIN openeyes.ref_medication_route mr2 ON mr2.term COLLATE utf8_general_ci = lr.desc COLLATE utf8_general_ci
                                                     AND mr2.source_type = 'DM+D'
    WHERE m2.id = rm.id
  )
WHERE ( rm.source_type='DM+D' AND rm.source_subtype != 'VTM');
