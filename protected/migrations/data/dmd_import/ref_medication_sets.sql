SELECT id INTO @amp_id FROM medication_set WHERE `name` = 'DM+D AMP';
SELECT id INTO @vmp_id FROM medication_set WHERE `name` = 'DM+D VMP';
SELECT id INTO @vtm_id FROM medication_set WHERE `name` = 'DM+D VTM';

SELECT
  rm.id,
  CASE rm.source_subtype
    WHEN 'AMP' THEN @amp_id
    WHEN 'VMP' THEN @vmp_id
    WHEN 'VTM' THEN @vtm_id
  END AS setId,
  NULL AS formId,
  mr.id AS RouteId
INTO OUTFILE '/tmp/medication_set.csv'
FROM medication rm

  LEFT JOIN {prefix}amp_amps amp2 ON amp2.apid = rm.amp_code
  LEFT JOIN {prefix}vmp_drug_form dft ON dft.vpid = amp2.vpid
  LEFT JOIN {prefix}lookup_form fhit ON fhit.cd = dft.formcd
  LEFT JOIN (SELECT term FROM medication_form WHERE medication_form.source_type = 'DM+D') as mf ON mf.term = fhit.desc

  LEFT JOIN {prefix}vmp_drug_route drt ON drt.vpid = amp2.vpid
  LEFT JOIN {prefix}lookup_route lr ON lr.cd = drt.routecd
  LEFT JOIN (
        SELECT medication_route.id, medication_route.term
        FROM medication_route
        WHERE medication_route.source_type = 'DM+D'
    ) as mr
    ON mr.term COLLATE utf8_general_ci = lr.desc

WHERE
  rm.source_type='DM+D';
